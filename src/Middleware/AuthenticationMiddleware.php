<?php declare(strict_types=1);

namespace Crate\Core\Middleware;

use Citrus\Contracts\MiddlewareContract;
use Citrus\Exceptions\RuntimeException;
use Citrus\Http\Request;
use Citrus\Http\Response;

class AuthenticationMiddleware implements MiddlewareContract
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
        $auth = $request->header('Authorization') ?? null;

        if ($auth && strpos($auth, ' ')) {
            [$type, $details] = explode(' ', $auth, 2);

            $guard = new Guard(strtolower($type, $details, $request));
            if ($guard->auth()) {
                $request->extend('guard', $guard);
            } else {
                return false;
            }

            if ($type === 'bearer') {

            } else if ($type === 'hmac') {

            } else if ($type === 'session') {

            } else if ($type === 'basic') {
                $details = base64_decode($details, true);
            }
        } else {
            $request->extend('guard', Guard::asPublic());
        }

        dd($request->header('Authorization'));

        $request->extend('currentUser', 'public');
        return $next();
    }

}
