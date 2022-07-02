<?php declare(strict_types=1);

namespace Crate\Core\Database;

use Citrus\Utilities\Str;

abstract class Model
{

    /**
     * Query many Models using a Query instance.
     *
     * @param Query $query
     * @return static[]
     */
    static public function query(Query $query): array
    {
        $model = new static;

        /** @var Connection */
        $connection = citrus(Connection::class, $model->sys('driver'));

        // Build & Execute Query
        $result = $connection->query($model->sys('scheme'), $query);
        return array_map(fn($val) => (clone $model)->fill($val), $result);
    }

    /**
     * Find many Models by a single column => value pair. 
     *
     * @param string $column
     * @param mixed $value
     * @return static[]
     */
    static public function find(string $column, mixed $value): array
    {
        $model = new static;

        /** @var Connection */
        $connection = citrus(Connection::class, $model->sys('driver'));

        // Build & Execute Query
        $query = new Query;
        $query->where($column, 'eq', $value);
        $result = $connection->query($model->sys('scheme'), $query);
        return array_map(fn($val) => (clone $model)->fill($val), $result);
    }

    /**
     * Find one Model by a single column => value pair.
     *
     * @param string $column
     * @param mixed $value
     * @return ?static
     */
    static public function findOne(string $column, mixed $value): ?static
    {
        $model = new static;

        /** @var Connection */
        $connection = citrus(Connection::class, $model->sys('driver'));

        // Build & Execute Query
        $query = new Query;
        $query->where($column, 'eq', $value);
        $query->limit(1);
        $result = $connection->queryOne($model->sys('scheme'), $query);
        return $result? $model->fill($result): null;
    }

    /**
     * Get one Model by it's primary key.
     *
     * @param string $id
     * @return ?static
     */
    static public function get(string $id): ?static
    {
        $model = new static;

        /** @var Connection */
        $connection = citrus(Connection::class, $model->sys('driver'));

        // Build & Execute Query
        $query = new Query;
        $query->where($model->primaryKey, 'eq', $id);
        $query->limit(1);
        $result = $connection->queryOne($model->sys('scheme'), $query);
        return $result? $model->fill($result): null;
    }


    /**
     * Model database driver.
     *
     * @var string
     */
    protected string $driver = 'default';

    /**
     * Model scheme name.
     *
     * @var string|null
     */
    protected ?string $scheme = null;

    /**
     * Model primary key.
     *
     * @var string
     */
    protected string $primaryKey = 'uuid';

    /**
     * Switch if Model exists or not.
     *
     * @var boolean
     */
    protected bool $exists = false;

    /**
     * Switch if Model has unstored changes or not.
     *
     * @var boolean
     */
    protected bool $dirty = false;

    /**
     * Model attributes.
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * Check if Model attribute exists.
     *
     * @param string $name
     * @return boolean
     */
    public function __isset(string $name)
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * Get Model attribute data.
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        $method = 'get' . Str::pascalcase($name);

        if (method_exists($this, $method)) {
            return $this->{$method}();
        } else {
            return array_key_exists($name, $this->attributes)? $this->attributes[$name]: null;
        }
    }

    /**
     * Set Model attribute data.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $method = 'set' . Str::pascalcase($name);

        if (method_exists($this, $method)) {
            $this->{$method}($value);
        } else {
            $this->attributes[$name] = $value;
        }

        $this->dirty = true;
    }

    /**
     * Get Model system-construction value. 
     *
     * @param string $key
     * @return mixed
     */
    public function sys(string $key): mixed
    {

        // Database Driver
        if ($key === 'driver') {
            return $this->driver;
        } 
        
        // Database Table | Scheme
        else if ($key === 'scheme' || $key === 'table') {
            $value = $this->scheme || $this->table || null;

            if (is_null($value)) {
                $value = substr(static::class, strrpos(static::class, '\\')+1);
                $value = strtolower($value);
                $value = str_ends_with($value, 's')? $value: $value."s";
            }

            return $value;
        } 
        
        // Database Primary Key
        else if ($key === 'primaryKey') {
            return $this->primaryKey;
        }

        // Model exists switch
        else if ($key === 'exists') {
            return $this->exists;
        }
        
        // Model dirty switch
        else if ($key === 'dirty') {
            return $this->dirty;
        }
    }

    /**
     * Check if Model exists.
     *
     * @return boolean
     */
    public function exists(): bool
    {
        return $this->exists;
    }

    /**
     * Check if Model attributes has been changed.
     *
     * @return boolean
     */
    public function isDirty(): bool
    {
        return $this->dirty;
    }

    /**
     * Fill Attributes.
     *
     * @param array|object $values
     * @return static
     */
    public function fill(array|object $values): static
    {

        return $this;
    }

    /**
     * Save Model.
     *
     * @return boolean
     */
    public function save(): bool
    {

        if (!$this->exists) {

        } else {
            if (!$this->dirty) {
                return true;
            } else {
                
            }
        }

        return true;
    }

}
