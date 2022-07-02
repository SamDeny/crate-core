<?php declare(strict_types=1);

namespace Crate\Core\Commands;

use Citrus\Console\Console;
use Citrus\Console\Form;
use Citrus\Console\Writer;
use Citrus\Contracts\CommandContract;
use Citrus\Exceptions\RuntimeException;
use Crate\Core\Database\Migrations;
use Crate\Core\Database\Migrator;
use Crate\Core\Models\User;

class SetupCommand implements CommandContract
{

    /**
     * Describe the command including all available methods and arguments.
     *
     * @return array
     */
    static public function describe(): array
    {
        return [
            'setup:key' => [

            ],
            'setup:install' => [

            ],
            'setup:upgrade' => [

            ]
        ];
    }


    /**
     * Console Instance
     *
     * @var Console
     */
    protected Console $console;

    /**
     * Writer Instance
     *
     * @var Writer
     */
    protected Writer $writer;

    /**
     * Create a new AboutCommand instance.
     *
     * @param Console $console
     * @param Writer $writer
     */
    public function __construct(Console $console, Writer $writer)
    {
        $this->console = $console;
        $this->writer = $writer;
    }

    public function key()
    {
        print('test');
    }

    public function install()
    {

        $this->writer->head('Install your new Crate Project', 'yellow');
        $this->writer->line();

        // Create a new Console Form
        $form = new Form();

        // Select Database Driver Question
        $default = env('DATABASE_DRIVER', 'sqlite');
        if ($default === 'mongodb') {
            $default = 2;
        } else if ($default === 'mysql') {
            $default = 1;
        } else {
            $default = 0;
        }
        $form->select('driver', [
            'label' => 'Select the desired database driver',
            'options' => [
                0 => 'SQLite            (using: SQLite3)',
                1 => 'MySQL             (using: MySQLi)',
                2 => 'MongoDB           (using: mongodb with mongodb/mongodb)',
                3 => 'PostgreSQL        (WiP - Not available right now)',
                4 => 'PDO - SQLite      (WiP - Not available right now)',
                5 => 'PDO - MySQL       (WiP - Not available right now)',
                6 => 'PDO - PostgreSQL  (WiP - Not available right now)',
            ],
            'disabled' => [3, 4, 5, 6],
            'default' => $default,
            'onValid' => function(Writer $writer, string $value) {
                if ($value === '0') {
                    return 'goto:sqlite_database';
                } else if ($value === '1') {
                    return 'goto:mysql_hostname';
                } else if ($value === '2') {
                    return 'goto:mongodb_dns';
                }
            },
            'onInvalid' => function(Writer $writer, string $value) {
                $writer->line();
                $writer->line(
                    $writer->red('Please select one of the available options:')
                );
                return 'repeat';
            }
        ]);

        // SQLite - Select Database Path
        $form->input('sqlite_database', [
            'label' => 'Set the path where your SQLite database should be located',
            'help' => 'Paths within Crate\'s root directory MUST start with "$/"!',
            'default' => env('DATABASE_PATH', '$/storage/data/database.sqlite'),
            'validate' => function(string $value, array $formdata, Writer $writer) {
                if (strpos($value, '$') === 0 || strpos($value, ':') === 0) {
                    $path = path($value);
                } else {
                    $path = $value;
                }

                if (file_exists($path)) {
                    return $value;
                } else {
                    $folder = dirname($path);
                    if (!file_exists($folder)) {
                        if (!@mkdir($folder, 0666, true)) {
                            $writer->line();
                            $writer->line($writer->red('The SQLite database path does not exist and could not be created.'));
                            return false;
                        }
                    }

                    if (!@touch($path)) {
                        $writer->line();
                        $writer->line($writer->red('The SQLite database does not exist and could not be created.'));
                        return false;
                    } else {
                        return $value;
                    }
                }
            },
            'onValid' => function() {
                return 'goto:root';
            },
        ]);

        // MySQL - Select Database Details
        $form->input('mysql_hostname', [
            'label' => 'Set the hostname and port for your MySQL connection',
            'default' => env('DATABASE_HOST', 'localhost') . ':' . env('DATABASE_PORT', 3306),
            'onValid' => function() {
                return 'goto:mysql_socket';
            },
        ]);
        $form->input('mysql_socket', [
            'label' => 'Set the Socket path for your MySQL connection',
            'default' => env('DATABASE_SOCKET', ''),
            'onValid' => function() {
                return 'goto:mysql_username';
            },
        ]);
        $form->input('mysql_username', [
            'label' => 'Set the username for your MySQL database',
            'default' => env('DATABASE_USERNAME', 'root'),
            'onValid' => function() {
                return 'goto:mysql_password';
            },
        ]);
        $form->password('mysql_password', [
            'label' => 'Set the password for your MySQL database',
            'default' => env('DATABASE_PASSWORD', ''),
            'onValid' => function() {
                return 'goto:mysql_database';
            },
        ]);
        $form->input('mysql_database', [
            'label' => 'Set the database name of your MySQL database',
            'default' => env('DATABASE_NAME', 'crate'),
            'validate' => function(string $value, array $formdata, Writer $writer) {
                try {
                    [$hostname, $port] = array_pad(explode(':', $formdata['mysql_hostname'], 2), 2, 3306);
                    $mysql = @new \MySQLi(
                        $hostname,
                        $formdata['mysql_username'],
                        $formdata['mysql_password'],
                        $value,
                        intval($port),
                        $formdata['mysql_socket']
                    );
                    if ($mysql->connect_error) {
                        throw new \Exception($mysql->connect_error);
                    }
                } catch(\Exception $e) {
                    $writer->line();
                    $writer->line($writer->red('The passed MySQL connection details are incorrect. Error:'));
                    $writer->line($writer->red($e->getMessage()));
                    $writer->line();
                    return false;
                }
                return $value;
            },
            'onValid' => function() {
                return 'goto:root';
            },
            'onInvalid' => function() {
                return 'goto:mysql_hostname';
            }
        ]);

        // MongoDB - Select Database Details
        $form->input('mongodb_dns', [
            'label' => 'Set the database DNS of your MongoDB connection',
            'default' => env('DATABASE_DNS', 'mongodb://localhost:27017'),
            'onValid' => function() {
                return 'goto:mongodb_database';
            },
        ]);
        $form->input('mongodb_database', [
            'label' => 'Set the database name of your MongoDB connection',
            'default' => env('DATABASE_NAME', 'crate'),
            'validate' => function(string $value, array $formdata, Writer $writer) {
                
            },
            'onValid' => function() {
                return 'goto:root';
            },
            'onInvalid' => function() {
                return 'goto:mongodb_dns';
            }
        ]);

        // Crate - Root Domain
        $form->input('root', [
            'label' => 'Set the root domain of your new Crate Project',
            'help' => 'Crate supports multiple domains, but still requires a master domain.',
            'default' => env('CRATE_URL', 'http://localhost'),
            'onValid' => function() {
                return 'goto:base';
            },
        ]);

        // Crate - Root Base Path
        $form->input('base', [
            'label' => 'Set the RESTful-API base-path of your new Crate Project',
            'help' => 'You can disable the RESTful-API function by passing "none"',
            'default' => env('CRATE_BASE', '/'),
            'onValid' => function() {
                return 'goto:username';
            },
        ]);

        // Crate - Root Username
        $form->input('username', [
            'label' => 'Set your own username for your new Crate Project',
            'default' => 'admin',
            'onValid' => function() {
                return 'goto:email';
            },
        ]);

        // Crate - Root E-Mail address
        $form->input('email', [
            'label' => 'Set your own email address for your new Crate Project',
            'default' => 'temp@localhost.tld',
            'validate' => function(string $value) {
                return filter_var($value, \FILTER_VALIDATE_EMAIL)? strtolower(filter_var($value, \FILTER_SANITIZE_EMAIL)): false;
            },
            'onValid' => function() {
                return 'goto:password';
            },
            'onInvalid' => function(Writer $writer, string $value) {
                $writer->line();
                $writer->line(
                    $writer->red('Please enter a valid email address')
                );
                return 'repeat';
            }
        ]);

        // Crate - Root Password
        $form->password('password', [
            'label' => 'Set your own password for your new Crate Project',
            'onValid' => function() {
                return 'finish';
            }
        ]);

        // Execute Form
        $data = $this->writer->form($form);
        $this->writer->line();
        $this->writer->head('Install @crate/core Module', 'yellow');

        // Create Basic Configuration file
        $secret = env('CRATE_SECRET', '');
        $config = [
            'crate' => [
                'url' => $data['root'],
                'base' => $data['base'] === 'none'? false: $data['base'],
                'secret' => empty($secret)? bin2hex(random_bytes(16)): $secret,
                'security' => $this->measureSecurity()
            ]
        ];
        if ($data['driver'] === 0) {
            $config['database'] = [
                'default' => 'sqlite',
                'drivers' => [
                    'sqlite' => [
                        'path' => $data['sqlite_database']
                    ]
                ]
            ];
        } else if ($data['driver'] === 1) {
            [$hostname, $port] = array_pad(explode(':', $data['mysql_hostname']), 2, 3306);
            $config['database'] = [
                'default' => 'mysql',
                'drivers' => [
                    'mysql' => [
                        'hostname'  => $hostname,
                        'port'      => intval($port),
                        'socket'    => $data['mysql_socket'],
                        'username'  => $data['mysql_username'],
                        'password'  => $data['mysql_password'],
                        'database'  => $data['mysql_database'],
                    ]
                ]
            ];
        } else if ($data['driver'] === 2) {
            $config['database'] = [
                'default' => 'mongodb',
                'drivers' => [
                    'mongodb' => [
                        'dns'       => $data['mongodb_dns'],
                        'database'  => $data['mongodb_database'],
                    ]
                ]
            ];
        }
        $filepath = path(':storage/data') . DIRECTORY_SEPARATOR . 'config.php';
        if (@file_put_contents($filepath, '<?php return '. var_export($config, true) .';')) {
            $this->writer->line($this->writer->green('Success: Configuration stored'));
        }

        // Set Configurations, since we're in the same Request cycle
        citrus()->setConfiguration('crate', $config['crate']);
        citrus()->setConfiguration('database', $config['database']);

        // Migrate @crate/core package
        $migrations = new Migrations();
        $files = $migrations->scan('@crate/core');

        $this->writer->line();
        $this->writer->line('Start Migration');
        foreach ($files AS $file) {
            $status = $migrations->execute('@crate/core', $file);
            $migrator = $migrations->getLastMigrator();
            $migration = $migrations->getLastMigration();

            if ($status) {
                $this->writer->line(
                    $this->writer->green('  Success: ' . $migrator->getFile() . ' - ' . $migration->title())
                );
            } else {
                $this->writer->line(
                    $this->writer->red('  Error:   ' . $migrator->getFile() . ' - ' . $migration->title())
                );
                break;
            }
        }
        $this->writer->line('End Migration');
        $this->writer->line();

        // Create User Account
        $user = new User();
        $user->status = 'active';
        $user->role = 'developer';
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        if ($user->save()) {
            $this->writer->line($this->writer->green('Success: User Account created'));
        }

        // Create Installed File
        if (@file_put_contents(path(':storage/data/.installed'), time())) {
            $this->writer->line($this->writer->green('Success: Installation completed'));
        }

        // Thanks
        $this->writer->line();
        $this->writer->line();
        $this->writer->line($this->writer->yellow('Thanks for giving the Crate Project a try!'));
        $this->writer->line();
        $this->writer->line('You can now visit '. config('crate.url') .' and start doing awesome stuff.');
    }

    public function upgrade()
    {
        print('test');
    }

    /**
     * Measure password algorithms
     *
     * @return void
     */
    protected function measureSecurity()
    {
        $result = ['algorithms' => []];

        // Check hashing algorithms
        if (defined('PASSWORD_ARGON2ID')) {
            $result['algorithms'][] = \PASSWORD_ARGON2ID;
            $result[\PASSWORD_ARGON2ID] = [
                'memory_cost'   => \PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost'     => \PASSWORD_ARGON2_DEFAULT_TIME_COST, 
                'threads'       => \PASSWORD_ARGON2_DEFAULT_THREADS
            ];
        }
        if (defined('PASSWORD_ARGON2I')) {
            $result['algorithms'][] = \PASSWORD_ARGON2I;
            $result[\PASSWORD_ARGON2I] = [
                'memory_cost'   => \PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost'     => \PASSWORD_ARGON2_DEFAULT_TIME_COST, 
                'threads'       => \PASSWORD_ARGON2_DEFAULT_THREADS
            ];
        }
        if (defined('PASSWORD_BCRYPT')) {
            $cost = 8;
            do {
                $cost++;
                $start = microtime(true);
                password_hash("test", PASSWORD_BCRYPT, ["cost" => $cost]);
                $end = microtime(true);
            } while (($end - $start) < 0.075);

            $result['algorithms'][] = \PASSWORD_BCRYPT;
            $result[\PASSWORD_BCRYPT] = [
                'cost' => $cost
            ];
        }
        
        // Error
        if (empty($result['algorithms'])) {
            throw new RuntimeException("No valid password hash algorithm found, please make sure that Argon2 or at least BCrypt is available.");
        }

        // Check encryption libraries
        if (extension_loaded('sodium') || (function_exists('sodium_crypto_box_publickey') && (function_exists('sodium_crypto_box_seal')))) {
            $result['crypt'] = 'sodium';
        } else if (extension_loaded('openssl') || function_exists('openssl_get_cipher_methods')) {
            $ciphers = openssl_get_cipher_methods();
            if (in_array('aes-256-gcm', $ciphers)) {
                $cipher = 'aes-256-gcm';
            } else if (in_array('aes-256-ctr', $ciphers)) {
                $cipher = 'aes-256-ctr';
            } else if (in_array('aes-256-cbc', $ciphers)) {
                $cipher = 'aes-256-cbc';
            } else if (in_array('aes-128-gcm', $ciphers)) {
                $cipher = 'aes-128-gcm';
            } else if (in_array('aes-128-ctr', $ciphers)) {
                $cipher = 'aes-128-gcm';
            } else if (in_array('aes-128-cbc', $ciphers)) {
                $cipher = 'aes-128-cbc';
            } else {
                throw new RuntimeException('No supported OpenSSL Cipher found. Check the documentation for a supported list of ciphers.');
            }

            $result['crypt'] = 'openssl';
            $result['openssl'] = [
                'cipher' => $cipher
            ];
        } else {
            throw new RuntimeException('No valid encryption library found, please install either Sodium or OpenSSL.');
        }

        // Return Result
        return $result;
    }
}
