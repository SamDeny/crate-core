<?php

return [

    /**
     * Crate Application Environment
     * ---
     * The main environment on which your Crate application instance currently 
     * runs in. The native supported values are 'production', 'staging' and 
     * 'development', it is not recommended using other values.
     */
    'env' => env('CRATE_ENV', 'production'),
    

    /**
     * Crate Application Name
     * ---
     * The main application name, which will be written down in the log files, 
     * and similar application or module-related datas. It also occures in the 
     * JSON responses.
     */
    'name' => env('CRATE_APP', 'My Crate Application'),
    

    /**
     * Crate Application URL
     * ---
     * The main application URL on which your new Crate application will be 
     * available. You can also configure additional URLs, pointing to the same
     * Crate instance, we recommend using the @crate/channels modules for this.
     */
    'url' => env('CRATE_URL', 'http://localhost'),
    

    /**
     * Crate Application Base Path
     * ---
     * The base path on which the Crate Rest-API service should be available. 
     * This should be '/' for a API-only usage, when using any frontend or 
     * backend module, it is recommended setting this to '/api' or a different
     * domain or subdomain (start with http(s):// in this case).
     */
    'base' => env('CRATE_BASE', '/'),


    /**
     * Force HTTPS connection
     * ---
     * Forces HTTPs connections on each single request. Submission-less HTTP 
     * requests will be redirects using the HTTP 301 status code, other ones, 
     * like POST, PUT or PATCH are redirected using HTTP 303.
     */
    'https' => env('CRATE_FORCE_HTTPS', false),


    /**
     * Desired Locale
     * ---
     * The primary used language locale code, to be used for all responses sone
     * by the Crate Application. Keep in mind, that this does NOT affect the 
     * log or similar debugging data (always falls back to 'en').
     */
    'locale' => env('CRATE_LOCALE', 'en'),


    /**
     * Desired Timezone
     * ---
     * The primary used timezone within your Crate application process. This 
     * will overwrite the default timezone of your server, and is mainly used 
     * in the publishing process of pending content and the log informations.
     */
    'timezone' => env('CRATE_TIMEZONE', 'UTC'),


    /**
     * Debugging Mode
     * ---
     * Enabling the debugging mode is not recommended on productive or live 
     * Crate environments. This will show detailed error messages and responses, 
     * but has no affect on the logging process.
     */
    'debug' => env('CRATE_DEBUG', env('CRATE_ENV', 'production') !== 'production'),

    
    /**
     * Secret Phrase
     * ---
     * The secret key, which is used for secure hashing. This value should
     * contain 32 random generated characters. Best way to generate this key 
     * is using crate CLI: `crate setup:secret`.
     */
    'secret' => env('CRATE_SECRET', null),

    /**
     * Security Settings
     * ---
     * 
     */
    'security' => [
        'algorithms' => [\PASSWORD_BCRYPT],
        \PASSWORD_BCRYPT => [
            'cost' => 10
        ],

        'crypt' => 'openssl',
        'openssl' => [
            'cipher' => 'aes-256-gcm'
        ]
    ],


    /**
     * Session Configuration
     * ---
     * The following settings are meant for the Session ServiceProvider, 
     * provided by the @crate/core module. Visit the PHP documentation for 
     * more details: 
     * https://www.php.net/manual/en/function.session-set-cookie-params
     */
    'session' => [
        'name'      => env('CRATE_SESSION_ID', 'crate_session'),
        'lifetime'  => 0,
        'path'      => '/',
        'domain'    => env('CRATE_URL', 'localhost'),
        'secure'    => env('CRATE_FORCE_HTTPS', false),
        'httponly'  => true,
        'samesite'  => 'Strict'
    ]

];
