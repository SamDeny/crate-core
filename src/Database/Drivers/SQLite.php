<?php declare(strict_types=1);

namespace Crate\Core\Database\Drivers;

use Crate\Core\Contracts\DriverContract;
use Crate\Core\Database\Drivers\Orders\DriverRequestOrder;
use Crate\Core\Database\Scheme;
use Crate\Core\Exceptions\DriverException;
use SQLite3;

class SQLite implements DriverContract
{
    use DriverRequestOrder;

    /**
     * Current SQLite3 database instance
     *
     * @var ?SQLite3
     */
    protected ?SQLite3 $connection = null;

    /**
     * SQLite3 database path
     *
     * @var string
     */
    protected string $path;

    /**
     * SQLite3 encryption key
     *
     * @var string|null
     */
    protected ?string $encryptionKey;

    /**
     * Applied PRAGMAs
     *
     * @var array
     */
    protected array $pragmas;

    /**
     * In-Transaction switch.
     *
     * @var boolean
     */
    protected bool $transaction = false;

    /**
     * Create a new SQLite driver instance.
     *
     * @param string $path
     * @param ?string $encryptionKey
     * @param array $pragmas
     */
    public function __construct(string $path, ?string $encryptionKey = null, array $pragmas = [])
    {
        $this->path = path($path);
        $this->encryptionKey = $encryptionKey;
        $this->pragmas = $pragmas;
        $this->connect();
    }

    /**
     * Clear current SQLite driver instance.
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Connect to the SQLite database.
     *
     * @return void
     */
    public function connect()
    {
        $flags = \SQLITE3_OPEN_CREATE | \SQLITE3_OPEN_READWRITE;

        if ($this->encryptionKey) {
            $this->connection = new SQLite3($this->path, $flags, $this->encryptionKey);
        } else {
            $this->connection = new SQLite3($this->path, $flags);
        }

        foreach ($this->pragmas AS $pragma => $value) {
            if (is_bool($value)) {
                $value = $value? 'yes': 'no';
            }
            $this->connection->exec("PRAGMA $pragma=$value;");
        }
    }

    /**
     * Disconnect from the SQLite database.
     *
     * @return void
     */
    public function disconnect()
    {
        if ($this->connection) {
            $this->connection->close();
            $this->connection = null;
        }
    }

    /**
     * Get the connection to the SQLite database or connect first, if needed.
     *
     * @return SQLite3
     */
    public function getConnection(): SQLite3
    {
        if (!$this->connection) {
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * Migrate a Scheme
     *
     * @return boolean
     */
    public function migrate(Scheme $scheme): bool
    {
        if ($scheme->action() === 'create') {
            $created_at = $scheme->created_at;
            $updated_at = $scheme->updated_at;

            // Pass Fields
            $fields = [];
            $fields['uuid'] = "'uuid' TEXT PRIMARY KEY CHECK(length(\"uuid\") == 36)";
            foreach ($scheme->properties() AS $name => $property) {
                if ($name === 'uuid' || $name === $created_at || $name === $updated_at) {
                    continue;
                }
                $constraints = [];

                // Evaluate Type
                if ($property->type === 'integer') {
                    $type = 'INTEGER';
                } else if ($property->type === 'double') {
                    $type = 'REAL';
                } else {
                    $type = 'TEXT';
                }

                // Required Constraint
                if (isset($property->required) && $property->required === true) {
                    $constraints[] = 'NOT NULL';
                } else {
                    $constraints[] = 'NULL';
                }

                // Unique Constraint
                if ($property->unique) {
                    $constraints[] = "UNIQUE";
                }

                // Default Constraint
                if ($property->default) {
                    $constraints[] = "DEFAULT '$property->default'";
                }

                // Unsigned Constraint
                if ($property->type === 'integer' ||$property->type === 'double') {
                    if (isset($property->min) && isset($property->max)) {
                        $constraints[] = "CHECK(\"$property->name\" >= $property->min AND \"$property->name\" <= $property->max)";
                    } else if (isset($property->min)) {
                        $constraints[] = "CHECK(\"$property->name\" >= $property->min)";
                    } else if (isset($property->max)) {
                        $constraints[] = "CHECK(\"$property->name\" <= $property->max)";
                    } else if (isset($property->unsigned)) {
                        $constraints[] = "CHECK(\"$property->name\" >= 0)";
                    }
                } else if ($property->type === 'string') {
                    if (isset($property->minLength) && isset($property->maxLength)) {
                        $constraints[] = "CHECK(length(\"$property->name\") >= $property->minLength AND length(\"$property->name\") <= $property->maxLength)";
                    } else if (isset($property->length)) {
                        $constraints[] = "CHECK(length(\"$property->name\") == $property->length)";
                    } else if (is_array($property->enum)) {
                        $constraints[] = "CHECK(\"$property->name\" IN ['". implode("', '", $property->enum) ."'])";
                    }
                }

                // Add Field
                $fields[$property->name] = trim("\"{$property->name}\" $type " . implode(' ', $constraints));
            }
            if ($created_at) {
                $fields[$created_at] = "'$created_at' TEXT DEFAULT (DATETIME('NOW'))";
            }
            if ($updated_at) {
                $fields[$updated_at] = "'$updated_at' TEXT NULL";
            }

            // Build Query
            if (!$updated_at) {
                $query = sprintf(
                    "CREATE TABLE IF NOT EXISTS %s (\n  %s\n);\n",
                    $scheme->scheme(), 
                    implode(",\n  ", $fields)
                );
            } else {
                $query = sprintf(
                    "CREATE TABLE IF NOT EXISTS %1\$s (\n  %2\$s\n);\n" .
                    "CREATE TRIGGER %1\$s_%3\$s AFTER UPDATE ON %1\$s\n" .
                    "  BEGIN\n" .
                    "    UPDATE %1\$s SET %3\$s = DATETIME('NOW') WHERE uuid = NEW.uuid;\n" .
                    "  END;",
                    $scheme->scheme(), 
                    implode(",\n  ", $fields),
                    $updated_at
                );
            }

            // Execute Query
            $result = @$this->getConnection()->exec($query);
            $this->lastSQL = $query;
            if (!$result) {
                $this->lastRequest = [
                    'errorCode'     => $this->getConnection()->lastErrorCode(),
                    'errorMessage'  => $this->getConnection()->lastErrorMsg(),
                    'insertIds'     => [],
                    'result'        => false,
                ];
                return false;
            } else {
                $this->lastRequest = [
                    'errorCode'     => 0,
                    'errorMessage'  => null,
                    'insertIds'     => [],
                    'result'        => true,
                ];
                return true;
            }
        } else if ($scheme->action() === 'update') {

        } else if ($scheme->action() === 'delete') {
            
        }

        return false;
    }

    public function select()
    {
        //@todo

        $connection = $this->getConnection();

        $results = $connection->query('SELECT * FROM migrations;');
        dump($results->fetchArray());

    }

    /**
     * Insert Data to the passed database table, works all or nothing.
     *
     * @param string $table
     * @param array $rows
     * @return int
     */
    public function insert(string $table, array $rows): int
    {
        if (!array_is_list($rows)) {
            $rows = [$rows];
        }
        $connection = $this->getConnection();
        
        // Prepare Query
        $columns = [];
        foreach ($rows[0] AS $key => $value) {
            $columns[$key] = ":param_$key";
        }

        $query  = "INSERT INTO $table ";
        $query .= "(". implode(', ', array_keys($columns)) .") VALUES ";
        $query .= "(". implode(', ', array_values($columns)) .");";

        // Execute prepared statement
        $ids = [];
        $error = false;
        $this->begin();
        if (($stmt = $connection->prepare($query)) !== false) {
            foreach ($columns AS $key => $prep) {
                $ref = 'param_' . $key;
                $stmt->bindParam($prep, $$ref);
            }

            // Loop rows and execute each single one.
            foreach ($rows AS $row) {
                foreach ($row AS $key => $value) {
                    $ref = 'param_' . $key;
                    $$ref = $value;
                }
            
                if (($result = $stmt->execute()) === false) {
                    $error = true;
                    break;
                } else {
                    $ids[] = $connection->lastInsertRowID();
                }
            }
        } else {
            $error = true;
        }

        // Finish
        $this->lastSQL = $stmt? $stmt->getSQL(): $query;
        if ($error) {
            $this->lastRequest = [
                'errorCode'     => $connection->lastErrorCode(),
                'errorMessage'  => $connection->lastErrorMsg(),
                'insertIds'     => [],
                'result'        => false,
            ];
            $this->rollback();
        } else {
            $this->lastRequest = [
                'errorCode'     => 0,
                'errorMessage'  => null,
                'insertIds'     => $ids,
                'result'        => $result,
            ];
            $this->commit();
        }

        // Return affected row counter
        return count($ids);
    }

    public function update()
    {
        //@todo
    }

    public function delete()
    {
        //@todo
    }

    /**
     * Begin a new Transaction
     *
     * @return void
     */
    public function begin(): void
    {
        if ($this->transaction) {
            $this->rollback();
            throw new DriverException('Multiple transactions on the same connection are not supported.');
        }
        $this->getConnection()->exec('BEGIN;');
        $this->transaction = true;
    }

    /**
     * Commit an existing Transaction
     *
     * @return void
     */
    public function commit(): void
    {
        if (!$this->transaction) {
            throw new DriverException('No transaction available to commit.');
        }
        $this->getConnection()->exec('COMMIT;');
        $this->transaction = false;
    }

    /**
     * Rollback an existing Transaction
     *
     * @return void
     */
    public function rollback(): void
    {
        if (!$this->transaction) {
            throw new DriverException('No transaction available to rollback.');
        }
        $this->getConnection()->exec('ROLLBAR;');
        $this->transaction = false;
    }

    /**
     * Check if transaction has been started.
     *
     * @return boolean
     */
    public function inTransaction(): bool
    {
        return $this->transaction;
    }

}
