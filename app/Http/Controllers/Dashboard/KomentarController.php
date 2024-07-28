<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\Komentar\KomentarCreateEvent;
use App\Events\Komentar\KomentarDeleteEvent;
use App\Events\VideoUser\VideoNotifikasiEvent;
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
            event(new KomentarCreateEvent(self::formatDataKomentar($komentar)));
            event(new VideoNotifikasiEvent(auth()->user()->name . ' Mengomentari Video'));
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

    public function destroy($id)
    {
        try {
            $komentar = Komentar::find($id);
            if ($komentar == null) {
                return response()->json(['status' => false, 'message' => 'Komentar not found'], 404);
            }
            event(new KomentarDeleteEvent(self::formatDataKomentar($komentar)));
            $komentar->delete();
            return response()->json(['status' => true, 'message' => 'Komentar deleted'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public static function formatDataKomentar($komentar)
    {
        return [
            'id' => $komentar->id,
            'userId' => $komentar->users->id,
            'komentar' => $komentar->komentar,
            'kode_komentar' => $komentar->kode_komentar,
            'name' => $komentar->users->name,
            'email' => $komentar->users->email,
            'created_at' => $komentar->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function show($id) {
        try {
            $komentar = Komentar::find($id);
            if ($komentar == null) {
                return response()->json(['status' => false, 'message' => 'Komentar not found'], 404);
            }
            return response()->json(['status' => true, 'data' => self::formatDataKomentar($komentar), 'message' => 'Sucsess Get Data Komentar'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public function update(Request $request, $id) {
        try {
            $komentar = Komentar::find($id);
            if ($komentar == null) {
                return response()->json(['status' => false, 'message' => 'Komentar not found'], 404);
            }
            $komentar->update([
                'komentar' => $request->komentar,
            ]);
            return response()->json(['status' => true, 'data' => $komentar, 'message' => 'Komentar updated successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }
}
