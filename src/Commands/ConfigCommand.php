<?php declare(strict_types=1);

namespace Crate\Core\Commands;

use Citrus\Console\Console;
use Citrus\Console\Writer;
use Citrus\Contracts\CommandContract;

class ConfigCommand implements CommandContract
{

    /**
     * Describe the command including all available methods and arguments.
     *
     * @return array
     */
    static public function describe(): array
    {
        return [
            'config:list'   => [
                'label' => 'Lists all configuration keys and values',
                'args'  => [
                    'env'       => [
                        'type'      => 'boolean',
                        'label'     => 'Reduces list to the environment variables',
                        'unmeet'    => 'namespace'
                    ],
                    'namespace' => [
                        'type'      => 'string',
                        'short'     => 'ns',
                        'label'     => 'Reduces list to the passed namespace',
                        'unmeet'    => 'env'
                    ],
                    'search'    => [
                        'type'      => 'string',
                        'label'     => 'Performes a key / value search',
                        'mods'      => [
                            'keys'      => 'Searches in the configuration keys only',
                            'values'    => 'Searches in the configuration values only'
                        ]
                    ],
                ]
            ],
            'config:get'    => [
                'label' => 'Gets a configuration value'
            ],
            'config:set'    => [
                'label' => 'Sets a configuration value'
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

    /**
     * Command Method - List all configurations
     *
     * @return void
     */
    public function list(array $params)
    {
    }

    /**
     * Command Method - Get a specific configuration
     *
     * @return void
     */
    public function get(array $params)
    {

    }

    /**
     * Command Method - Set a specific configuration
     *
     * @return void
     */
    public function set(array $params)
    {

    }
    
}
