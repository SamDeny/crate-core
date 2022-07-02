<?php declare(strict_types=1);

namespace Crate\Core\Database;

use Crate\Core\Database\Migrations\Properties\ArrayProperty;
use Crate\Core\Database\Migrations\Properties\DoubleProperty;
use Crate\Core\Database\Migrations\Properties\IntegerProperty;
use Crate\Core\Database\Migrations\Properties\ObjectProperty;
use Crate\Core\Database\Migrations\Properties\Property;
use Crate\Core\Database\Migrations\Properties\StringProperty;
use Crate\Core\Exceptions\CrateException;

class Scheme
{

    const RESERVED_PROPERTIES = [
        'id', 
        'uid', 
        'uuid',
        'created', 
        'created_at',
        'updated', 
        'updated_at'
    ];

    const PROPERTY_TYPES = [
        'string', 
        'object', 
        'array', 
        'integer', 
        'double'
    ];

    /**
     * Desired database driver for this scheme.
     *
     * @var string
     */
    public string $driver = 'default';

    /**
     * Switch to store the scheme in Crate's scheme directory.
     * Disabling the storage will also disable the model state below.
     *
     * @var boolean
     */
    public bool $store = true;

    /**
     * Switch if this scheme is based on Crate's Model system.
     *
     * @var boolean
     */
    public bool $model = true;

    /**
     * Primary Key name used for this Scheme.
     *
     * @var string
     */
    public string $primaryKey = 'uuid';

    /**
     * Primary Key format used for this Scheme, possible values:
     *      'id'        (increment id managed by the database engine or Crate)
     *      'uid'       (unique / random-generated id managed by Crate)
     *      'uuid'      (random-based UUID managed by Crate)
     *      'uuidv4'    (v4-based UUID managed by Crate)
     *      'custom'    (custom primary key value managed by the author)
     *
     * @var string
     */
    public string $primaryKeyFormat = 'uuid';

    /**
     * Creation Date/Time Property name.
     * This field is managed by Crate and automatically available on each Model, 
     * unless this field is set to NULL, which disabled the creation.
     * 
     * @var ?string
     */
    public ?string $created = 'created_at';
    
    /**
     * Last-Update Date/Time Property name.
     * This field is managed by Crate and automatically available on each Model, 
     * unless this field is set to NULL, which disabled the creation.
     * 
     * @var ?string
     */
    public ?string $updated = 'updated_at';

    /**
     * Scheme Properties.
     *
     * @var Property[]
     */
    protected array $properties = [];

    /**
     * Scheme Unique Properties.
     *
     * @var array
     */
    protected array $uniques = [];

    /**
     * Undocumented function
     *
     * @param string $scheme
     * @param string $action
     */
    public function __construct(string $scheme, string $action)
    {
        $this->scheme = $scheme;
        $this->action = $action;
    }

    /**
     * Receive Scheme name
     *
     * @return string
     */
    public function scheme(): string
    {
        return $this->scheme;
    }

    /**
     * Receive Scheme action
     *
     * @return string
     */
    public function action(): string
    {
        return $this->action;
    }

    /**
     * Receive Scheme properties
     * 
     * @return array
     */
    public function properties(): array
    {
        return $this->properties;
    }

    /**
     * Select a Scheme Property
     *
     * @param string $name
     * @return Property
     */
    public function property(string $name): Property
    {
        if (in_array($name, Scheme::RESERVED_PROPERTIES)) {
            throw new CrateException("The property key '$name' is reserved and cannot be created.");
        }
        
        if (!array_key_exists($name, $this->properties)) {
            throw new CrateException("The property key '$name' does not exist.");
        }

        return $this->properties[$name];
    }

    /**
     * Create a new StringProperty.
     *
     * @param string $name
     * @return StringProperty
     */
    public function string(string $name): StringProperty
    {
        if (array_key_exists($name, $this->properties)) {
            throw new CrateException("The property key '$name' does already exist.");
        }

        $this->properties[$name] = new StringProperty($name);
        return $this->properties[$name];
    }

    /**
     * Create a new ObjectProperty.
     *
     * @param string $name
     * @return ObjectProperty
     */
    public function object(string $name): ObjectProperty
    {
        if (array_key_exists($name, $this->properties)) {
            throw new CrateException("The property key '$name' does already exist.");
        }

        $this->properties[$name] = new ObjectProperty($name);
        return $this->properties[$name];
    }

    /**
     * Create a new ArrayProperty.
     *
     * @param string $name
     * @return ArrayProperty
     */
    public function array(string $name): ArrayProperty
    {
        if (array_key_exists($name, $this->properties)) {
            throw new CrateException("The property key '$name' does already exist.");
        }

        $this->properties[$name] = new ArrayProperty($name);
        return $this->properties[$name];
    }

    /**
     * Create a new IntegerProperty.
     *
     * @param string $name
     * @return IntegerProperty
     */
    public function integer(string $name): IntegerProperty
    {
        if (array_key_exists($name, $this->properties)) {
            throw new CrateException("The property key '$name' does already exist.");
        }

        $this->properties[$name] = new IntegerProperty($name);
        return $this->properties[$name];
    }

    /**
     * Create a new DoubleProperty.
     *
     * @param string $name
     * @return DoubleProperty
     */
    public function double(string $name): DoubleProperty
    {
        if (array_key_exists($name, $this->properties)) {
            throw new CrateException("The property key '$name' does already exist.");
        }

        $this->properties[$name] = new DoubleProperty($name);
        return $this->properties[$name];
    }


    /**
     * Create a new Special UUID StringProperty.
     *
     * @param string $name
     * @return StringProperty
     */
    public function uuid(string $name): StringProperty
    {
        if (array_key_exists($name, $this->properties)) {
            throw new CrateException("The property key '$name' does already exist.");
        }

        /** @var StringProperty */
        $this->properties[$name] = new StringProperty($name);
        $this->properties[$name]->format('uuid');
        $this->properties[$name]->length(36);
        return $this->properties[$name];
    }

    /**
     * Create a new Special Boolean IntegerProperty.
     *
     * @param string $name
     * @return IntegerProperty
     */
    public function boolean(string $name): IntegerProperty
    {
        if (array_key_exists($name, $this->properties)) {
            throw new CrateException("The property key '$name' does already exist.");
        }

        /** @var IntegerProperty */
        $this->properties[$name] = new IntegerProperty($name);
        $this->properties[$name]->format('boolean');
        $this->properties[$name]->unsigned();
        $this->properties[$name]->max(1);
        $this->properties[$name]->max(1);
        return $this->properties[$name];
    }

    /**
     * Create a new Special Date StringProperty.
     *
     * @param string $name
     * @return StringProperty
     */
    public function date(string $name): StringProperty
    {
        if (array_key_exists($name, $this->properties)) {
            throw new CrateException("The property key '$name' does already exist.");
        }

        $this->properties[$name] = new StringProperty($name);
        $this->properties[$name]->format('date');
        return $this->properties[$name];
    }

    /**
     * Create a new Special Time StringProperty.
     *
     * @param string $name
     * @return StringProperty
     */
    public function time(string $name): StringProperty
    {
        if (array_key_exists($name, $this->properties)) {
            throw new CrateException("The property key '$name' does already exist.");
        }

        $this->properties[$name] = new StringProperty($name);
        $this->properties[$name]->format('time');
        return $this->properties[$name];
    }

    /**
     * Create a new Special DateTime StringProperty.
     *
     * @param string $name
     * @return StringProperty
     */
    public function datetime(string $name): StringProperty
    {
        if (array_key_exists($name, $this->properties)) {
            throw new CrateException("The property key '$name' does already exist.");
        }

        $this->properties[$name] = new StringProperty($name);
        $this->properties[$name]->format('datetime');
        return $this->properties[$name];
    }

    /**
     * Sets a unique key with different properties
     *
     * @param array $properties
     * @return void
     */
    public function unique(array $properties): void
    {
        $this->uniques[] = $properties;
    }


    /**
     * Action :: Rename a Scheme field
     *
     * @param string $currentName
     * @param string $newName
     * @return void
     */
    public function rename(string $currentName, string $newName)
    {

    }

}
