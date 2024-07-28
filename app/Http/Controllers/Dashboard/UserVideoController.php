<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\RequestVideo\RequestVideoCreateEvent;
use App\Events\RequestVideo\RequestVideoDoneEvent;
use App\Events\RequestVideo\RequestVideoMelihatEvent;
use App\Events\VideoUser\VideoDoneAdminEvent;
use App\Events\VideoUser\VideoNotifikasiEvent;
use App\Events\VideoUser\VideoRequestEvent;
use App\Helpers\HitungLamaWaktuMenonton;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\VideoResource;
use App\Models\Materi\Materi;
use App\Models\RequestVideo\RequestVideo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class UserVideoController extends Controller
{
    public function index()
    {
        return view('menu.user.index');
    }

    public function getData()
    {
        try {
            $data = Materi::with(['videoRequests' => function ($query) {
                $query->where('user_id', auth()->user()->id)->where('status', '!=', 'done');
            }])->get();

            $data =
                $this->prepareMateriData($data);
            return response()->json(['message' => true, 'data' => VideoResource::collection($data), 'message' => 'Sucsess Get Data Materi'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }
    public function getDataQuery($query)
    {
        try {
            $data = Materi::where('title', 'like', "%$query%")
                ->with(['videoRequests' => function ($query) {
                    $query->where('user_id', auth()->user()->id);
                }])
                ->get();

            $data = $this->prepareMateriData($data);
            return response()->json(['message' => true, 'data' => VideoResource::collection($data), 'message' => 'Sucsess Get Data Materi'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public function requestVideo(Request $request)
    {
        $requestData = RequestVideo::create([
            'user_id' => $request->id_user,
            'video_material_id' => $request->id_materi,
            'kode_request' => 'RQV' . date('dmy') . rand(1000, 9999),
        ]);
        event(new RequestVideoCreateEvent(RequestVideoController::formatRequestVideoDataForDatatable($requestData)));
        event(new VideoRequestEvent($requestData));
        event(new VideoNotifikasiEvent(auth()->user()->name . ' Meminta Izin Melihat Video'));
        return response()->json(['status' => true, 'data' => new VideoResource($requestData), 'message' => 'Request Video created successfully'], 200);
    }

    public function prepareMateriData($data)
    {
        return $data->map(function ($materi) {
            $userRequest = $materi->videoRequests->first();

            $materi->access = match ($userRequest?->status) {
                'approved' => 'Cek Video',
                'pending' => 'Pending',
                default => 'Minta Request',
            };
            $materi->url = match ($userRequest?->status) {
                'approved' => '/user-video/lihat/' . $materi->id,
                'pending' => '',
                default => 'user-video/request?id_user=' . auth()->user()->id . '&id_video=' . $materi->id,
            };
            $materi->thumbnail = Storage::disk('materi')->url($materi->thumbnail);
            return $materi;
        });
    }

    public function lihatVideo($id)
    {
        $materi = RequestVideo::where('user_id', auth()->user()->id)->where('video_material_id', $id)->first();
        if ($materi == null) {
            return redirect('/user-video');
        } else {
            return view('menu.user.lihat', ['id' => $id]);
        }
    }

    public function show($id)
    {
        try {
            $cacheKey = 'materi_data_' . auth()->user()->id . '_' . $id;
            $userRequest = RequestVideo::where('user_id', auth()->user()->id)
                ->where('video_material_id', $id)
                ->where('status', 'sedang melihat')
                ->latest()->first();

            $materi =
                Materi::with(['videoRequests' => function ($query) use ($id) {
                    $query->where('user_id', auth()->user()->id)->where('status', 'sedang melihat')->where('video_material_id', $id);
                },])->find($id);
            if ($userRequest) {
                $materi->expires_at = $materi->videoRequests[0]->expires_at;
                $materi->expired_at = Carbon::parse($materi->videoRequests[0]->expired_at)->timestamp * 1000;
                $materi->status = $materi->videoRequests[0]->status;
                $materiData = $materi;
            } else {
                $dataRequestVideo = RequestVideo::where('user_id', auth()->user()->id)->where('video_material_id', $id)->where('status', 'approved')->latest()->first();
                if (!$dataRequestVideo) {
                    return response()->json(['message' => 'Materi not found'], 404);
                }
                $dataRequestVideo->status = 'sedang melihat';
                $dataRequestVideo->expired_at = Carbon::now()->addMinutes((int) $dataRequestVideo->expires_at);
                $dataRequestVideo->save();
                event(new RequestVideoMelihatEvent(RequestVideoController::formatRequestVideoDataForDatatable($dataRequestVideo)));
                $materiData = Cache::remember($cacheKey, (int) $materi->expires_at, function () use ($id) {
                    $materi = Materi::with(['videoRequests' => function ($query) use ($id) {
                        $query->where('user_id', auth()->user()->id)->where('video_material_id', $id)->where('status', 'sedang melihat');
                    }])->find($id);
                    $materi->expires_at = $materi->videoRequests[0]->expires_at;
                    $materi->expired_at = Carbon::parse($materi->videoRequests[0]->expired_at)->timestamp * 1000;
                    return $materi;
                });
            }

            if (!$materiData) {
                return response()->json(['message' => 'Materi not found'], 404);
            }

            return response()->json(['message' => true, 'data' => $materiData, 'message' => 'Sucsess Get Data Materi'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public function clearCache($id)
    {
        $cacheKey = 'materi_data_' . auth()->user()->id . '_' . $id;
        Cache::forget($cacheKey);
        $materi = RequestVideo::where('user_id', auth()->user()->id)->where('video_material_id', $id)->where('status', 'sedang melihat')->first();
        $expiresAt = HitungLamaWaktuMenonton::hitungLamaWaktuMenonton($materi->expired_at, $materi->expires_at);
        $materi->lama_menonton = $expiresAt;
        $materi->status = 'done';
        $materi->save();
        event(new VideoNotifikasiEvent(auth()->user()->name . ' Telah Selesai Melihat Video'));
        event(new RequestVideoDoneEvent(RequestVideoController::formatRequestVideoDataForDatatable($materi)));
        return response()->json(['message' => 'Cache cleared successfully']);
    }
}
