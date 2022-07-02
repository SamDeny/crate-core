<?php declare(strict_types=1);

namespace Crate\Core\Database\Migrations;

use Crate\Core\Factories\ConnectionFactory;

class Migrator
{

    /**
     * @var ConnectionFactory
     */
    protected ConnectionFactory $connectionFactory;

    /**
     * Migration Module
     *
     * @var string
     */
    protected string $module;

    /**
     * Migration File
     *
     * @var string
     */
    protected string $file;

    /**
     * Migration Processes
     *
     * @var array
     */
    protected array $migrations;

    /**
     * Create a new Migrator
     * 
     * @var ConnectionFactory
     */
    public function __construct(ConnectionFactory $connectionFactory, string $module, string $file)
    {
        $this->connectionFactory = $connectionFactory;
        $this->module = $module;
        $this->file = $file;

        $this->migrations = [
            'create'    => [],
            'update'    => [],
            'delete'    => [],
            'select'    => [],
        ];
    }

    /**
     * Receive Migration Module
     *
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * Receive Migration File
     *
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * Create a new Scheme
     *
     * @param string $scheme
     * @param \Closure $callback
     * @return void
     */
    public function create(string $scheme, \Closure $callback): void
    {
        $instance = new Scheme($scheme, 'create');
        call_user_func($callback, $instance);
        $this->migrations['create'][$scheme] = $instance;
    }

    /**
     * Update an existing Scheme
     *
     * @param string $scheme
     * @param \Closure $callback
     * @return void
     */
    public function update(string $scheme, \Closure $callback): void
    {
        $instance = new Scheme($scheme, 'update');
        call_user_func($callback, $instance);
        $this->migrations['update'][$scheme] = $instance;
    }

    /**
     * Delete an existing Scheme
     *
     * @param string $scheme
     * @return void
     */
    public function delete(string $scheme): void
    {
        $instance = new Scheme($scheme, 'delete');
        $this->migrations['delete'][$scheme] = $instance;
    }

    /**
     * Select a scheme and execute Connection methods
     *
     * @param string $scheme
     * @return void
     */
    public function select(string $scheme, \Closure $callback): void
    {
        $this->migrations['select'][$scheme] = $callback;
    }

    /**
     * Execute Migrations
     *
     * @return bool
     */
    public function execute(): bool
    {
        foreach ($this->migrations['create'] as $scheme) {
            /** @var Connection */
            $connection = $this->connectionFactory->make($scheme->driver);
            if (!$connection->getDriver()->migrate($scheme)) {
                return false;
            }
        }

        foreach ($this->migrations['update'] as $scheme) {
            /** @var Connection */
            $connection = $this->connectionFactory->make($scheme->driver);
            if (!$connection->getDriver()->migrate($scheme)) {
                return false;
            }
        }

        foreach ($this->migrations['delete'] as $scheme) {
            /** @var Connection */
            $connection = $this->connectionFactory->make($scheme->driver);
            if (!$connection->getDriver()->migrate($scheme)) {
                return false;
            }
        }

        foreach ($this->migrations['select'] as $callback) {
        }

        return true;
    }

}
