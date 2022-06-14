<?php declare(strict_types=1);

namespace Crate\Core\Database\Drivers;

use Crate\Core\Contracts\DatabaseDriver;
use Crate\Core\Database\Drivers\Orders\DriverRequestOrder;

class PDO implements DatabaseDriver
{
    use DriverRequestOrder;

}
