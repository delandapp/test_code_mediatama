<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\RequestVideo\RequestVideoApproveEvent;
use App\Events\RequestVideo\RequestVideoCancelEvent;
use App\Helpers\EncryptionHelper;
use App\Http\Controllers\Controller;
use App\Models\RequestVideo\RequestVideo;
use App\Models\RequestVideo\RequestVideoData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                $requestVideo->approved_at = Carbon::now();
                $requestVideo->save();
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
                return response()->json(['status' => true, 'message' => 'RequestVideo cancel successfully'], 200);
            }
        } catch (Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function formatRequestVideoDataForDatatable($requestvideo)
    {
        $data = [];
        $data[] = null;
        $data[] = $requestvideo->kode_request;
        $data[] = $requestvideo->materis->title;
        $data[] = "<div class='flex flex-col'> <p class='font-bold text-sm'>" . $requestvideo->custommers->name . "</p> <p class='text-sm'>" . $requestvideo->custommers->email . "</p> </div>";
        $data[] = Carbon::parse($requestvideo->created_at)->format('d-m-Y');
        $data[] = $requestvideo->status == 'pending' ? "<span class='pending text-sm font-medium rounded dark:bg-yellow-900 dark:text-purple-300'>" . $requestvideo->status . "</span>" : "<span class='approve text-sm font-medium rounded dark:bg-yellow-900 dark:text-purple-300'>" . $requestvideo->status . "</span>";
        $data[] = $requestvideo->expires_at ? $requestvideo->expires_at . ' Menit' : '-';
        $data[] = $requestvideo->approved_at ? Carbon::parse($requestvideo->approved_at)->format('d-m-Y') : '-';
        if (Auth::user()->can('approve-video') || Auth::user()->can('cancel-video')) {
            $data[] = null;
        }
        if (Auth::user()->can('approve-video') && $requestvideo->status == 'pending') {
            $data[count($data) - 1] .= "<a href='request/approve/" . EncryptionHelper::encrypt_custom($requestvideo->id) . "' class='inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-green-400 hover:bg-green-700 focus:ring-4 focus:ring-green-300 dark:bg-green-400 dark:hover:bg-green-700 dark:focus:ring-green-800' id='approveModalBtn'><i class='fa-solid fa-circle-check'></i></a>";
        }
        if (Auth::user()->can('cancel-video') && $requestvideo->status == 'approved') {
            $data[count($data) - 1]
                .= "<a href='request/cancel/" . EncryptionHelper::encrypt_custom($requestvideo->id) . "' class='inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900' id='cancelModalBtn'><i class='fa-solid fa-ban'></i></a>";
        }
        if (Auth::user()->can('edit-video') || Auth::user()->can('hapus-video') && $requestvideo->status == 'pending') {
            $data[] = null;
        }
        if (Auth::user()->can('edit-video') && $requestvideo->status == 'pending') {
            $data[count($data) - 1] .=
                "<a href='request/edit/" . EncryptionHelper::encrypt_custom($requestvideo->id) . "' class='mr-2 inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800' id='editModalBtn'>Edit</a>";
        }

        if (Auth::user()->can('hapus-video') && $requestvideo->status == 'pending') {
            $data[count($data) - 1] .=
                "<a href='request/delete/" . EncryptionHelper::encrypt_custom($requestvideo->id) . "' class='inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900' id='deleteModalBtn'>Delete</a>";
        }
        return $data;
    }
}
