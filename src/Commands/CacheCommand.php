<?php declare(strict_types=1);

namespace Crate\Core\Commands;

use Citrus\Console\Console;
use Citrus\Console\Writer;
use Citrus\Contracts\CommandContract;
use Citrus\Utilities\Format;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CacheCommand implements CommandContract
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
                        'label'     => 'Explicitly clear the temp storage'
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
                        'label'     => 'Explicitly measure the temp storage'
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
        $folders = 0;
        $files = 0;
        
        // Cache Directory
        if (empty($params) || ($params['cache'] ?? false)) {
            $entries = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(path(':cache'), RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach($entries AS $entry) {
                if (in_array($entry->getBasename(), ['.gitempty', '.', '..'])) {
                    continue;
                }
                if ($entry->isDir()) {
                    $folders++;
                    rmdir($entry->getRealPath());
                } else {
                    $files++;
                    unlink($entry->getRealPath());
                }
            }
        }

        // Temp Directory
        if (empty($params) || ($params['temp'] ?? false)) {
            $entries = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(path(':temp'), RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach($entries AS $entry) {
                if (in_array($entry->getBasename(), ['.gitempty', '.', '..'])) {
                    continue;
                }
                if ($entry->isDir()) {
                    $folders++;
                    rmdir($entry->getRealPath());
                } else {
                    $files++;
                    unlink($entry->getRealPath());
                }
            }
        }

        $this->writer->line(
            $this->writer->yellow('Cache Cleared') .
            sprintf(' - Removed %d files and %d folders.', $files, $folders)
        );
    }

    /**
     * Command Method - Get Cache Size
     *
     * @return void
     */
    public function size(array $params)
    {
        $rows = [];
        
        // Cache Directory
        if (empty($params) || ($params['cache'] ?? false)) {
            $files = 0;
            $folders = 0;
            $size = 0;

            // Loop through files
            $entries = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(path(':cache'), RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach($entries AS $entry) {
                if (in_array($entry->getBasename(), ['.gitempty', '.', '..'])) {
                    continue;
                }
                if ($entry->isDir()) {
                    $folders++;
                } else {
                    $files++;
                    $size += filesize($entry->getRealPath());
                }
            }

            // Modify Bytes Text
            $bytes = Format::bytes($size);
            if (str_ends_with($bytes, ' B')) {
                $bytes .= '  ';
            }
            
            // Add Row
            $rows[] = [
                str_replace('\\', '/', substr(path(':cache'), strlen(path('$'))+1)),
                $folders,
                $files,
                $bytes
            ];
        }

        // Temp Directory
        if (empty($params) || ($params['temp'] ?? false)) {
            $files = 0;
            $folders = 0;
            $size = 0;

            // Loop through files
            $entries = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(path(':temp'), RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach($entries AS $entry) {
                if (in_array($entry->getBasename(), ['.gitempty', '.', '..'])) {
                    continue;
                }
                if ($entry->isDir()) {
                    $folders++;
                } else {
                    $files++;
                    $size += filesize($entry->getRealPath());
                }
            }

            // Modify Bytes Text
            $bytes = Format::bytes($size);
            if (str_ends_with($bytes, ' B')) {
                $bytes .= '  ';
            }
            
            // Add Row
            $rows[] = [
                str_replace('\\', '/', substr(path(':temp'), strlen(path('$'))+1)),
                $folders,
                $files,
                $bytes
            ];
        }

        // Print
        $this->writer->line($this->writer->yellow('Cache Sizes'));
        $this->writer->line();
        $this->writer->table(
            [ 'Path', 'Folders', 'Files', 'Total' ],
            $rows,
            ['left', 'right', 'right', 'right']
        );
    }

}
