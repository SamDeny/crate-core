<?php

return [

    /**
     * Default Database Driver
     * ---
     * Crate supports the usage of multiple database connections, using 
     * different drivers as well. But you need to declare a default driver here 
     * which will always be used, unless otherwise stated. The used name MUST
     * be available within the 'drivers' array below!
     */
    'default' => env('DATABASE_DRIVER', 'sqlite'),

    
    /**
     * Crate Database
     * ---
     * Crate itself is looking for a neat place to store some application 
     * related data as well. Feel free to share your database with Crate, or 
     * give Crate his own place and keep your content yours.
     */
    'crate' => env('DATABASE_DRIVER', 'sqlite'),


    /**
     * Available Database Drivers
     * ---
     * Below you will find all native provided database drivers. You can add and 
     * use as many database connections as you need, also with the same driver. 
     * Just use a unique identifier and provide the correct details.
     */
    'drivers' => [

        /**
         * SQLite Driver -> Depends on PHP's \SQLite
         */
        'sqlite'        => [
            'provider'  => \Crate\Database\Provider\SQLite::class,
            'path'      => env('DATABASE_PATH', '~/storage/crate/database.sqlite'),
            'pragmas'   => [
                'foreign_keys'  => env('DATABASE_FOREIGN_KEYS', true),
                'journal_mode'  => env('DATABASE_JOURNAL_MODE', 'WAL')
            ]
        ],

        /**
         * MongoDB Driver -> Depends on mongodb/mongodb package
         */
        'mongodb'       => [
            'provider'  => \Crate\Database\Provider\MongoDB::class,
        ],

        /**
         * MySQLi Driver -> Depends on PHP's \MySQLi
         */
        'mysqli'        => [
            'provider'  => \Crate\Database\Provider\MySQLi::class,
            'hostname'  => env('DATABASE_HOST', 'localhost'),
            'port'      => env('DATABASE_PORT', 5432),
            'username'  => env('DATABASE_USERNAME', 'root'),
            'password'  => env('DATABASE_PASSWORD', ''),
            'database'  => env('DATABASE_NAME', 'crate'),
            'socket'    => env('DATABASE_SOCKET', null)
        ],

        /**
         * PDO MySQL Driver -> Depends on PHP's PDO | PDO-MYSQL extension
         */
        'pdo-mysql'     => [
            'provider'  => \Crate\Database\Provider\PDOMySQL::class,
            'hostname'  => env('DATABASE_HOST', 'localhost'),
            'port'      => env('DATABASE_PORT', 5432),
            'username'  => env('DATABASE_USERNAME', 'root'),
            'password'  => env('DATABASE_PASSWORD', ''),
            'database'  => env('DATABASE_NAME', 'crate'),
            'socket'    => env('DATABASE_SOCKET', null),
            'dsn'       => env('DATABASE_DSN', null)
        ],

        /**
         * PDO SQLite Driver -> Depends on PHP's PDO | PDO-SQLITE extension
         */
        'pdo-sqlite'    => [
            'provider'  => \Crate\Database\Provider\PDOSQLite::class,
            'path'      => env('DATABASE_PATH', '~/storage/crate/database.sqlite'),
            'pragmas'   => [
                'foreign_keys'  => env('DATABASE_FOREIGN_KEYS', true),
                'journal_mode'  => env('DATABASE_JOURNAL_MODE', 'WAL')
            ]
        ],

        // We currently don't support PostgreSQL, but we plan to do so in the future.

    ],

];
