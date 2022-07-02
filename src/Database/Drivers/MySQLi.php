<?php declare(strict_types=1);

namespace Crate\Core\Database\Drivers;

use Crate\Core\Contracts\DriverContract;
use Crate\Core\Database\Drivers\Orders\DriverRequestOrder;
use Crate\Core\Exceptions\DriverException;
use MySQLi as MySQLiConnection;

class MySQLi implements DriverContract
{
    use DriverRequestOrder;

    /**
     * Current MySQLi database instance
     *
     * @var ?MySQLiConnection
     */
    protected ?MySQLiConnection $connection = null;

    /**
     * MySQLI database hostname
     *
     * @var string
     */
    protected string $host;

    /**
     * MySQLi database username
     *
     * @var string
     */
    protected string $user;

    /**
     * MySQLi database password
     *
     * @var string
     */
    protected string $pass;
    
    /**
     * MySQLi database name
     *
     * @var string
     */
    protected string $name;

    /**
     * MySQLi database port
     *
     * @var int
     */
    protected int $port;
    
    /**
     * MySQLi database socket
     *
     * @var string
     */
    protected string $socket;

    /**
     * In-Transaction switch.
     *
     * @var boolean
     */
    protected bool $transaction = false;

    /**
     * Result as boolean value of the last operation
     *
     * @var bool|null
     */
    protected bool $lastResult = null; 

    /**
     * Create a new MySQLi driver instance.
     *
     * @param string $hostname
     * @param string $username
     * @param string|null $password
     * @param string $database
     * @param integer $port
     * @param string|null $socket
     */
    public function __construct(
        string $hostname, 
        string $username,
        ?string $password, 
        string $database,
        int $port = 3306, 
        string $socket = ''
    ) {
        $this->host = $hostname;
        $this->user = $username;
        $this->pass = $password ?? '';
        $this->name = $database;
        $this->port = $port;
        $this->socket = $socket;
        $this->connect();
    }

    /**
     * Clear current MySQLi driver instance.
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Connect to the MySQLi database.
     *
     * @return void
     */
    public function connect()
    {
        $this->connection = new MySQLiConnection($this->host, $this->user, $this->pass, $this->name, $this->port, $this->socket);
    }

    /**
     * Disconnect from the MySQLi database.
     *
     * @return void
     */
    public function disconnect()
    {
        $this->connection->close();
        $this->connection = null;
    }

    /**
     * Get the connection to the MySQLi database or connect first, if needed.
     *
     * @return MySQLiConnection
     */
    public function getConnection(): MySQLiConnection
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
        $prepare = [];
        foreach ($rows[0] AS $key => $value) {
            $type = is_float($value)? 'd': (is_int($value)? 'i': 's');
            $columns[$key] = $type;
            $prepare[] = &$columns[$key];
        }

        $query  = "INSERT INTO $table ";
        $query .= "(". implode(', ', array_keys($columns)) .") VALUES ";
        $query .= "(". implode(', ', array_pad([], count($columns), '?')) .");";

        // Execute prepared statement
        $ids = [];
        $error = false;
        $this->begin();
        if (($stmt = $connection->prepare($query)) !== false) {
            $types = implode('', array_values($columns));
            $stmt->bind_param($types, ...$prepare);

            // Loop rows and execute each single one.
            foreach ($rows AS $row) {
                foreach ($row AS $key => $value) {
                    $columns[$key] = $value;
                }
            
                if (($result = $stmt->execute()) === false) {
                    $error = true;
                    break;
                } else {
                    $ids[] = $connection->insert_id;
                }
            }
        } else {
            $error = true;
        }

        // Finish
        $this->lastSQL = $query;
        if ($error) {
            $this->lastRequest = [
                'errorCode'     => $connection->errno,
                'errorMessage'  => $connection->error,
                'insertIds'     => [],
                'result'        => false,
                'affectedRows'  => $connection->affected_rows
            ];
            $this->rollback();
        } else {
            $this->lastRequest = [
                'errorCode'     => 0,
                'errorMessage'  => null,
                'insertIds'     => count($ids),
                'result'        => $result,
                'affectedRows'  => $connection->affected_rows
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
        $this->getConnection()->begin_transaction();
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
        $this->getConnection()->commit();
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
        $this->getConnection()->rollback();
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
