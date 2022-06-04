<?php declare(strict_types=1);

namespace Crate\Core\Classes;

use Crate\Core\Http\RouteCollector;

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
     * @param string $filepath
     * @param string|null $alias
     * @return void
     */
    public function configurable(string $filepath, ?string $alias = null)
    {

    }

    /**
     * Register Citrus Factories.
     *
     * @param array $factories
     * @return void
     */
    public function factories(array $factories)
    {

    }

    /**
     * Register Citrus Services.
     *
     * @param array $services
     * @return void
     */
    public function services(array $services)
    {

    }

    /**
     * Register Citrus Commands.
     *
     * @param array $services
     * @return void
     */
    public function commands(array $commands)
    {

    }

    /**
     * Register Module Routes.
     *
     * @param string|\Closure $routes
     * @return void
     */
    public function routes(string|\Closure $routes)
    {
        if (is_string($routes)) {
            $path = $this->root . DIRECTORY_SEPARATOR . $routes;
            citrus(function(RouteCollector $collector) use ($path) {
                require_once $path;
            });
        } else {
            
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
