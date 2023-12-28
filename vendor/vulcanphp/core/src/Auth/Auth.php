<?php

namespace VulcanPhp\Core\Auth;

use App\Models\User;
use VulcanPhp\Core\Auth\Drivers\BasicAuthDriver;
use VulcanPhp\Core\Auth\Interfaces\IAuth;
use VulcanPhp\Core\Auth\Interfaces\IAuthDriver;

class Auth implements IAuth
{
    protected IAuthDriver $Driver;

    protected bool $checked = false;

    public function __construct(?IAuthDriver $Driver = null)
    {
        $this->setDriver($Driver ?: new BasicAuthDriver);
    }

    public function setDriver(IAuthDriver $Driver): self
    {
        $this->Driver = $Driver;
        return $this;
    }

    public function getDriver(): IAuthDriver
    {
        return $this->Driver;
    }

    public function checkAuth(): self
    {
        if (!$this->checked) {
            $this->checked = true;
            $this->getDriver()->checkUser();
        }

        return $this;
    }

    public function isLogged(): bool
    {
        return ($this->getUser() instanceof User) === true && intval($this->getUser()?->id) > 0;
    }

    public function isGuest(): bool
    {
        return $this->isLogged() === false;
    }

    public function getUser(): ?User
    {
        return $this->checkAuth()->getDriver()->getUser();
    }

    public function hasRoles(array $roles): bool
    {
        return $this->isLogged() && in_array($this->getUser()?->role, $roles);
    }

    public function isRole(string $role): bool
    {
        return $this->isLogged() && $this->getUser()?->role === $role;
    }

    public function attempLogin(...$args): self
    {
        $this->getDriver()->setUser(...$args);
        return $this;
    }

    public function attemptLogout(): self
    {
        $this->getDriver()->removeUser();
        return $this;
    }
}
