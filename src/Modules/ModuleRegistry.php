<?php declare(strict_types=1);

namespace Crate\Core\Modules;

use Citrus\Framework\Application;
use Citrus\Contracts\SingletonContract;
use Citrus\FileSystem\Parser\JSONParser;
use Citrus\Tarnished\TarnishedCollection;
use Citrus\Tarnished\TarnishedPool;
use Citrus\Tarnished\Tarnisher;
use Crate\Core\Exceptions\CrateException;
use Ds\Map;
use Ds\Set;

class ModuleRegistry implements SingletonContract
{

    /**
     * Citrus Application
     *
     * @var Application
     */
    protected Application $app;

    /**
     * Module Root Path
     *
     * @var string
     */
    protected string $root;

    /**
     * Available Module instances.
     *
     * @var Map
     */
    protected Map $modules;

    /**
     * Module Caching system.
     *
     * @var TarnishedCollection
     */
    protected TarnishedCollection $cache;

    /**
     * Currently loaded Module
     *
     * @var ?Module
     */
    private ?Module $current = null;

    /**
     * Create new Module Registry.
     *
     * @param Application $application
     * @throws CrateException The module path does not exist!
     */
    public function __construct(Application $citrus)
    {
        if (($root = realpath($citrus->resolvePath(':modules'))) === false) {
            throw new CrateException('The module path does not exist!');
        }

        $this->app = $citrus;
        $this->root = $root;
        $this->cache = $citrus->make(TarnishedPool::class, [
            $citrus->resolvePath(':cache')
        ])->getCollection('modules', 0x03);
        $this->modules = new Map;
    }

    /**
     * Get all available Modules.
     *
     * @param bool $toArray
     * @return Map|Module[]
     */
    public function getModules(bool $toArray = false): Map|array
    {
        return $toArray? $this->modules->toArray(): $this->modules;
    }

    /**
     * Get all installed Modules.
     *
     * @return Module[]
     */
    public function getInstalledModules(): array
    {
    }

    /**
     * Get a specific module.
     *
     * @param string $id
     * @return ?Module
     */
    public function getModule(string $id): ?Module
    {
        if ($this->modules->hasKey($id)) {
            return $this->modules->get($id);
        } else {
            return null;
        }
    }

    /**
     * Check if module is installed.
     *
     * @param string $id
     * @return boolean
     */
    public function isInstalled(string $id): bool
    {

    }

    /**
     * Check if a module is enabled.
     *
     * @param string $id
     * @return boolean
     */
    public function isEnabled(string $id): bool
    {

    }

    /**
     * Check if a module is disabled.
     *
     * @param string $id
     * @return boolean
     */
    public function isDisabled(string $id): bool
    {

    }

    /**
     * Iterates through the module namespaces and folders.
     *
     * @param string $base The base directory to iterate over.
     * @return Generator
     */
    private function iterate($base = ''): \Generator
    {
        $root = $this->root . (empty($base)? '': DIRECTORY_SEPARATOR . $base);
        
        $handle = opendir($root);
        while (($item = readdir($handle)) !== false) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $path = $root . DIRECTORY_SEPARATOR . $item;
            if (is_file($path)) {
                continue;
            }

            if (strpos($item, '@') === 0) {
                foreach ($this->iterate($item) AS $result) {
                    yield [$item, $result[1], $result[2]];
                }
            } else {
                yield ['local', $item, $path];
            }
        }
        closedir($handle);
    }

    /**
     * Iniitalize available modules
     *
     * @return void
     */
    public function init()
    {
        foreach($this->iterate() AS $data) {
            [$namespace, $module, $path] = $data;
            $module_id = $namespace . '/' . $module;

            // Check module.php file
            $filepath = $path . DIRECTORY_SEPARATOR . 'module.php';
            if (!file_exists($filepath) || !is_file(($filepath))) {
                //@todo $this->logger();
                continue;
            }

            // Initial module from cache or fresh
            $module = $this->cache->receive(
                $module_id, 
                fn(...$args) => $this->load(...$args), 
                [$module_id, dirname($filepath)]
            );
            $this->modules->put($module_id, $module);
        }

        if ($this->cache->hasChanged()) {
            $this->cache->write();
        }
    }

    /**
     * Load specific module
     * 
     * @param string $id The specific module id to load.
     * @return void
     * @throws CrateException The passed module id '%s' does not exist.
     * @throws CrateException The passed module '%s' has already been loaded.
     */
    private function load(Tarnisher $tarnisher, string $id, string $root)
    {
        if (file_exists($root . DIRECTORY_SEPARATOR . 'composer.json')) {
            $data = JSONParser::parseFile($root . DIRECTORY_SEPARATOR . 'composer.json');
        }

        // Create Module
        $this->current = new Module($id, $root, $data ?? []);

        // Observe Files
        $tarnisher->observe($root . DIRECTORY_SEPARATOR . 'config');
        $tarnisher->observe($root . DIRECTORY_SEPARATOR . 'composer.json');
        $tarnisher->observe($root . DIRECTORY_SEPARATOR . 'module.php');
        $tarnisher->observe($root . DIRECTORY_SEPARATOR . 'router.php');

        // Require module file
        require_once $root . DIRECTORY_SEPARATOR . 'module.php';

        // Collect Data
        $module = $tarnisher->collect($this->current);
        $this->current = null;

        // Return Module
        return $module;
    }

    /**
     * Register a new Module, used by the module() function.
     * @internal This function is only supposed to be used internally be the module() function.
     *
     * @param \Closure $callback
     * @return void
     * @throws CrateException No module available, this method should only be called by the module() function.
     * @throws CrateException The composer file for the module '%s' is invalid. Error: %s.
     */
    public function register(\Closure $callback): void
    {
        if (!$this->current) {
            throw new CrateException('No module available, this method should only be called by the module() function.');
        }

        call_user_func($callback, $this->current);

        if ($this->current->autoDetect) {

        }
    }

    /**
     * Bootstraps a Plugin
     *
     * @param string $module_id
     * @return void
     */
    public function bootstrap(string $module_id): void
    {
        $this->modules[$module_id]->bootstrap();
    }

}
