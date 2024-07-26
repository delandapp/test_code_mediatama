<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;

class EncryptionHelper
{
    public static function encrypt_custom($data)
    {
        $key = 'delandapp'; // Secret key yang sama untuk enkripsi dan dekripsi
        $cipher = "aes-256-cbc";

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);

        // Generate UUID (versi 4)
        $uuid = Uuid::uuid4();

        // Gabungkan UUID, data terenkripsi, dan IV
        return $uuid->toString() . '::' . bin2hex($encrypted) . '::' . bin2hex($iv);
    }

    public static function decrypt_custom($data)
    {
        $key = 'delandapp';
        $cipher = "aes-256-cbc";

        // Pisahkan UUID, data terenkripsi, dan IV
        list($uuid, $encrypted_data, $iv) = explode('::', $data, 3);
        $encrypted_data = hex2bin($encrypted_data);
        $iv = hex2bin($iv);

        // Dekripsi data
        $decrypted = openssl_decrypt($encrypted_data, $cipher, $key, 0, $iv);

        // Anda bisa melakukan validasi UUID di sini (opsional)

        return $decrypted;
    }
}
