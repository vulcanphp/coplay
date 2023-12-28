<?php

namespace VulcanPhp\Core\Foundation;

use VulcanPhp\Core\Foundation\Exceptions\ControllerException;

class Controller
{
    protected array $middlewares = array();

    protected function setMiddlewares(array $middleware): void
    {
        $this->middlewares = $middleware;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    protected function checkAuth(array $roles)
    {
        if (!function_exists('auth')) {
            throw new ControllerException('Auth does not initiated');
        }

        if (auth()->isGuest()) {
            return redirect(auth_url('login'));
        } elseif (!auth()->hasRoles($roles)) {
            throw new ControllerException('you don\'t have access to this page.');
        }

        return true;
    }
}
