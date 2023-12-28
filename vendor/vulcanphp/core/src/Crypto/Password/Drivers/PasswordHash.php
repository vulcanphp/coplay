<?php

namespace VulcanPhp\Core\Crypto\Password\Drivers;

use VulcanPhp\Core\Crypto\Hash;

class PasswordHash implements IPasswordDriver
{
    public function password(string $plain): string
    {
        return str_replace('$2a$07$', '', Hash::make($plain, 'Blowfish'));
    }

    public function passwordVerify(string $plain, string $hash): bool
    {
        return Hash::validate($plain, '$2a$07$' . $hash);
    }
}
