<?php declare(strict_types=1);

use Crate\Core\Http\RouteCollector;

citrus(function (RouteCollector $collector) {
    var_dump($collector);
});
