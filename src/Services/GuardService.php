<?php declare(strict_types=1);

namespace Crate\Core\Services;

use Citrus\Concerns\ServiceConcern;
use Citrus\Exceptions\RuntimeException;
use Citrus\Http\Request;
use Crate\Core\Models\User;

class GuardService extends ServiceConcern
{

    /**
     * Undocumented function
     *
     * @param string $type
     * @param string $details
     * @param Request $request
     */
    public function __construct(string $type, string $details, Request $request)
    {
        if (!config("auth.$type") === null) {
            throw new RuntimeException("The $type authentication method is not supported.");
        }
        if (!config("auth.$type.enabled")) {
            throw new RuntimeException("The $type authentication method is not enabled.");
        }

        if ($type === 'bearer') {
            $scan = explode('.', $details);
            if (count($scan) === 3) {
                $result = $this->authorizeJWT($scan[0], $scan[1], $scan[2]);
            } else if (count($scan) === 1) {
                $result = $this->authorizeAccessToken($scan[0]);
            } else {
                throw new RuntimeException('The type of the passed Bearer token could not be detected.');
            }
        } else if ($type === 'hmac') {
            $result = $this->authorizeHMAC($details, $request->header('Date'), $request->target());
        } else if ($type === 'session') {
            $result = $this->authorizeSession($details);
        } else if ($type === 'basic') {
            $scan = explode(':', base64_decode($details));
            if (count($scan) === 2) {
                $result = $this->authorizeBasic($scan[0], $scan[1]);
            } else {
                throw new RuntimeException('The type of the passed Basic token could not be detected.');
            }
        }
    }

    protected function authorizeJWT(string $header, string $payload, string $secret): ?User
    {
        
    }

    protected function authorizeAccessToken(string $token): ?User
    {
        
    }

    protected function authorizeHMAC(string $token, string $date, string $target): ?User
    {
        
    }

    /**
     * Session-based authentication method
     *
     * @param string $session
     * @return User|null
     */
    protected function authorizeSession(string $session): ?User
    {
        if (($user = User::findOneBy('session', $session)) === null) {
            return null;
        }


    }

    /**
     * Basic authentication method using username / email and password.
     *
     * @param string $userdata
     * @param string $password
     * @return User|null
     */
    protected function authorizeBasic(string $userdata, string $password): ?User
    {
        if (filter_var($userdata, \FILTER_VALIDATE_EMAIL) === false) {
            $column = 'username';
        } else {
            $column = 'email';
            $userdata = strtolower(filter_var($userdata, \FILTER_SANITIZE_EMAIL));
        }

        if (($user = User::findOneBy($column, $userdata)) === null) {
            return null;
        }

        if (password_verify($password, $user->password)) {
            return $user;
        } else {
            return null;
        }
    }

}
