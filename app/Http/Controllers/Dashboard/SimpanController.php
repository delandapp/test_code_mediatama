<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Simpan\Simpan;
use Illuminate\Http\Request;

class SimpanController extends Controller
{
    public function handleSimpan(Request $request)
    {
        try {
            $request->validate([
                'materi_id' => 'required|exists:video_materials,id',
                'user_id' => 'required|exists:users,id',
            ]);
            $likeData = Simpan::where('materi_id', $request->materi_id)->where('user_id', $request->user_id)->first();
            if ($likeData != null) {
                $like = $likeData;
                $like->status = !$like->status;
                $like->save();
                return response()->json(['status' => true, 'data' => $like, 'message' => 'Simpan video successfully'], 200);
            }
            $like = auth()->user()->simpans()->create([
                'materi_id' => $request->materi_id,
                'user_id' => $request->user_id,
                'status' => true,
            ]);
            return response()->json(['status' => true, 'data' => $like, 'message' => 'Simpan video successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public function getData($id)
    {
        try {
            $data = Simpan::where('materi_id', $id)->get();
            $dataSimpan = [];
            $dataSimpan["data_simpan"] = $data->map(function ($data) {
                return self::formatDataSimpan($data);
            });
            $dataSimpan["total_simpan"] = $data->where('status', '1')->count();
            return response()->json(['status' => true, 'data' => $dataSimpan, 'message' => 'Get simpan video successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public static function formatDataSimpan($data)
    {
        return [
            'id' => $data->id,
            'materi_id' => $data->materi_id,
            'user_id' => $data->user_id,
            'name' => $data->users->name,
            'email' => $data->users->email,
            'created_at' => $data->created_at->format('Y-m-d H:i:s'),
            'status' => $data->status
        ];
    }
}
