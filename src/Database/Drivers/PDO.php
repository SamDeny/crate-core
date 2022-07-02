<?php declare(strict_types=1);

namespace Crate\Core\Database\Drivers;

use Crate\Core\Contracts\DriverContract;
use Crate\Core\Database\Drivers\Orders\DriverRequestOrder;

class PDO implements DriverContract
{
    use DriverRequestOrder;

}
