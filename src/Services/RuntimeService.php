<?php declare(strict_types=1);

namespace Crate\Core\Services;

use Citrus\Contracts\Runtime;
use Citrus\Framework\Application;
use Crate\Core\Classes\ModuleRegistry;

class RuntimeService implements Runtime
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
     * @inheritDoc
     */
    public function init(): void
    {
        $this->registry->init();
        $this->registry->load('@crate/core');

        // Register Module Registry

        // Load Additional Modules
    }

    /**
     * @inheritDoc
     */
    public function finish(): void
    {
    }

}
