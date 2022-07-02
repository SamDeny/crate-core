<?php declare(strict_types=1);

namespace Crate\Core\Middleware;

use Citrus\Contracts\MiddlewareContract;
use Citrus\Exceptions\RuntimeException;
use Citrus\Http\Request;
use Citrus\Http\Response;

class AuthorizationMiddleware implements MiddlewareContract
{

    /**
     * Process Request
     *
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function process(Request $request, \Closure $next): Response
    {
        $session = $request->receive('currentUser');
        if (empty($session)) {
            throw new RuntimeException('The AuthorizationMiddleware requires the AuthenticationMiddleware to be set before.');
        }

        return $next();
    }

}
