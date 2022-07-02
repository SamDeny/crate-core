<?php declare(strict_types=1);

namespace Crate\Core\Database\Migrations\Properties;

class ObjectProperty extends Property
{

    /**
     * Create a new Scheme ObjectProperty
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'object');
    }

}
