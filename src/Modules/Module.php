<?php declare(strict_types=1);

namespace Crate\Core\Modules;

use Citrus\Console\Console;
use Citrus\Framework\Application;
use Crate\Core\Exceptions\CrateException;
use Crate\Core\Parser\INIParser;
use Crate\Core\Parser\JSONParser;
use Crate\Core\Parser\YAMLParser;
use DirectoryIterator;

class Module 
{

    /**
     * Unique Module ID
     *
     * @var string
     */
    protected string $id;

    /**
     * Module Root Path
     *
     * @var string
     */
    protected string $root;

    /**
     * Module Data array
     *
     * @var array
     */
    protected array $data;

    /**
     * Module Configuration array
     *
     * @var array
     */
    protected array $configs = [];

    /**
     * Module Factories array
     *
     * @var array
     */
    protected array $factories = [];

    /**
     * Module Services array
     *
     * @var array
     */
    protected array $services = [];

    /**
     * Module Commands array
     *
     * @var array
     */
    protected array $commands = [];

    /**
     * Module Routes data
     *
     * @var mixed
     */
    protected mixed $routes = null;

    /**
     * Module Cache State
     *
     * @var string
     */
    protected bool $cache = true;

    /**
     * Create a new Module
     *
     * @param string $id
     * @param string $root
     * @param array $datra
     */
    public function __construct(string $id, string $root, array $data)
    {
        $this->root = $root . DIRECTORY_SEPARATOR;
        $this->id = $id;
        $this->data = $data;
    }

    /**
     * Bootstrap Module
     * 
     * @return void
     */
    public function bootstrap(): void
    {
        /** @var \Citrus\Framework\Configurator */
        $config = citrus()->getConfigurator();
        array_walk($this->configs, fn($configs, $alias) => $config->setConfiguration($alias, $configs));

        // Register Console Commands
        array_walk($this->commands, fn($command) => Console::registerCommands($command));

        // Register Routes
        if ($this->routes) {
            include $this->routes;
        }

    }

    /**
     * Magic Getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            if (array_key_exists($name, $this->data)) {
                return $this->data[$name];
            } else {
                return null;
            }
        }
    }

    /**
     * Magic Setter
     *
     * @param string $name
     * @param mixed $value
     * @return void
     * @throws InvalidArgumentException The passed value for the Module data must be an array.
     * @throws InvalidArgumentException The passed Module key does not exist or cannot be set.
     */
    public function __set($name, $value)
    {
        if ($name === 'data') {
            if (!is_array($value)) {
                throw new \InvalidArgumentException('The passed value for the Module data must be an array.');
            }
            $this->data = array_merge($this->data, $value);
        } else if ($name === 'cache') {
            $this->cache = !!$value;
        } else {
            throw new \InvalidArgumentException('The passed Module key does not exist or cannot be set.');
        }
    }

    /**
     * Register a Configuration directory or specific file.
     *
     * @param string $filepath The filepath to the config directory or to the
     *               specific file to load.
     * @param string|null $alias The alias when filepath points to a specific
     *                    config file, otherwise null.
     * @return void
     */
    public function configure(string $filepath, ?string $alias = null)
    {
        $filepath = path(':modules', $this->id, $filepath);
        if (!file_exists($filepath)) {
            throw new CrateException("The passed configuration path '$filepath' does not exist.");
        }

        if (is_dir($filepath)) {
            $handle = opendir($filepath);
            while(($file = readdir($handle)) !== false) {
                if (!is_file($filepath . DIRECTORY_SEPARATOR . $file)) {
                    continue;
                }

                $ext = pathinfo($file, \PATHINFO_EXTENSION);
                $alias = substr($file, 0, -strlen($ext)-1);

                $this->configs[$alias] = citrus()->getConfigurator()->parseConfiguration(
                    $filepath . DIRECTORY_SEPARATOR . $file, 
                    $ext === 'conf'? 'ini': $ext
                );
            }
            closedir($handle); 
        } else {
            $this->configs[$alias] = $filepath;
        }
    }

    /**
     * Receive or Register Plain Configurations
     *
     * @param ?array $configs An array with all alias => config pairs to set 
     *               or nothing to receive the currently available configs.
     * @return array|void
     */
    public function configs(array $configs = null)
    {
        if ($configs === null) {
            return $this->config;
        } else {
            $this->configs = array_merge($this->configs, $configs);
        }
    }

    /**
     * Receive or Register Citrus Factories.
     *
     * @param ?array $factories An array with all alias => factory pairs to set 
     *               or nothing to receive the currently available factories.
     * @return array|void
     */
    public function factories(array $factories = null)
    {
        if ($factories === null) {
            return $this->factories;
        } else {
            $this->factories = array_merge($this->factories, $factories);
        }
    }

    /**
     * Receive or Register Citrus Services.
     *
     * @param ?array $factories An array with all alias => service pairs to set 
     *               or nothing to receive the currently available services.
     * @return array|void
     */
    public function services(array $services = null)
    {
        if ($services === null) {
            return $this->services;
        } else {
            $this->services = array_merge($this->services, $services);
        }
    }

    /**
     * Receive or Register Citrus Commands.
     *
     * @param ?array $factories An array with all alias => command pairs to set 
     *               or nothing to receive the currently available commands.
     * @return array|void
     */
    public function commands(array $commands = null)
    {
        if ($commands === null) {
            return $this->commands;
        } else {
            $this->commands = array_merge($this->commands, $commands);
        }
    }

    /**
     * Register Module Routes.
     *
     * @param null|string|\Closure $routes
     * @return void
     */
    public function routes(null|string|\Closure $routes = null)
    {
        if (is_null($routes)) {
            return $this->routes;
        } else {
            if (is_string($routes)) {
                //@todo Add Tarnished observer for routes
                $this->routes = $this->root . DIRECTORY_SEPARATOR . $routes;
            } else {
                $this->routes = $routes;
            }
        }
    }

    /**
     * Receive Module Version without v or status. (Use $module->version to receive the raw version).
     *
     * @return string
     */
    public function getVersion(): string
    {
        $version = $this->data['version'] ?? '0.1.0';

        if (strpos($version, 'v') === 0) {
            $version = substr($version, 1);
        }

        if (($index = strpos($version, '-')) !== false) {
            $version = substr($version, 0, $index);
        }

        return $version;
    }

    /**
     * Receive Module Status.
     *
     * @return string
     */
    public function getStatus(): string
    {
        if (!isset($this->data['status'])) {
            $version = $this->data['version'] ?? '0.1.0';

            if (($index = strpos($version, '-')) !== false) {
                $status = substr($version, $index+1);
            } else {
                $status = 'stable';
            }

            // Remove version behind state (ex. beta3 == beta)
            $status = preg_replace('/[^a-z-]+/', '', strtolower($status));
            $this->data['status'] = $status;
        }

        return $this->data['status'];
    }

    /**
     * Check if the Module is "stable".
     *
     * @return boolean
     */
    public function isStable(): bool
    {
        return $this->getStatus() === 'stable'
            || $this->getStatus() === 'patch'
            || $this->getStatus() === 'p';
    }

    /**
     * Check if the Module is "beta".
     *
     * @return boolean
     */
    public function isBeta(): bool
    {
        return $this->getStatus() === 'beta'
            || $this->getStatus() === 'b'
            || $this->getStatus() === 'rc';         // Release-Candidates are seen as Beta-Versions
    }

    /**
     * Check if the Module is "alpha".
     *
     * @return boolean
     */
    public function isAlpha(): bool
    {
        return $this->getStatus() === 'alpha' 
            || $this->getStatus() === 'a'
            || $this->getStatus() === 'dev';        // Development-Builds are seen as Alpha-Versions
    }

    /**
     * Get Path within Module directory.
     *
     * @param string $path
     * @return string
     */
    public function getPath(string $path): string
    {
        return $this->root . $path;
    }

}