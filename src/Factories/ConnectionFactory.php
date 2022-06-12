<?php declare(strict_types=1);

namespace Crate\Factories;

use Citrus\Concerns\FactoryConcern;

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
        if ($id === null && empty($arguments)) {
            $id = config('mailer.default');
            $config = config('mailer.drivers.' . $id);
        }

        return new Mailer();
    }

}
