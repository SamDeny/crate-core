<?php declare(strict_types=1);

namespace Crate\Core\Database\Drivers;

use Crate\Core\Contracts\DriverContract;
use Crate\Core\Database\Drivers\Orders\DriverRequestOrder;
use Crate\Core\Exceptions\DriverException;

class PostgreSQL implements DriverContract
{
    use DriverRequestOrder;

    public function __construct()
    {
        throw new DriverException('The PostgreSQL driver is not available in this version.');
    }

}
