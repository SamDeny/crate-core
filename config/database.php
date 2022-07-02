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
     * 
     * The configuration below is based on the used driver, check crate's docs 
     * for more information about those.
     */
    'drivers' => [

        /**
         * SQLite Driver -> Depends on PHP's \SQLite extension (ext-sqlite)
         */
        'sqlite'        => [

            // Driver class which extends DriverInterface
            'provider'      => \Crate\Core\Database\Drivers\SQLite::class,

            // Driver-specific configuration, which are passed as named 
            // arguments to it's constructor
            'path'          => env('DATABASE_PATH', '~/storage/crate/database.sqlite'),
            'encryptionKey' => null,
            'pragmas'       => [
                'foreign_keys'  => env('DATABASE_FOREIGN_KEYS', true),
                'journal_mode'  => env('DATABASE_JOURNAL_MODE', 'WAL')
            ]
        ],

        /**
         * MongoDB Driver -> Depends on ext-mongodb and the mongodb/mongodb package
         */
        'mongodb'       => [

            // Driver class which extends DriverInterface
            'provider'      => \Crate\Core\Database\Drivers\MongoDB::class,

            // Driver-specific configuration, which are passed as named 
            // arguments to it's constructor
            'dns'           => env('DATABASE_DNS', 'mongodb://localhost:27017'),
            'database'      => env('DATABASE_NAME', 'crate'),
            'dnsOptions'    => [ ],
            'driverOptions' => [ ]
        ],

        /**
         * MySQLi Driver -> Depends on PHP's \MySQLi
         */
        'mysqli'        => [

            // Driver class which extends DriverInterface
            'provider'  => \Crate\Core\Database\Drivers\MySQLi::class,

            // Driver-specific configuration, which are passed as named 
            // arguments to it's constructor
            'hostname'  => env('DATABASE_HOST', 'localhost'),
            'port'      => env('DATABASE_PORT', 5432),
            'username'  => env('DATABASE_USERNAME', 'root'),
            'password'  => env('DATABASE_PASSWORD', ''),
            'database'  => env('DATABASE_NAME', 'crate'),
            'socket'    => env('DATABASE_SOCKET', '')
        ],

        /**
         * PostgreSQL Driver -> Depends on PHP's pgsql_* functions
         */
        'postgres'      => [
            'info'      => "We don't support PostgreSQL at the moment, but we plan to do so in the future!",
            'provider'  => null
        ],

        /**
         * PDO MySQL Driver -> Depends on PHP's PDO | PDO-MYSQL extension
         */
        'pdo-mysql'     => [
            'info'      => "We don't support PDO at the moment, but we plan to do so in the future!",
            'provider'  => null
        ],

        /**
         * PDO SQLite Driver -> Depends on PHP's PDO | PDO-SQLITE extension
         */
        'pdo-sqlite'    => [
            'info'      => "We don't support PDO at the moment, but we plan to do so in the future!",
            'provider'  => null
        ],

        /**
         * PDO PostgreSQL Driver -> Depends on PHP's PDO | PDO-PGSQL extension
         */
        'pdo-pgsql'     => [
            'info'      => "We don't support PDO at the moment, but we plan to do so in the future!",
            'provider'  => null
        ]
    ],

];
