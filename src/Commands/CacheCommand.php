<?php declare(strict_types=1);

namespace Crate\Core\Commands;

use Citrus\Console\Console;
use Citrus\Console\Writer;
use Citrus\Contracts\ConsoleCommand;

class CacheCommand implements ConsoleCommand
{

    /**
     * Describe the command including all available methods and arguments.
     *
     * @return array
     */
    static public function describe(): array
    {
        return [
            'cache:clear'   => [
                'label' => 'Clears cache and temp storages',
                'args'  => [
                    'cache' => [
                        'type'      => 'boolean',
                        'short'     => 'c',
                        'required'  => false,
                        'label'     => 'Explicitly clear the cache storage'
                    ],
                    'temp'  => [
                        'type'      => 'boolean',
                        'short'     => 't',
                        'required'  => false,
                        'label'     => 'Explicitly clear the tmp storage'
                    ],
                    'template'  => [
                        'type'      => 'boolean',
                        'short'     => 'd',
                        'required'  => false,
                        'label'     => 'Explicitly clear the template storage'
                    ]
                ]
            ],
            'cache:size'    => [
                'label' => 'Shows size of cache and temp storages',
                'args'  => [
                    'cache' => [
                        'type'      => 'boolean',
                        'short'     => 'c',
                        'required'  => false,
                        'label'     => 'Explicitly measure the cache storage'
                    ],
                    'temp'  => [
                        'type'      => 'boolean',
                        'short'     => 't',
                        'required'  => false,
                        'label'     => 'Explicitly measure the tmp storage'
                    ],
                    'template'  => [
                        'type'      => 'boolean',
                        'short'     => 'd',
                        'required'  => false,
                        'label'     => 'Explicitly measure the template storage'
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

    /**
     * Command Method - Clear Cache
     *
     * @return void
     */
    public function clear(array $params)
    {

    }


    /**
     * Command Method - Get Cache Size
     *
     * @return void
     */
    public function size(array $params)
    {

    }

}
