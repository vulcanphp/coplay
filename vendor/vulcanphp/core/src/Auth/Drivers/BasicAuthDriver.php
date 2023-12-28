<?php

namespace VulcanPhp\Core\Auth\Drivers;

use App\Models\User;
use VulcanPhp\Core\Auth\Interfaces\IAuthDriver;
use VulcanPhp\Core\Auth\Traits\CacheAuth;
use VulcanPhp\Core\Auth\Traits\CookieAuth;
use VulcanPhp\Core\Helpers\Session;

class BasicAuthDriver implements IAuthDriver
{
    use CacheAuth, CookieAuth;

    protected  ?User $user = null;

    public function checkUser(): self
    {
        // start session for use
        Session::start();

        // WARNING: This is Un-Secure to Use Cookie Auth to remember logged user
        // use cookie auth <START>
        if (config('auth.use_cookie') && !Session::has('user') && $this->HasCookieAuth()) {
            Session::set('user', $this->GetCookieAuth());
        }
        // use cookie auth <END>

        // check if logged user
        if (Session::has('user')) {

            // get user id from session
            $id = intval(Session::get('user'));

            // WARNING: This is Un-Secure to Use Cache to Store Authenticate User Data
            if (config('auth.use_cache')) {

                // use cache for authenticate user <START>
                $this->InitCacheAuth();

                if ($this->HasCacheUser($id)) {
                    $user = $this->GetCacheUser($id);
                } else {
                    $user = User::find($id);

                    if ($user !== false) {
                        $this->SetCacheUser($user);
                    }
                }

                $this->CloseCacheDB();
                // use cache for authenticate user <END>

            } else {
                /**
                 * @var $user
                 *
                 * use this $user variable instead of cache auth code
                 */
                $user = User::find($id);
            }

            // set or remove current user
            if ($user !== false) {
                $this->user = $user;
            } else {
                $this->removeUser();
            }

            // remove temp $user
            unset($user);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user ?? null;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        Session::set('user', $this->user->id);

        $this->SetCookieAuth($this->user->id);

        return $this;
    }

    public function removeUser(): self
    {
        $id = intval($this->user?->id);

        if ($id > 0) {
            $this->StartCacheDB()->RemoveCacheUser($id)->CloseCacheDB();
            $this->RemoveCookieAuth($id);
        }

        Session::remove('user');

        $this->user = null;

        return $this;
    }
}
