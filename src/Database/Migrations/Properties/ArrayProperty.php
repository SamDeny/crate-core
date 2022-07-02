<?php declare(strict_types=1);

namespace Crate\Core\Database\Migrations\Properties;

class ArrayProperty extends Property
{

    /**
     * Create a new Scheme ArrayProperty
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'array');
    }

    /**
     * Set ArrayProperty allowed values.
     *
     * @param array $values
     * @return self
     */
    public function allowed(array $values): self
    {
        $this->allowed = $values;
        return $this;
    }

}
