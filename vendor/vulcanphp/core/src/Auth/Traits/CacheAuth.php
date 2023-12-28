<?php

namespace VulcanPhp\Core\Auth\Traits;

use App\Models\User;
use VulcanPhp\FastCache\Interfaces\ICacheHandler;

trait CacheAuth
{
    protected ICacheHandler $CacheStorage;

    public function InitCacheAuth()
    {
        $this->CacheStorage = cache('CacheAuthenticateUsers');
        $this->CacheStorage->eraseExpired();
    }

    public function HasCacheUser($id): bool
    {
        return $this->CacheStorage->hasCache($id);
    }

    public function RemoveCacheUser($id): self
    {
        $this->CacheStorage->erase($id);
        return $this;
    }

    public function SetCacheUser(User $user): self
    {
        $this->CacheStorage->store($user->id, $user, '10 minutes');
        return $this;
    }

    public function GetCacheUser($id): User
    {
        return $this->CacheStorage->retrieve($id);
    }

    public function CloseCacheDB(): self
    {
        $this->CacheStorage->close();
        return $this;
    }

    public function StartCacheDB(): self
    {
        $this->CacheStorage->Reload();
        return $this;
    }
}
