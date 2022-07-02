<?php declare(strict_types=1);

namespace Crate\Core\Factories;

use Citrus\Concerns\FactoryConcern;
use Citrus\Exceptions\RuntimeException;
use Crate\Core\Database\Connection;

class ConnectionFactory extends FactoryConcern
{

    /**
     * Make a new or return an existing Mailer instance
     *
     * @param string $id
     * @param array $arguments
     * @return Mailer
     */
    public function make(string $id = null, ...$arguments): Connection
    {
        if (empty($arguments)) {
            if ($id === 'crate') {
                $id = config('database.crate');
            }
            if ($id === null || $id === 'default') {
                $id = config('database.default');
            }
            $arguments = config('database.drivers.' . $id, []);
        }

        if (empty($arguments)) {
            new RuntimeException('The ConnectionFactory could not create a connection instance.');
        }

        return new Connection($arguments);
    }

}
