<?php declare(strict_types=1);

namespace Crate\Core\Database\Drivers;

use Crate\Core\Contracts\DriverContract;
use Crate\Core\Database\Drivers\Orders\DriverRequestOrder;
use MongoDB\Client;
use MongoDB\Database;

class MongoDB implements DriverContract
{
    use DriverRequestOrder;

    /**
     * Current MongoDB database connection
     *
     * @var Client
     */
    protected ?Client $connection = null;
    
    /**
     * Current MongoDB database instance
     *
     * @var Database
     */
    protected ?Database $database = null;

    /**
     * MongoDB database DNS
     *
     * @var string
     */
    protected string $dns;

    /**
     * MongoDB database name
     *
     * @var string
     */
    protected string $name;

    /**
     * MongoDB database DNS options
     *
     * @var array
     */
    protected array $dnsOptions;

    /**
     * MongoDB database driver options
     *
     * @var array
     */
    protected array $driverOptions;

    /**
     * Create a new MongoDB driver instance.
     *
     * @param string $dns
     * @param string $database
     * @param array $dnsOptions
     * @param array $driverOptions
     */
    public function __construct(string $dns, string $database, array $dnsOptions = [], array $driverOptions = [])
    {
        $this->dns = $dns;
        $this->name = $database;
        $this->dnsOptions = $dnsOptions;
        $this->driverOptions = $driverOptions;
        $this->connect();
    }

    /**
     * Clear current MongoDB driver instance.
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Connect to the MongoDB database system.
     *
     * @return void
     */
    public function connect()
    {
        $this->connection = new Client($this->dns, $this->dnsOptions, $this->driverOptions);
        $this->database = $this->connection->selectDatabase($this->name);
    }

    /**
     * Disconnect from the MongoDB database system.
     *
     * @return void
     */
    public function disconnect()
    {
        unset($this->database);
        unset($this->connection);
        $this->database = null;
        $this->connection = null;
    }

    /**
     * Get the connection to the MongoDB dbs or connect first, if needed.
     *
     * @return Database
     */
    public function getConnection(): Database
    {
        if (!$this->connection) {
            $this->connect();
        }
        return $this->database;
    }

    /**
     * Insert one or multiple documents.
     *
     * @param string $collection
     * @param array $documents
     * @return int
     */
    public function insert(string $collection, array $documents): int
    {
        $connection = $this->getConnection();
        $collection = $connection->selectCollection($collection);

        // Execute
        try {
            if (array_is_list($documents)) {
                $result = $collection->insertMany($documents);
                $ids = $result->getInsertedIds();
            } else {
                $result = $collection->insertOne($documents);
                $ids = [$result->getInsertedId()];
            }
        } catch (\Exception $e) {
            $errno = $e->getCode();
            $error = $e->getMessage();
        }

        // Set
        if (isset($error)) {
            $this->lastRequest = [
                'errorCode'     => $errno,
                'errorMessage'  => $error,
                'insertIds'     => $ids ?? [],
                'result'        => $result,
                'affectedRows'  => 0
            ];
            return 0;
        } else {
            $this->lastRequest = [
                'errorCode'     => 0,
                'errorMessage'  => null,
                'insertIds'     => $ids,
                'result'        => $result,
                'affectedRows'  => $result->getInsertedCount()
            ];
            return $result->getInsertedCount();
        }
    }

}
