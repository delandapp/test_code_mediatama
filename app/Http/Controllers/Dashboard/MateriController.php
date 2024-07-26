<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\Materi\MateriCreateEvent;
use App\Events\Materi\MateriDeleteEvent;
use App\Events\Materi\MateriEditEvent;
use App\Helpers\EncryptionHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Materi\MateriCreateRequest;
use App\Http\Requests\Materi\MateriEditRequest;
use App\Models\Materi\Materi;
use App\Models\Materi\MateriData;
use Illuminate\Support\Facades\Auth;
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
            $data['id_user'] = Auth::user()->id;
            $data['kode_materi'] = "K-" . $data['id_user'] . substr($data['title'], 0, 3) . rand(1000, 9999);
            $materi = Materi::create($data);
            $pathVideo = Storage::disk('materi')->putFile('video', $request->file('video'));
            $materi->update([
                'video' => $pathVideo
            ]);
            if ($request->hasFile('thumbnail')) {
                $path = Storage::disk('materi')->putFile('thumbnail', $request->file('thumbnail'));
                $materi->update([
                    'thumbnail' => $path,
                ]);
            } else {
                $materi->update([
                    'thumbnail' => '/thumbnail/default-thumbnail.png',
                ]);
            }

            event(new MateriCreateEvent($this->formatVideoDataForDatatable($materi)));

            return response()->json(['status' => true, 'data' => $this->formatVideoDataForDatatable($materi), 'message' => 'Materi created successfully'], 200);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();

            return response()->json([
                'errors' => $errors
            ], 422);
        }
    }

    public function show($id)
    {
        $id = EncryptionHelper::decrypt_custom($id);
        $user = Materi::find($id);
        if ($user == null) {
            return response()->json(['status' => false, 'message' => 'Materi not found'], 404);
        }

        return response()->json(['status' => true, 'data' => $user], 200);
    }

    public function edit(MateriEditRequest $request, $id)
    {
        try {
            $id = EncryptionHelper::decrypt_custom($id);
            $data = $request->validated();
            $materi = Materi::find($id);
            if ($materi == null) {
                return response()->json(['status' => false, 'message' => 'Materi not found'], 404);
            }

            if ($request->hasFile('thumbnail') && $materi['thumbnail'] != 'default-thumbnail.png' || $request['thumbnailImage'] == "hapus") {
                if ($materi->thumbnail) {
                    Storage::disk('materi')->delete($materi->thumbnail);
                }
            }
            if ($request->hasFile('video')) {
                if ($materi->video) {
                    Storage::disk('materi')->delete($materi->video);
                }
            }

            if ($request->hasFile('thumbnail')) {
                $path = Storage::disk('materi')->putFile('thumbnail', $request->file('thumbnail'));
                $data['thumbnail'] = $path;
            } else if ($request['thumbnailImage'] == "keep") {
                $data['thumbnail'] = $materi['thumbnail'];
            } else {
                $materi->update([
                    'thumbnail' => 'thumbnail/default-thumbnail.png'
                ]);
            }

            if ($request->hasFile('video')) {
                $path = Storage::disk('materi')->putFile('video', $request->file('video'));
                $data['video'] = $path;
            } else if ($request['videoMateri'] == "keep") {
                $data['video'] = $materi['video'];
            }
            $materi->update($data);
            event(new MateriEditEvent($this->formatVideoDataForDatatable($materi)));
            return response()->json(['status' => true, 'message' => 'Materi updated successfully'], 200);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return response()->json([
                'errors' => $errors
            ], 422);
        }
    }

    public function destroy($id)
    {
        $id = EncryptionHelper::decrypt_custom($id);
        $materi = Materi::find($id);
        if ($materi == null) {
            return response()->json(['status' => false, 'message' => 'Materi not found'], 404);
        }
        $materi->delete();
        event(new MateriDeleteEvent($this->formatVideoDataForDatatable($materi)));
        return response()->json(['status' => true, 'message' => 'Materi deleted successfully'], 200);
    }

    private function formatVideoDataForDatatable($video)
    {
        $data = [];
        $data[] = null;
        $data[] = $video->kode_materi;
        $data[] = $video->title;
        $data[] = "<video class='w-36 h-36 bg-cover bg-center rounded-md' controls><source src='" . Storage::disk('materi')->url($video->video) . "' type='video/mp4'>Your browser does not support the video tag.</video>";
        $data[] = "<img class='w-36 h-36 bg-cover bg-center rounded-md' src='" . Storage::disk('materi')->url($video->thumbnail) . "'>";
        $data[] = null;
        $data[count($data) - 1] .=
            "<a href='video/edit/" . EncryptionHelper::encrypt_custom($video->id) . "' class='inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800' id='editModalBtn'>Edit</a>";
        $data[count($data) - 1] .=
            "<a href='video/delete/" . EncryptionHelper::encrypt_custom($video->id) . "' class='inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900' id='deleteModalBtn'>Delete</a>";

        return $data;
    }
}
