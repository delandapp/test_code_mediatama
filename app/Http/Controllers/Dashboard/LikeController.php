<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Like\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function handleLike(Request $request)
    {
        try {
            $request->validate([
                'materi_id' => 'required|exists:video_materials,id',
                'user_id' => 'required|exists:users,id',
            ]);
            $likeData = Like::where('materi_id', $request->materi_id)->where('user_id', $request->user_id)->first();
            if ($likeData != null) {
                $like = $likeData;
                $like->status = !$like->status;
                $like->save();
                return response()->json(['status' => true, 'data' => $like, 'message' => 'Like video successfully'], 200);
            }
            $like = auth()->user()->likes()->create([
                'materi_id' => $request->materi_id,
                'user_id' => $request->user_id,
                'status' => true,
            ]);
            return response()->json(['status' => true, 'data' => $like, 'message' => 'Like video successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'materi_id' => 'required|exists:video_materials,id',
                'user_id' => 'required|exists:users,id',
            ]);
            $like = auth()->user()->likes()->where('materi_id', $request->materi_id)->where('user_id', $request->user_id)->first();
            $like->delete();
            return response()->json(['status' => true, 'data' => $like, 'message' => 'Like deleted successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public function getData($id)
    {
        try {
            $data = Like::where('materi_id', $id)->get();
            $datalike = [];
            $datalike["data_like"] = $data->map(function ($data) {
                return self::formatDatalike($data);
            });
            $datalike['liked'] = auth()->user()->likes()->where('materi_id', $id)->first()->status;
            $datalike['dislaked'] = auth()->user()->dislikes()->where('materi_id', $id)->first()->status;
            $datalike["total_like"] = $data->where('status', '1')->count();
            return response()->json(['status' => true, 'data' => $datalike, 'message' => 'Get like video successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public static function formatDatalike($data)
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
