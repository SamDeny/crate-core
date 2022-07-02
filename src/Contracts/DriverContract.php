<?php declare(strict_types=1);

namespace Crate\Core\Contracts;

use Crate\Core\Database\Query;
use Crate\Core\Database\Scheme;

interface DriverContract
{

    /**
     * Connect to the Database.
     *
     * @return void
     */
    public function connect(): void;

    /**
     * Disconnect from the Database.
     * 
     * @return void
     */
    public function disconnect(): void;

    /**
     * Migrate a Scheme.
     *
     * @param Scheme $scheme
     * @return boolean
     */
    public function migrate(Scheme $scheme): bool;

    /**
     * Select one or more documents.
     *
     * @param string $table
     * @return mixed
     */
    public function select(string $table, Query $query): mixed;

    /**
     * Insert one or more documents. 
     *
     * @param string $table
     * @param array $values A single document or multiple documents as list.
     * @return int The number of inserted rows.
     */
    public function insert(string $table, array $values): int;

    /**
     * Update one or more documents.
     *
     * @param string $table
     * @param array $values
     * @param array $where
     * @return integer
     */
    public function update(string $table, array $values, array $where): int;

    /**
     * Delete one or more documents.
     *
     * @param string $table
     * @param array $where
     * @return integer
     */
    public function delete(string $table, array $where): int;

}
