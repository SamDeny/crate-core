<?php declare(strict_types=1);

namespace Crate\Core\Classes;

use Citrus\Framework\Application;
use Crate\Core\Exceptions\CrateException;
use Crate\Core\Parser\JSONParser;

class ModuleRegistry 
{

    /**
     * Module Root Path
     *
     * @var string
     */
    protected string $root;

    /**
     * Available Module Sets
     *
     * @var array
     */
    protected array $modules = [];

    /**
     * Currently loaded Module
     *
     * @var string|null
     */
    protected ?string $current = null;

    /**
     * Create new Module Registry
     *
     * @param Application $application
     * @throws CrateException The module path does not exist!
     */
    public function __construct(Application $application)
    {
        if (($root = realpath($application->getPath('modules'))) === false) {
            throw new CrateException('The module path does not exist!');
        }

        $this->root = $root;
    }

    /**
     * Iterate through the module namespaces and folders.
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
                yield ['', $item, $path];
            }
        }
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

            // Check module.php file
            $filepath = $path . DIRECTORY_SEPARATOR . 'module.php';
            if (!file_exists($filepath) || !is_file(($filepath))) {
                //@todo Log 
                continue;
            }

            // Include Module
            $this->modules[$namespace . '/' . $module] = [
                'local'     => $namespace === '',
                'namespace' => $namespace,
                'module'    => $module,
                'path'      => $path,
                'loaded'    => false,
                'module'    => null
            ];
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
    public function load(string $id)
    {
        if (!array_key_exists($id, $this->modules)) {
            throw new CrateException("The passed module id '$id' does not exist.");
        }

        if ($this->modules[$id]['loaded']) {
            throw new CrateException("The passed module '$id' has already been loaded.");
        }

        // Load Modu.e
        $this->current = $id;
        require_once $this->modules[$id]['path'] . DIRECTORY_SEPARATOR . 'module.php';
        $this->current = null;
        $this->modules[$id]['loaded'] = true;
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

        // Get Module Storage
        $id = $this->current;
        $internal = &$this->modules[$id];

        // Check Cache
        //@todo

        // Load composer.json
        $composerPath = $internal['path'] . DIRECTORY_SEPARATOR . 'composer.json';
        if (file_exists($composerPath)) {
            try {
                $composer = json_decode(file_get_contents($composerPath), true);
            } catch(\Exception $e) {
                throw new CrateException("The composer file for the module '$id' is invalid. Error: " . $e->getMessage());
            }
        }

        // Load Module
        $internal['module'] = new Module($internal['path'], $id, $composer ?? []);
        call_user_func($callback, $internal['module']);
        $internal['module']->cacheModule();
    }

    /**
     * Undocumented function
     *
     * @param string $module
     * @return boolean
     */
    public function isEnabled(string $module): bool
    {
        return false;
    }

    /**
     * Undocumented function
     *
     * @param string $module
     * @return boolean
     */
    public function isDisabled(string $module): bool
    {
        return false;
    }

}
