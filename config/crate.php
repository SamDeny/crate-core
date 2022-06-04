<?php

return [

    /**
     * Crate Application Environment
     * ---
     * The main environment on which your Crate application instance currently 
     * runs in. The native supported values are 'production', 'staging' and 
     * 'development', but you can use other values as well.
     */
    'env' => env('CRATE_ENV', 'production'),
    

    /**
     * Crate Application Name
     * ---
     * The main application name, which will be written down in the log files, 
     * and similar application or module-related datas. It also occures in the 
     * JSON responses.
     */
    'name' => env('CRATE_NAME', 'Crate Application'),
    

    /**
     * Crate Application URL
     * ---
     * The main application URL on which your new Crate application will be 
     * available. You can also configure additional URLs, pointing to the same
     * Crate instance, by using crates 'channels' module.
     */
    'url' => env('CRATE_URL', 'http://localhost'),


    /**
     * Force HTTPS connection
     * ---
     * Forces HTTPs connections on each single request. Submission-less HTTP 
     * requests will be redirects using the HTTP 301 status code, other ones, 
     * like POST, PUT or PATCH are redirected using HTTP 303.
     */
    'https' => env('CRATE_HTTPS', false),


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

];
