<?php declare(strict_types=1);

namespace Crate\Core\Database;

use Crate\Core\Contracts\DriverContract;

class Connection
{

    /**
     * Database Connection Provider
     *
     * @var string
     */
    protected string $provider;

    /**
     * Database Driver instance
     *
     * @var DriverContract
     */
    protected DriverContract $driver;

    /**
     * Crate a new Database Connection
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $provider = $params['provider'];
        unset($params['provider']);

        $this->provider = $provider;
        $this->driver = new $provider(...$params);
    }

    /**
     * Disconnect
     */
    public function __destruct()
    {
        $this->driver->disconnect();
    }

    /**
     * Receive driver class.
     *
     * @return string
     */
    public function getDriverClass(): string
    {
        return $this->provider;
    }

    /**
     * Receive current driver instance.
     *
     * @return DriverContract
     */
    public function getDriver(): DriverContract
    {
        return $this->driver;
    }

    /**
     * Query one or more documents.
     *
     * @param string $scheme
     * @param Query $query
     * @return array
     */
    public function query(string $scheme, Query $query): array
    {
        return [];
    }

    /**
     * Query one specific document.
     *
     * @param string $scheme
     * @param Query $query
     * @return ?object
     */
    public function queryOne(string $scheme, Query $query): ?object
    {
        return new \stdClass;
    }

    /**
     * Select all documents.
     *
     * @param string $scheme
     * @return array
     */
    public function select(string $scheme): array
    {
        return [];
    }

    /**
     * Insert one or more documents.
     *
     * @param string $scheme
     * @param array $documents
     * @return int
     */
    public function insert(string $scheme, array $documents): int
    {
        return 0;
    }

    /**
     * Update one or more documents.
     *
     * @param string $scheme
     * @param array $documents
     * @param array $where
     * @return int
     */
    public function update(string $scheme, array $documents, array $where = []): int
    {
        return 0;
    }

    /**
     * Replace or Insert one or more documents.
     *
     * @param string $scheme
     * @param array $documents
     * @return int
     */
    public function replace(string $scheme, array $documents, array $where = []): int
    {
        return 0;
    }

    /**
     * Delete one or more documents.
     *
     * @param string $scheme
     * @param array $where
     * @return int
     */
    public function delete(string $scheme, array $where = []): int
    {
        return 0;
    }

}
