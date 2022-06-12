<?php declare(strict_types=1);

namespace Crate\Core\Services;

use Citrus\Framework\Application;
use Citrus\Router\Router;
use Crate\Core\Contracts\RestBulkControllerContract;
use Crate\Core\Contracts\RestControllerContract;
use Crate\Core\Contracts\RestFindControllerContract;
use Crate\Core\Contracts\RestPatchControllerContract;
use Crate\Core\Modules\ModuleRegistry;

class RuntimeService
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

        // Set Router Definitions
        Router::addDefinitions([
            RestControllerContract::class => [
                ['GET',     '/',        'list'],
                ['GET',     '/{id:id}?','get'],
                ['POST',    '/',        'create'],
                ['POST',    '/{id:id}', 'update'],
                ['PUT',     '/{id:id}?','createOrUpdate'],
                ['DELETE',  '/{id:id}', 'delete'],
            ],
            RestPatchControllerContract::class => [
                ['PATCH',   '/{id:id}', 'patch']
            ],
            RestBulkControllerContract::class => [
                ['POST',    '/bulkGet', 'bulkGet'],
                [
                    ['POST','PUT','PATCH'],
                    '/bulkPost',
                    'bulkPost'
                ],
                [
                    ['POST','DELETE'],
                    '/bulkDelete',
                    'bulkDelete'
                ]
            ],
            RestFindControllerContract::class => [
                [
                    ['GET', 'POST'],
                    '/find',
                    '/find'
                ]
            ]
        ]);
        
        // Register Module Registry
        $this->registry = new ModuleRegistry($this->application);
        $this->application->getContainer()->set(ModuleRegistry::class, $this->registry);
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
        return file_exists($this->application->resolvePath(':data', '.installed'));
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
