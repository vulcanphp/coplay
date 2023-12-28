<?php

namespace VulcanPhp\Core\Auth\Interfaces;

use App\Models\User;

interface IAuth
{
    public function checkAuth(): self;

    public function isLogged(): bool;

    public function isGuest(): bool;

    public function hasRoles(array $roles): bool;

    public function isRole(string $role): bool;

    public function getUser(): ?User;

    public function attemptLogout(): self;

    public function attempLogin(...$args): self;
}
