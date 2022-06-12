<?php declare(strict_types=1);

namespace Crate\Core\Database\Drivers;

use Crate\Core\Contracts\DriverInterface;
use Crate\Core\Exceptions\DriverException;

class PostgreSQL implements DriverInterface
{

    public function __construct()
    {
        throw new DriverException('The PostgreSQL driver is not available in this version.');
    }

}
