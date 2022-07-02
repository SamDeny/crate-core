<?php declare(strict_types=1);

namespace Crate\Core\Middleware;

use Citrus\Contracts\MiddlewareContract;
use Citrus\Http\Request;
use Citrus\Http\Response;
use Crate\Core\Services\SessionService;

class SessionMiddleware implements MiddlewareContract
{

    /**
     * @var SessionService
     */
    protected SessionService $session;

    /**
     * Create new Session Middleware
     *
     * @param SessionService $session
     */
    public function __construct(SessionService $session)
    {
        $this->session = $session;
    }

    /**
     * Process Request
     *
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function process(Request $request, \Closure $next): Response
    {
        $this->session->start();
        $request->extend('session', $this->session);
        return $next();
    }

}
