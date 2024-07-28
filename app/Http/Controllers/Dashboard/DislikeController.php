<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Dislike\Dislike;
use Illuminate\Http\Request;

class DislikeController extends Controller
{
    public function handleDislike(Request $request)
    {
        try {
            $request->validate([
                'materi_id' => 'required|exists:video_materials,id',
                'user_id' => 'required|exists:users,id',
            ]);
            $likeData = Dislike::where('materi_id', $request->materi_id)->where('user_id', $request->user_id)->first();
            if ($likeData != null) {
                $like = $likeData;
                $like->status = !$like->status;
                $like->save();
                return response()->json(['status' => true, 'data' => $like, 'message' => 'Dislike video successfully'], 200);
            }
            $like = auth()->user()->dislikes()->create([
                'materi_id' => $request->materi_id,
                'user_id' => $request->user_id,
                'status' => true,
            ]);
            return response()->json(['status' => true, 'data' => $like, 'message' => 'Dislike video successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public function getData($id)
    {
        try {
            $data = Dislike::where('materi_id', $id)->get();
            $dataDislike = [];
            $dataDislike["data_dislike"] = $data->map(function ($data) {
                return self::formatDataDislike($data);
            });
            $dataDislike["disliked"] = auth()->user()->dislikes()->where('materi_id', $id)->first() == null ? 0 : auth()->user()->dislikes()->where('materi_id', $id)->first()->status;
            $dataDislike["liked"] = auth()->user()->likes()->where('materi_id', $id)->first() == null ? 0 : auth()->user()->likes()->where('materi_id', $id)->first()->status;
            $dataDislike["total_dislike"] = $data->where('status', '1')->count();
            return response()->json(['status' => true, 'data' => $dataDislike, 'message' => 'Get dislike video successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public static function formatDataDislike($data)
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
