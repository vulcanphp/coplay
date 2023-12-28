<?php

namespace VulcanPhp\Core\Crypto;

class Encryption
{
    const CIPHER_METHOD = 'AES-256-CBC';

    public static function encrypt(string $value): string
    {
        $key        = bin2hex(openssl_random_pseudo_bytes(4));
        $ivlen      = openssl_cipher_iv_length(self::CIPHER_METHOD);
        $iv         = openssl_random_pseudo_bytes($ivlen);
        $ciphertext = openssl_encrypt($value, self::CIPHER_METHOD, $key, 0, $iv);

        return base64_encode($ciphertext . '::' . $iv . '::' . $key);
    }

    public static function decrypt(string $ciphertext): string
    {
        list($encrypted_data, $iv, $key) = explode('::', base64_decode($ciphertext));

        return openssl_decrypt($encrypted_data, self::CIPHER_METHOD, $key, 0, $iv);
    }

    public static function encryptArray(array $arr): string
    {
        return base64_encode(encode_string($arr));
    }

    public static function decryptArray(string $string)
    {
        return decode_string(base64_decode($string));
    }
}
