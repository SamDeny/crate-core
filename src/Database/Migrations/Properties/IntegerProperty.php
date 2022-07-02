<?php declare(strict_types=1);

namespace Crate\Core\Database\Migrations\Properties;

class IntegerProperty extends NumberProperty
{

    /**
     * Create a new Scheme IntegerProperty
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'integer');
    }

}
