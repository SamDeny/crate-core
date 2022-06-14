<?php declare(strict_types=1);

namespace Crate\Core\Commands;

use Citrus\Contracts\CommandContract;

class ModuleCommand implements CommandContract
{

    /**
     * Describe the command including all available methods and arguments.
     *
     * @return array
     */
    static public function describe(): array
    {
        return [
            'module:install' => [

            ],
            'module:uninstall' => [

            ],
            'module:enable' => [

            ],
            'module:disable' => [

            ]
        ];
    }
    
}
