<?php declare(strict_types=1);

use Citrus\Router\Router;

citrus(function (Router $router) {
    var_dump($collector);

    $collector->get('/', function() {
        echo 'test';
    });
});
