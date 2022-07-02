<?php declare(strict_types=1);

namespace Crate\Core\Database\Migrations\Properties;

class DoubleProperty extends NumberProperty
{

    /**
     * Create a new Scheme StringProperty
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'double');
    }

}
