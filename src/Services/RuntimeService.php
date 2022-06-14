<?php declare(strict_types=1);

namespace Crate\Core\Services;

use Citrus\Events\RequestEvent;
use Citrus\Framework\Application;
use Citrus\Http\Request;
use Citrus\Http\Response;
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
    protected Application $app;

    /**
     * Module Registry
     *
     * @var ModuleRegistry
     */
    protected ModuleRegistry $registry;

    /**
     * @inheritDoc
     */
    public function __construct(Application $citrus)
    {
        $this->app = $citrus;

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
        $this->registry = new ModuleRegistry($this->app);
        $this->app->getContainer()->set(ModuleRegistry::class, $this->registry);
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
        return file_exists($this->app->resolvePath(':data', '.installed'));
    }

    /**
     * @inheritDoc
     */
    public function bootstrap(): void
    {
        $this->registry->init();
        $this->registry->bootstrap('@crate/core');
        $this->registry->bootstrap('@crate/backend');

        // Load Other Modules
        if ($this->isinstalled()) {

        }
    }

    /**
     * @inheritDoc
     */
    public function finalize(): void
    {
        if (!$this->isInstalled()) {
            $this->app->getEventManager()->addListener(
                RequestEvent::class, 
                [$this, 'showInstallationMessage']
            );
        }
    }

    /**
     * Show installation Message
     *
     * @return void
     */
    public function showInstallationMessage(RequestEvent $event): void
    {
        $event->preventDefault();

        // @todo
        // Temporary Response message, when Crate is not installed yet.
        // Will be removed in a future release. 
        $response = new Response;
        $response->setHTML('<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8" />
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title>Crate CMS - Installation required</title>
                <style type="text/css">
                    *, *:before, *:after
                    {
                        box-sizing: border-box;
                    }
                    html {
                        margin: 0;
                        padding: 0;
                        font-size: 16px;
                        font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
                        background-color: #18181B;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                    }
                    a {
                        color: #312E81;
                        text-decoration: none;
                    }
                    a:hover {
                        color: #134E4A;
                    }
                    b {
                        font-weight: 600;
                    }
                    .container {
                        width: 100vw;
                        height: 100vh;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }
                    .panel {
                        width: 600px;
                        height: auto;
                        padding: 0;
                    }
                    .panel header h1 {
                        color: #F4F4F5;
                        margin: 0 0 15px 0;
                        padding: 0;
                    }
                    .panel article {
                        width: 600px;
                        height: auto;
                        padding: 25px;
                        border-radius: 5px;
                        background-color: #F4F4F5;
                    }
                    .panel article h2 {
                        margin: 30px 0 10px 0;
                        padding: 0;
                        font-size: 18px;
                        font-weight: 600;
                    }
                    .panel article h2:first-child {
                        margin-top: 0;
                    }
                    .panel article p {
                        margin: 0 0 25px 0;
                    }
                    .panel article pre {
                        color: #312E81;
                        margin: 0 0 25px 0;
                        padding: 10px 20px;
                        border-left: 2px solid #312E81;
                        background-color: #E0E7FF;
                    }
                    .panel article p:last-child {
                        margin-bottom: 0;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="panel">
                        <header>
                            <h1>Thanks for giving Crate CMS a try.</h1>
                        </header>
                        <article>
                            <h2>Installation required</h2>
                            <p>
                                The current version of <b>Crate CMS</b> does not provide a web-based installation
                                wizard, please install <b>Crate CMS</b> via the command line interface using this 
                                in the root directory (where the "citrus" file is located):
                            </p>
                            <pre><code>php citrus setup:install</code></pre>
                            <p>
                                You can also install <b>Crate CMS</b> manually, visit our website for detailed 
                                instructions: <a href="" target="_blank">https://crate.md/install</a>.
                            </p>

                            <h2>Problems using or installing <b>Crate CMS</b>?</h2>
                            <p>
                                Contact us on <a href="" target="_blank">GitHub</a>,  
                                on our <a href="" target="_blank">Forum</a> 
                                or via mail at <a href="">help@crate.md</a>
                            </p>
                        </article>
                    </div>
                </div>
            </body>
            </html>
        ');

        // Print message
        print($response);
    }

}
