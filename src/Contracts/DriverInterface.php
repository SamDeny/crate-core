<?php declare(strict_types=1);

namespace Crate\Core\Contracts;

interface DriverInterface
{

    /**
     * Connect to the Database.
     *
     * @return void
     */
    public function connect();

    /**
     * Disconnect from the Database.
     * 
     * @return void
     */
    public function disconnect();

    /**
     * Insert a new Document.
     *
     * @param string $table
     * @param array $values A single document or multiple documents as list.
     * @return int The number of inserted rows.
     */
    public function insert(string $table, array $values): int;

}
