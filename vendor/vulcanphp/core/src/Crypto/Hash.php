<?php

namespace VulcanPhp\Core\Crypto;

class Hash
{
    protected const
        HASH_SECRET  = 'hs:^%&Q@$&*!@#$%^&*^:hs',
        ENCRYPT_ALGO = [
            'Blowfish' => ['2a', 7],
            'SHA-256'  => [5, 'rounds=5000'],
            'SHA-512'  => [6, 'rounds=5000'],
        ];

    public static function make(string $plain_text, string $algorithmn = 'Blowfish'): string
    {
        $plain_text = sprintf("%s-%s", $plain_text, self::HASH_SECRET);

        list($algo, $cost) = self::ENCRYPT_ALGO[$algorithmn];

        if ($algo == '2a' && is_int($cost)) {

            if ($cost < 4 || $cost > 16) {
                throw new \Exception('Hash: Invalid cost factor ' . $cost . ', it should be between 4 to 16');
            }

            $cost = sprintf('%02d', $cost);
        }

        $salt = '';

        for ($i = 0; $i < 8; ++$i) {
            $salt .= pack('S1', mt_rand(0, 0xffff));
        }

        $salt = strtr(rtrim(base64_encode($salt), '='), '+', '.');

        return crypt($plain_text, sprintf('$%s$%s$%s$', $algo, $cost, $salt));
    }

    public static function validate(string $plain, string $hash): bool
    {
        $plain = sprintf("%s-%s", $plain, self::HASH_SECRET);

        return crypt($plain, $hash) === $hash;
    }

    public static function random(int $length = 8, string $type = 'all'): string
    {
        $alpha   = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $nonzero = '123456789';
        $numeric = '0123456789';
        $special = '-=/\[];,.~!@#$%^&*()_+{}|:?<>';

        if ($type == 'nonzero') {
            $chars = $nonzero;
        } else if ($type == 'alpha') {
            $chars = $alpha;
        } else if ($type == 'numeric') {
            $chars = $numeric;
        } else if ($type == 'alphanumeric') {
            $chars = $nonzero . $alpha . $numeric;
        } else {
            // Default (all)
            $chars = $nonzero . $alpha . $numeric . $special;
        }

        $pieces = [];
        $max    = mb_strlen($chars, '8bit') - 1;

        for ($i = 0; $i < $length; ++$i) {
            $pieces[] = $chars[random_int(0, $max)];
        }

        return implode('', $pieces);
    }
}
