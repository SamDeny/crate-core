<?php declare(strict_types=1);

namespace Crate\Core\Services;

use Citrus\Contracts\RuntimeInterface;
use Citrus\Events\Event;
use Citrus\Framework\Application;
use Crate\Core\Classes\ModuleRegistry;

class RuntimeService implements RuntimeInterface
{

    /**
     * Citrus Application
     *
     * @var Application
     */
    protected Application $application;

    /**
     * Module Registry
     *
     * @var ModuleRegistry
     */
    protected ModuleRegistry $registry;

    /**
     * @inheritDoc
     */
    public function __construct(Application $application)
    {
        $this->application = $application;

        // Set primary directories
        $this->application->setDirectories([
            'data'          => '$/storage/data',
            'modules'       => '$/modules',
            'uploads'       => '$/storage/uploads',
            'workspaces'    => '$/storage/workspaces'
        ]);
        
        // Register Module Registry
        $this->registry = new ModuleRegistry($this->application);
        $this->application->registerServices([
            ModuleRegistry::class   => $this->registry
        ]);
    }

    /**
     * Receive Module Registry
     *
     * @return ModuleRegistry
     */
    public function getModuleRegistry(): ModuleRegistry
    {
        return $this->registry;
    }

    /**
     * Checks if Crate is installed
     *
     * @return boolean
     */
    public function isInstalled(): bool
    {
        return file_exists($this->application->getPath('data', '.installed'));
    }

    /**
     * @inheritDoc
     */
    public function bootstrap(): void
    {
        $this->registry->init();
        $this->registry->load('@crate/core');

        // Load Other Modules
        if ($this->isinstalled()) {

        }

        // Setup Modules
        foreach ($this->registry->getModules() AS $module) {
            if (!$module['loaded']) {
                continue;
            }

            // Only inject installed plugins AND @crate/core
            $object = $module['instance'];
            if (!$module['installed'] && $object->id !== '@crate/core') {
                continue;
            }

            // Set Configurations
            $this->application->getConfigurator()->setConfigurations(
                $object->configs()
            );

            // Set Factories
            $this->application->registerFactories(
                $object->factories()
            );

            // Set Services
            $this->application->registerServices(
                $object->services()
            );

            // Insert Console Commands
            //@todo
            //$this->application->registerCommands(
            //    $module->commands()
            //);
        }
    }

    /**
     * @inheritDoc
     */
    public function beforeFinish(): void
    {

    }

    /**
     * @inheritDoc
     */
    public function afterFinish(): void
    {
        
    }

}
