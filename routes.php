<?php declare(strict_types=1);

use Citrus\Http\Request;
use Citrus\Http\Response;
use Citrus\Router\Router;
use Crate\Core\Controllers\UsersController;

citrus(function (Router $router) {

    $router->get('/', function(Request $request) {
        return (new Response)->setBody(config('crate.url'));
    });

    $router->ctrl('users', UsersController::class);

});
