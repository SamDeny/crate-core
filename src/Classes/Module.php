<?php declare(strict_types=1);

namespace Crate\Core\Classes;

use Crate\Core\Router\Collector;
use Crate\Core\Parser\INIParser;
use Crate\Core\Parser\JSONParser;
use Crate\Core\Parser\YAMLParser;
use DirectoryIterator;
use LimeExtra\Helper\YAML;

class Module 
{

    /**
     * Module Root Path
     *
     * @var string
     */
    protected string $root;

    /**
     * Unique Module ID
     *
     * @var string
     */
    protected string $id;

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
    protected array $config = [];

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
     * @param string $root
     * @param string $id
     * @param array $data
     */
    public function __construct(string $root, string $id, array $data)
    {
        $this->root = $root;
        $this->id = $id;
        $this->data = $data;
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
     * Cache the Module Data
     * @internal This function is only supposed to be used internally be the ModuleRegistry.
     * 
     * @return void
     */
    public function cacheModule()
    {

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
    public function configurable(string $filepath, ?string $alias = null)
    {
        $configpath = $this->root . DIRECTORY_SEPARATOR . $filepath;
        
        // Loop directory
        if (is_dir($configpath)) {
            $handle = new DirectoryIterator($configpath);
            foreach ($handle AS $file) {
                if (!$file->isFile()) {
                    continue;;
                }
                $this->configurable(
                    $filepath . DIRECTORY_SEPARATOR . $file->getBasename(),
                    substr($file->getFilename(), 0, -strlen($file->getExtension())-1)
                );
            }
            return;
        }

        // Set Format & Alias
        $format = pathinfo($filepath, PATHINFO_EXTENSION);
        if (empty($alias)) {
            $alias = substr(basename($filepath), 0, -strlen($format)-1);
        }

        // Load File
        $config = [];
        switch ($format) {
            case 'php':
                $config = include $configpath;
                break;
            case 'json':
                $config = (new JSONParser)->parseFile($configpath);
                break;
            case 'yaml':
                $config = (new YAMLParser)->parseFile($configpath);
                break;
            case 'ini':
                $config = (new INIParser)->parseFile($configpath);
                break;
        }

        // Set Configuration
        $this->config[$alias] = $config;
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

}
