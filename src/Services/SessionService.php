<?php declare(strict_types=1);

namespace Crate\Services;

use Citrus\Concerns\ServiceConcern;

class SessionService extends ServiceConcern
{

    /**
     * @inheritDoc
     */
    public function bootstrap()
    {
        $this->start();
    }

    /**
     * Get current Session ID.
     *
     * @return string
     */
    public function getSessionId(): string
    {
        return session_id();
    }

    /**
     * Get current Session Name.
     *
     * @return string
     */
    public function getSessionName(): string
    {
        return session_name();
    }

    /**
     * Check if session is active.
     *
     * @return void
     */
    public function isActive(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Start a session.
     *
     * @return void
     */
    public function start(): void
    {
        if(session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name($this->application->config('crate.session.name'));
        session_set_cookie_params([
            'lifetime'  => $this->application->config('crate.session.lifetime'), 
            'path'      => $this->application->config('crate.session.path'), 
            'domain'    => $this->application->config('crate.session.domain'), 
            'secure'    => $this->application->config('crate.session.secure'), 
            'httponly'  => $this->application->config('crate.session.httponly'), 
            'samesite'  => $this->application->config('crate.session.samesite'), 
        ]);
        session_start();
    }

    /**
     * Restart a session.
     *
     * @param bool $destroy
     * @return void
     */
    public function restart(bool $destroy = false): void
    {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }
        session_regenerate_id($destroy);
    }

    /**
     * Set a new session value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get an existing session value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Unset a single session value.
     *
     * @param string $key
     * @return void
     */
    public function unset(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy the whole session.
     *
     * @return void
     */
    public function destroy()
    {
        session_destroy();
    }

}
