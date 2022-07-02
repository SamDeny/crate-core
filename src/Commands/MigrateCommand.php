<?php declare(strict_types=1);

namespace Crate\Core\Commands;

use Citrus\Console\Console;
use Citrus\Console\Writer;
use Citrus\Contracts\CommandContract;
use Crate\Core\Database\Migrations;

class MigrateCommand implements CommandContract
{

    /**
     * Describe the command including all available methods and arguments.
     *
     * @return array
     */
    static public function describe(): array
    {
        return [
            'migrate:execute' => [
                'label' => 'Execute uninstall migration files',
                'args'  => [
                    'module' => [
                        'type'      => 'string',
                        'short'     => 'm',
                        'label'     => 'Migrate a specific module'
                    ]
                ]
            ],
            'migrate:rollback' => [
                'label' => 'Rollback the last x migration files',
                'args'  => [
                    'module' => [
                        'type'      => 'string',
                        'required'  => true,
                        'label'     => 'Number of migrations to rollback'
                    ],
                    'module' => [
                        'type'      => 'string',
                        'short'     => 'm',
                        'label'     => 'Rollback a specific module'
                    ]
                ]
            ]
        ];
    }

    /**
     * Create a new Command instance.
     *
     * @param Console $console The main Console instance.
     * @param Writer $writer The main Writer instance.
     */
    public function __construct(Console $console, Writer $writer)
    {
        $this->console = $console;
        $this->writer = $writer;
    }

    public function index()
    {
        //@todo
    }

    /**
     * Execute Migration files
     *
     * @param array $params
     * @return void
     */
    public function execute(array $params)
    {
        $migrations = new Migrations;

        dump($migrations->scan());
        
    }

    /**
     * Rollback x migration files
     *
     * @param array $params
     * @return void
     */
    public function rollback(array $params)
    {

    }
    
}
