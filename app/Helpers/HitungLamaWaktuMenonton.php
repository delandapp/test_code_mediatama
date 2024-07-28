<?php

namespace App\Helpers;

use Carbon\Carbon;

class HitungLamaWaktuMenonton
{
    public static function hitungLamaWaktuMenonton($expiredAt, $expiresAt)
    {
        $expiredAt = Carbon::parse($expiredAt);
        $expiresAtMinutes = intval($expiresAt);
        $expiredAt = $expiredAt->copy()->subMinutes($expiresAtMinutes);
        $waktuSekarang = Carbon::now();
        $selisihDenganSekarang = $expiredAt->diffInSeconds($waktuSekarang);
        $menit = max(0, $expiredAt->diffInSeconds($waktuSekarang));
        $menit = intval($menit / 60);
        $detik = $selisihDenganSekarang % 60;

        return "{$menit} Menit {$detik} Detik";
    }
}
