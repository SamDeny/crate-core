<?php declare(strict_types=1);

namespace Crate\Factories;

use Citrus\Concerns\FactoryConcern;
use Crate\Mailer\Mailer;

class MailerFactory extends FactoryConcern
{

    /**
     * Make a new or return an existing Mailer instance
     *
     * @param string $id
     * @param array $arguments
     * @return Mailer
     */
    public function make(string $class, ...$arguments): Mailer
    {
        $driver = count($arguments) > 0? $arguments[0]: config('mailer.default');
        $config = config('mailer.drivers.' . $driver);

        return new $class();
    }

}
