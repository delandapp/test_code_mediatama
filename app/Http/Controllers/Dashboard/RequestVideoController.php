<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\RequestVideo\RequestVideoApproveEvent;
use App\Events\RequestVideo\RequestVideoCancelEvent;
use App\Events\RequestVideo\RequestVideoDeleteEvent;
use App\Events\RequestVideo\RequestVideoDoneEvent;
use App\Events\VideoUser\VideoApproveEvent;
use App\Events\VideoUser\VideoCancelEvent;
use App\Events\VideoUser\VideoDoneAdminEvent;
use App\Events\VideoUser\VideoRejectedEvent;
use App\Helpers\EncryptionHelper;
use App\Helpers\HitungLamaWaktuMenonton;
use App\Http\Controllers\Controller;
use App\Models\RequestVideo\RequestVideo;
use App\Models\RequestVideo\RequestVideoData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Throwable;

class RequestVideoController extends Controller
{
    public function index()
    {
        return view('menu.request-video.index');
    }

    public function getRequestVideo(Request $request)
    {
        $requestVideo = new RequestVideoData();
        $list = [];
        $data = $requestVideo->get_datarequestvideo($request);
        foreach ($data as $item) {
            $data = $this->formatRequestVideoDataForDatatable($item);
            $list[] = $data;
        }

        $output = [
            "draw" => $request->input('draw'),
            "recordsTotal" => $requestVideo->count_all(),
            "recordsFiltered" => $requestVideo->count_filtered($request),
            "data" => $list,
        ];

        return response()->json($output);
    }

    public function destroy($id)
    {
        $id = EncryptionHelper::decrypt_custom($id);
        $requestData = RequestVideo::find($id);
        if ($requestData == null) {
            return response()->json(['status' => false, 'message' => 'Request not found'], 404);
        }
        event(new RequestVideoDeleteEvent($this->formatRequestVideoDataForDatatable($requestData)));
        event(new VideoRejectedEvent($requestData));
        $requestData->delete();
        return response()->json(['status' => true, 'message' => 'Request deleted successfully'], 200);
    }

    public function approveRequestVideo(Request $request, $id)
    {
        try {
            $id_requestVideo = EncryptionHelper::decrypt_custom($id);
            $requestVideo = RequestVideo::find($id_requestVideo);
            if ($requestVideo == null) {
                return response()->json(['status' => false, 'message' => 'RequestVideo not found'], 404);
            }

            if ($requestVideo->status == 'pending') {
                $requestVideo->status = 'approved';
                $requestVideo->expires_at = $request['waktu'];
                $requestVideo->approved_at = Carbon::now()->locale('id');
                $requestVideo->save();
                event(new VideoApproveEvent($requestVideo));
                event(new RequestVideoApproveEvent($this->formatRequestVideoDataForDatatable($requestVideo)));
                return response()->json(['status' => true, 'message' => 'RequestVideo approved successfully'], 200);
            }
        } catch (Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cancelRequestVideo($id)
    {
        try {
            $id_requestVideo = EncryptionHelper::decrypt_custom($id);
            $requestVideo = RequestVideo::find($id_requestVideo);
            if ($requestVideo == null) {
                return response()->json(['status' => false, 'message' => 'RequestVideo not found'], 404);
            }

            if ($requestVideo->status == 'approved') {
                $requestVideo->status = 'pending';
                $requestVideo->expires_at = null;
                $requestVideo->approved_at = null;
                $requestVideo->save();
                event(new RequestVideoCancelEvent($this->formatRequestVideoDataForDatatable($requestVideo)));
                event(new VideoCancelEvent($requestVideo));
                return response()->json(['status' => true, 'message' => 'RequestVideo cancel successfully'], 200);
            }
        } catch (Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function hentikanMenonton($idUser, $idMateri)
    {
        try {
            $id_user = EncryptionHelper::decrypt_custom($idUser);
            $id_materi = EncryptionHelper::decrypt_custom($idMateri);
            $cacheKey = 'materi_data_' . $id_user . '_' . $id_materi;
            Cache::forget($cacheKey);
            $materi = RequestVideo::where('user_id', $id_user)->where('video_material_id', $id_materi)->where('status', 'sedang melihat')->first();
            $expiresAt = HitungLamaWaktuMenonton::hitungLamaWaktuMenonton($materi->expired_at, $materi->expires_at);
            $materi->lama_menonton = $expiresAt;
            $materi->status = 'done';
            $materi->save();
            event(new RequestVideoDoneEvent(RequestVideoController::formatRequestVideoDataForDatatable($materi)));
            event(new VideoDoneAdminEvent($id_user));
            return response()->json(['message' => 'Cache cleared successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public static function formatRequestVideoDataForDatatable($requestvideo)
    {
        $data = [];
        $data[] = null;
        $data[] = $requestvideo->kode_request;
        $data[] = $requestvideo->materis->title;
        $data[] = "<div class='flex flex-col'> <p class='font-bold text-sm'>" . $requestvideo->custommers->name . "</p> <p class='text-sm'>" . $requestvideo->custommers->email . "</p> </div>";
        $data[] = Carbon::parse($requestvideo->created_at)->format('d-m-Y');
        $data[] = $requestvideo->status == 'pending' ? "<span class='pending text-sm font-medium rounded dark:bg-yellow-900 dark:text-purple-300'>" . $requestvideo->status . "</span>" : "<span class='approve text-sm font-medium rounded dark:bg-yellow-900 dark:text-purple-300'>" . $requestvideo->status . "</span>";
        $data[] = $requestvideo->expires_at ? $requestvideo->expires_at . ' Menit' : '-';
        $data[] = $requestvideo->lama_menonton ? $requestvideo->lama_menonton : '-';
        $data[] = $requestvideo->approved_at ? Carbon::parse($requestvideo->approved_at)->format('d-m-Y') : '-';
        if ((Auth::user()->can('approve-video') || Auth::user()->can('cancel-video')) && $requestvideo->status != 'done') {
            $data[] = null;
        }
        if (Auth::user()->can('approve-video') && $requestvideo->status == 'pending') {
            $data[count($data) - 1] .= "<a href='request/approve/" . EncryptionHelper::encrypt_custom($requestvideo->id) . "' class='inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-green-400 hover:bg-green-700 focus:ring-4 focus:ring-green-300 dark:bg-green-400 dark:hover:bg-green-700 dark:focus:ring-green-800' id='approveModalBtn'><i class='fa-solid fa-circle-check'></i></a>";
        }
        if (Auth::user()->can('cancel-video') && $requestvideo->status == 'approved') {
            $data[count($data) - 1]
                .= "<a href='request/cancel/" . EncryptionHelper::encrypt_custom($requestvideo->id) . "' class='inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900' id='cancelModalBtn'><i class='fa-solid fa-ban'></i></a>";
        }
        if (Auth::user()->can('hapus-video') && $requestvideo->status == 'sedang melihat') {
            $data[count($data) - 1] .=
                "<a href='request/hentikan/" . EncryptionHelper::encrypt_custom($requestvideo->user_id) . "/" . EncryptionHelper::encrypt_custom($requestvideo->video_material_id) . "' class='inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900' id='btnSelesai'>Hentikan</a>";
        }
        if (Auth::user()->can('edit-video') || Auth::user()->can('hapus-video') && $requestvideo->status == 'pending') {
            $data[] = null;
        }
        // if (Auth::user()->can('edit-video') && $requestvideo->status == 'pending') {
        //     $data[count($data) - 1] .=
        //         "<a href='request/edit/" . EncryptionHelper::encrypt_custom($requestvideo->id) . "' class='mr-2 inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800' id='editModalBtn'>Edit</a>";
        // }

        if (Auth::user()->can('hapus-video') && $requestvideo->status == 'pending' || $requestvideo->status == 'done') {
            $data[count($data) - 1] .=
                "<a href='request/delete/" . EncryptionHelper::encrypt_custom($requestvideo->id) . "' class='inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900' id='deleteModalBtn'>Delete</a>";
        }
        return $data;
    }
}
