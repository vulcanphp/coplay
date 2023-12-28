<?php

namespace VulcanPhp\Core\Auth\Traits;

use App\Models\UserMeta;
use Exception;
use VulcanPhp\Core\Crypto\Encryption;
use VulcanPhp\Core\Crypto\Hash;
use VulcanPhp\Core\Helpers\Cookie;

trait CookieAuth
{
    public function SetCookieAuth(int $id): self
    {
        $token = $this->GetCookieAuthSignature($id);

        if (!UserMeta::saveMeta(['cookie_token' => $token], $id)) {
            throw new Exception('Unable to save cookie auth token');
        }

        Cookie::set('user', Encryption::encrypt($id . '::' . $token), (1440 * 30));

        return $this;
    }

    public function GetCookieAuth(): ?int
    {
        list($id, $token) = explode('::', Encryption::decrypt(Cookie::get('user')));

        return $this->CheckCookieAuthSignature($id, $token) && $this->CheckCookieAuthSignatureValid($id, $token) ? $id : null;
    }

    public function HasCookieAuth(): bool
    {
        return Cookie::has('user');
    }

    public function RemoveCookieAuth(int $id): self
    {
        UserMeta::saveMeta(['cookie_token' => ''], $id);

        Cookie::remove('user');

        return $this;
    }

    public function GetCookieAuthSignature(int $id): string
    {
        return str_replace('$5$rounds=5000$', '', Hash::make($this->CreateAuthSignature($id), 'SHA-256'));
    }

    public function CheckCookieAuthSignature(int $id, string $token): bool
    {
        return Hash::validate($this->CreateAuthSignature($id), '$5$rounds=5000$' . $token);
    }

    protected function CreateAuthSignature(int $id): string
    {
        return 'sign:{user(' . md5($this->FakeUserId($id)) . ')}:sign';
    }

    protected function CheckCookieAuthSignatureValid(int $id, string $token): bool
    {
        $token = UserMeta::find(['user' => $id, 'meta_key' => 'cookie_token', 'value' => $token]);

        return $token !== false && isset($token->value) && !empty($token->value) && strlen($token->value) == 60;
    }

    protected function FakeUserId(string $id): string
    {
        // create another unique id other then real id..
        return sprintf("id:(%s)", ((($id * 25) + 15) - (($id * 6) - ($id / 2))) + strlen($id));
    }
}
