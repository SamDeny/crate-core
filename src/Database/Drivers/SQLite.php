<?php declare(strict_types=1);

namespace Crate\Core\Database\Drivers;

use Crate\Core\Concerns\DriverRequestData;
use Crate\Core\Contracts\DriverInterface;
use Crate\Core\Exceptions\DriverException;
use SQLite3;

class SQLite implements DriverInterface
{
    use DriverRequestData;

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
    protected string $filepath;

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
     * @param string $filepath
     * @param ?string $encryptionKey
     * @param array $pragmas
     */
    public function __construct(string $filepath, ?string $encryptionKey = null, array $pragmas = [])
    {
        $this->filepath = $filepath;
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
            $this->connection = new SQLite3($this->filepath, $flags, $this->encryptionKey);
        } else {
            $this->connection = new SQLite3($this->filepath, $flags);
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
        $this->connection->close();
        $this->connection = null;
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
