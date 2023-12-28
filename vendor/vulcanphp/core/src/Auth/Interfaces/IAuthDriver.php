<?php

namespace VulcanPhp\Core\Auth\Interfaces;

use App\Models\User;

interface IAuthDriver
{
    public function checkUser(): self;

    public function setUser(User $user): self;

    public function getUser(): ?User;

    public function removeUser(): self;
}
