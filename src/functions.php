<?php declare(strict_types=1);

use Crate\Core\Modules\ModuleRegistry;

if (!function_exists('module')) {
    /**
     * Initialize Module
     *
     * @param \Closure $callback
     * @return void
     */
    function module(\Closure $callback)
    {
        citrus(\Crate\Core\Modules\ModuleRegistry::class)->register($callback);
    }
}
