<?php declare(strict_types=1);

use Citrus\Http\Request;
use Citrus\Http\Response;
use Citrus\Router\Router;
use Crate\Core\Controllers\PoliciesController;
use Crate\Core\Controllers\PrivilegesController;
use Crate\Core\Controllers\UsersController;
use Crate\Core\Middleware\AuthenticationMiddleware;
use Crate\Core\Middleware\AuthorizationMiddleware;
use Crate\Core\Middleware\SessionMiddleware;
use Crate\Core\Models\User;

citrus(function (Router $router) {
    $router->group([
        'prefix' => config('crate.base', '/')
    ], function (Router $router) {
        $router->get('/', function(Request $request) {

            dump(new User);
            

            return (new Response)->setJSON([
                'application'   => [
                    'title'     => 'Crate CMS',
                    'comment'   => 'Thanks for using Crate CMS',
                    'version'   => citrus()->getVersion(),
                    'citrus'    => citrus()->getVersion(),
                    'php'       => citrus()->getPHPVersion()
                ],
                'website'       => [
                    'title'     => config('crate.name'),
                    'url'       => config('crate.url')
                ]
            ]);
        });
        
        $router->group([
            'middleware' => [
                SessionMiddleware::class,
                AuthenticationMiddleware::class,
                AuthorizationMiddleware::class,
            ]
        ], function(Router $router) {

            $router->ctrl('/users', UsersController::class, [
                'middlewareOptions' => [
                    AuthenticationMiddleware::class => function (Request $request) {
                        $currentUser = $request->receive('currentUser');
                        return $currentUser->can($action . ':users');
                    }
                ]
            ]);

            $router->ctrl('/policies', PoliciesController::class, [
                'middlewareOptions' => [
                    AuthenticationMiddleware::class => function (Request $request) {
                        $currentUser = $request->receive('currentUser');
                        return $currentUser->can('policies');
                    }
                ]
            ]);
            
            $router->ctrl('/privileges', PrivilegesController::class, [
                'middlewareOptions' => [
                    AuthenticationMiddleware::class => function (Request $request) {
                        $currentUser = $request->receive('currentUser');
                        return $currentUser->can('privileges');
                    }
                ]
            ]);

        });
    });
});
