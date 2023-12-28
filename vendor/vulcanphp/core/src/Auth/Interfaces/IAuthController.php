<?php

namespace VulcanPhp\Core\Auth\Interfaces;

interface IAuthController
{
    public function login();

    public function logout();

    public function register();

    public function forget();

    public function reset();
}
