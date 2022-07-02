<?php declare(strict_types=1);

namespace Crate\Core\Concerns;

use Citrus\Utilities\Str;
use Crate\Core\Database\Connection;
use Crate\Core\Factories\ConnectionFactory;
use InvalidArgumentException;

abstract class ModelConcern
{

    /**
     * Model Scheme Name
     * 
     * @var ?string
     */
    protected ?string $scheme = null;

    /**
     * Model Driver
     * 
     * @var string
     */
    protected string $driver = 'default';

    /**
     * Model UUID
     * 
     * @var ?string
     */
    protected ?string $uuid = null;

    /**
     * Model Exists
     * 
     * @var bool
     */
    protected bool $exists = false;

    /**
     * Model Attributes
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * Get Model Attribute
     *
     * @param string $name
     * @return void
     */
    public function __get(string $name)
    {
        if (method_exists($this, 'get' . ucfirst($name))) {
            return $this->{'get' . ucfirst($name)}();
        } else if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        } else {
            throw new InvalidArgumentException("The passed attribute '$name' does not exist.");
        }
    }

    /**
     * Set Model Attribute
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, mixed $value)
    {
        if (method_exists($this, 'set' . ucfirst($name))) {
            $this->{'set' . ucfirst($name)}($value);
        } else {
            $this->attributes[$name] = $value;
        }
    }

    /**
     * Save a Model
     *
     * @return bool
     */
    public function save(): bool
    {
        if ($this->scheme === null) {
            $scheme = substr(static::class, strrpos(static::class, '\\')+1);
            $scheme = strtolower($scheme);
            $scheme = str_ends_with($scheme, 's')? $scheme: $scheme."s";
        } else {
            $scheme = $this->scheme;
        }

        /** @var Connection */
        $connection = citrus(ConnectionFactory::class)->make($this->driver);
        if ($this->exists) {
            return $connection->update($scheme, $this->attributes, ['uuid' => $this->uuid]) > 0;
        } else {
            $attributes = $this->attributes;
            $attributes['uuid'] = $this->uuid = Str::uuid();
            return $connection->insert($scheme, $attributes) > 0;
        }
    }

}
