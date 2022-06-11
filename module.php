<?php declare(strict_types=1);

/**
 * Register Module
 * ---
 * First of all, our module must introduce itself to Crate. We've to use the 
 * module() function, since the dependency injection container hasn't been 
 * build yet. Thus, this file should ONLY set the basic details and config, 
 * such as the available routes, and MUST NOT handle any action itself. Keep in 
 * mind, that this file will be cached (unless otherwise stated).
 * 
 * @param $module is the already created Module instance for your extension.
 *        It already contains the basic data, provided by the composer.json 
 *        file. (unless no composer.json file exists in your module folder).
 */
module(function(\Crate\Core\Classes\Module $module) {

    // Since we're using a composer.json file within this module, we don't 
    // have to set the basic module data, but we can still add some extra 
    // flavour (adapt some details). Using $module->data = [] will merge the 
    // passed information with the already provided one.
    $module->data = [
        'status'                => 'alpha',

        // Similar to composer's "suggest" option but specificly for modules.
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
    $module->configurable('config');


    // This module provides some Citrus CLI Commands, we should inform Citrus
    // about them, so the end-user can use them.
    $module->commands([
        'cache'         => \Crate\Core\Commands\CacheCommand::class,
        'config'        => \Crate\Core\Commands\ConfigCommand::class,
        'crate'         => \Crate\Core\Commands\CrateCommand::class,
        'make'          => \Crate\Core\Commands\MakeCommand::class,
        'migrate'       => \Crate\Core\Commands\MigrateCommand::class,
        'module'        => \Crate\Core\Commands\ModuleCommand::class,
        'setup'         => \Crate\Core\Commands\SetupCommand::class
    ]);


    // Our crate/core package is the primary environment for the Crate CMS.
    // It provides the basic user and policies functionallity, and handles 
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
