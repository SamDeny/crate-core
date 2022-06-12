<?php declare(strict_types=1);

use Citrus\Router\Router;
use Crate\Core\Controllers\UsersController;

citrus(function (Router $router) {


    $router->ctrl(UsersController::class);

});
