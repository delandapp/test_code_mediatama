<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DislikeController extends Controller
{
    public function create(Request $request)
    {

        try {
            $request->validate([
                'video_id' => 'required|exists:video_materials,id',
                'user_id' => 'required|exists:users,id',
            ]);

            $dislike = auth()->user()->dislikes()->create([
                'video_id' => $request->video_id,
                'user_id' => $request->user_id,
                'status' => true,
            ]);
            return response()->json(['status' => true, 'data' => $dislike, 'message' => 'Dislike created successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }
}
