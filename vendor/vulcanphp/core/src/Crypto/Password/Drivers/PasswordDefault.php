<?php

namespace VulcanPhp\Core\Crypto\Password\Drivers;

class PasswordDefault implements IPasswordDriver
{
    const SECRET_SOLT = '{pd:2022}:^%&Q@$&*!@#$%^&*^:{/pd:2022}';

    public $algo = PASSWORD_BCRYPT;

    public function setAlgo($algo): self
    {
        $this->algo = $algo;
        return $this;
    }

    public function password(string $plain): string
    {
        $plain = sprintf("%s-%s", $plain, self::SECRET_SOLT);
        return password_hash($plain, $this->algo);
    }

    public function passwordVerify(string $plain, string $hash): bool
    {
        $plain = sprintf("%s-%s", $plain, self::SECRET_SOLT);
        return password_verify($plain, $hash);
    }
}
