<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\Materi\MateriCreateEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Materi\MateriCreateRequest;
use App\Models\Materi\Materi;
use App\Models\Materi\MateriData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MateriController extends Controller
{
    public function index()
    {
        return view('menu.video.index');
    }

    public function getVideo(Request $request)
    {
        $customer = new MateriData();
        $list = [];
        $data = $customer->get_datavideo($request);
        foreach ($data as $item) {
            $data = $this->formatVideoDataForDatatable($item);
            $list[] = $data;
        }

        $output = [
            "draw" => $request->input('draw'),
            "recordsTotal" => $customer->count_all(),
            "recordsFiltered" => $customer->count_filtered($request),
            "data" => $list,
        ];

        return response()->json($output);
    }

    public function create(MateriCreateRequest $request)
    {
        try {
            $data = $request->validated();
            $materi = Materi::create($data);
            if ($request->hasFile('thumbnail')) {
                $imageName = 'thumbnail' . '-' . $data['title']. '-' . $materi->created_at . '.' . $request->profile->extension();
                $path = Storage::disk('thumbnail_materi')->putFileAs('', $request->file('thumbnail'), $imageName);
                $materi->update([
                    'thumbnail' => $imageName
                ]);
            } else {
                $materi->update([
                    'thumbnail' => 'default-thumbnail.png'
                ]);
            }
            $imageNameVideo = 'materi' . '-' . $data['title']. '-' . $materi->created_at . '.' . $request->profile->extension();
            $path = Storage::disk('video_materi')->putFileAs('', $request->file('thumbnail'), $imageNameVideo);


            event(new MateriCreateEvent($this->formatVideoDataForDatatable($materi)));

            return response()->json(['status' => true, 'data' => $this->formatVideoDataForDatatable($materi), 'message' => 'Materi created successfully'], 200);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();

            return response()->json([
                'errors' => $errors
            ], 422);
        }
    }

    private function formatVideoDataForDatatable($video)
    {
        $data = [];
        $data[] = null;
        $data[] = $video->title;
        $data[] = $video->video_url;
        $data[] = $video->thumbnail_url;
        return $data;
    }
}
