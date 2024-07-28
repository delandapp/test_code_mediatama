<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Komentar\Komentar;
use Illuminate\Http\Request;

class KomentarController extends Controller
{

    public function create(Request $request)
    {
        try {
            $komentar = auth()->user()->komentars()->create([
                'komentar' => $request->komentar,
                'materi_id' => $request->materi_id,
                'kode_komentar' => 'KOM-' . rand(100000, 999999),
            ]);

            return response()->json([
                'status' => true,
                'data' => $komentar,
                'message' => 'Komentar ditambahkan',
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public function getData()
    {
        try {
            $komentars = auth()->user()->komentars()->get();
            $komentar = Komentar::where('user_id', '!=', auth()->user()->id)->get();
            $komentars = $komentars->merge($komentar);
            $data = $komentars->map(function ($komentar) {
                return self::formatDataKomentar($komentar);
            });
            return response()->json(['status' => true, 'data' => $data, 'message' => 'Sucsess Get Data Komentar'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public static function formatDataKomentar($komentar)
    {
        return [
            'id' => $komentar->id,
            'komentar' => $komentar->komentar,
            'kode_komentar' => $komentar->kode_komentar,
            'name' => $komentar->users->name,
            'email' => $komentar->users->email,
            'created_at' => $komentar->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
