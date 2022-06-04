<?php declare(strict_types=1);

namespace Crate\Core\Commands;

use Citrus\Contracts\ConsoleCommand;

class SetupCommand implements ConsoleCommand
{

    static public function describe()
    {
        return [
            'setup' => [

            ],
            'setup:key' => [

            ],
            'setup:upgrade' => [

            ]
        ];
    }

    public function key()
    {
        print('test');
    }

}
