<?php declare(strict_types=1);

namespace Crate\Core\Commands;

use Citrus\Contracts\ConsoleCommand;

class ConfigCommand implements ConsoleCommand
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
                        'required'  => false,
                        'label'     => 'Reduces list to the environment variables',
                        'unmeet'    => 'namespace'
                    ],
                    'namespace' => [
                        'type'      => 'string',
                        'short'     => 'ns',
                        'required'  => false,
                        'label'     => 'Reduces list to the passed namespace',
                        'unmeet'    => 'env'
                    ],
                    'search'    => [
                        'type'      => 'string',
                        'required'  => false,
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
    
}
