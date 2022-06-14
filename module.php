<?php declare(strict_types=1);

/**
 * Register Module
 * ------------------------------------------------------------
 * First of all, we need to ontroduce our Module to Crate by 
 * using the `module()` function with a Closure as argument, 
 * which contains the unique Module instance.
 */
module(function (\Crate\Core\Modules\Module $module) {

    // Since we're using a composer.json file within this module, we don't have 
    // to set the basic module data, but we can still add some extra flavour.
    $module->data = [
        'status'                => 'alpha',
        'optional-dependencies' => [
            'crate/nodejs'  => '^0.1.0'
        ]
    ];

    // During development, it may is a good idea to disable the whole caching 
    // for this module, at least in a non-production environment. Thus, we
    // check the environment and current state of our module (the state is 
    // evaluated of the provided data / composer.json details).
    $module->cache = citrus()->isProduction() && $module->isStable(); 

    // This module provides some configuration files in the ./config folder.
    // Crate should know about them, so the end-users are able to configure 
    // your module without touching your source files.
    // PS.: You can use YAML, JSON, INI or PHP for your configuration files. 
    $module->configure('config');

    // This module provides some Citrus CLI Commands, we should inform Citrus
    // about them, so the end-user can use them.
    $module->commands([
        \Crate\Core\Commands\CacheCommand::class,
        \Crate\Core\Commands\ConfigCommand::class,
        \Crate\Core\Commands\MigrateCommand::class,
        \Crate\Core\Commands\ModuleCommand::class,
        \Crate\Core\Commands\SetupCommand::class
    ]);

    // Our crate/core package is the primary environment for the Crate CMS.
    // It provides the basic user and policies functionality, and handles 
    // all the basic tokens we need. Those provided services must be registered 
    // as well, so other plugins can depend on it.
    $module->services([
        'auth'          => \Crate\Core\Services\AuthenticationService::class,
        'guard'         => \Crate\Core\Services\AuthorizationService::class,
        "policy"        => \Crate\Core\Services\PolicyService::class,
        'token'         => \Crate\Core\Services\TokenService::class
    ]);

    // Our crate/core package provides some additional class factories, which 
    // are - similar to the Service Providers above - available for every other 
    // module. Thus we need to set them as follows:
    $module->factories([
        'connection'    => \Crate\Core\Factories\ConnectionFactory::class,
        'mailer'        => \Crate\Core\Factories\MailerFactory::class
    ]);

    // Now, we'have to declare the first basic routes, which will be available 
    // within the Crate ecosystem.
    $module->routes('routes.php');

});
