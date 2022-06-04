<?php declare(strict_types=1);

if (!function_exists('module')) {
    /**
     * Initialize Module
     *
     * @param \Closure $callback
     * @return void
     */
    function module(\Closure $callback)
    {
        return citrus()->getRuntimeService()->getModuleRegistry()->register($callback);
    }
}
