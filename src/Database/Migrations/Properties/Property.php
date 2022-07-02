<?php declare(strict_types=1);

namespace Crate\Core\Database\Migrations\Properties;

abstract class Property
{

    /**
     * Create a new Scheme Property
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Magic Getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            return null;
        }
    }

    /**
     * Magic Isset
     *
     * @param string $name
     * @return boolean
     */
    public function __isset(string $name): bool
    {
        return property_exists($this, $name) && $this->$name !== null;
    }

    /**
     * Set Property Format
     *
     * @param string $format
     * @return static
     */
    public function format(string $format): static
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Set Property default value
     *
     * @param mixed $default
     * @return static
     */
    public function default(mixed $default): static
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Set Property required state to true
     *
     * @return static
     */
    public function required(): static
    {
        $this->required = true;
        return $this;
    }

    /**
     * Set Property required state to false
     *
     * @return static
     */
    public function optional(): static
    {
        $this->required = false;
        return $this;
    }

}
