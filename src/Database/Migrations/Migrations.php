<?php declare(strict_types=1);

namespace Crate\Core\Database\Migrations;

use Citrus\Exceptions\RuntimeException;
use Citrus\FileSystem\FileSystem;
use Citrus\Utilities\Str;
use Crate\Core\Factories\ConnectionFactory;
use Crate\Core\Contracts\MigrationContract;

class Migrations
{

    /**
     * Root Direcrtory
     *
     * @var string
     */
    public string $root;

    /**
     * Already installed migrations
     *
     * @var array
     */
    public array $migrations;

    /**
     * Last applied Migrator
     *
     * @var ?Migrator
     */
    public ?Migrator $lastMigrator = null;

    /**
     * Last applied Migration
     *
     * @var ?MigrationContract
     */
    public ?MigrationContract $lastMigration = null;

    /**
     * Create a new Migrations instance.
     */
    public function __construct()
    {
        $this->root = path(':modules');
        if (!file_exists($this->root)) {
            throw new RuntimeException("The modules directory does not exist.");
        }

        if (file_exists(path(':storage/data/.installed'))) {
            $connection = citrus(ConnectionFactory::class)->make('crate');
            $this->migrations = $connection->find('migrations');
        } else {
            $this->migrations = [];
        }
    }

    /**
     * Receive the last executed Migrator
     *
     * @return Migrator|null
     */
    public function getLastMigrator(): ?Migrator
    {
        return $this->lastMigrator;
    }

    /**
     * Receive the last executed Migrator
     *
     * @return MigrationContract|null
     */
    public function getLastMigration(): ?MigrationContract
    {
        return $this->lastMigration;
    }

    /**
     * Scan for uninstalled migration files.
     *
     * @param string|null $module
     * @return array
     */
    public function scan(?string $module = null): array
    {
        if (is_string($module)) {
            $path = $this->root . DIRECTORY_SEPARATOR . $module;
            if (!file_exists($path)) {
                throw new RuntimeException("The passed module '$module' does not exist.");
            }
        }

        if (!isset($path)) {
            return $this->collectAll();
        } else {
            $result = $this->collect($path);
            return $result === null? []: $result;
        }
    }

    /**
     * Collect all Migration files.
     *
     * @return array
     */
    protected function collectAll(): array
    {
        $result = [];

        $generator = new FileSystem($this->root);
        foreach ($generator->contains(['module.php', 'composer.json'], 1) AS $modpath) {
            $module = substr($modpath, strlen($this->root)+1);

            if (($temp = $this->collect($modpath)) !== null) {
                $result[$module] = $temp;
            }
        }

        return $result;
    }

    /**
     * Collect Migration files from one single module.
     *
     * @param string $path
     * @return ?array
     */
    protected function collect(string $path): ?array
    {
        $migrationpath = $path . DIRECTORY_SEPARATOR . 'migrations';
        if (!file_exists($migrationpath)) {
            return null;
        }

        $handle = opendir($migrationpath);
        $result = [];
        while(($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}\_[0-9]+\.php$/', $file)) {
                $result[] = $file;
            }
        }
        closedir($handle);

        return empty($result)? null: $result;
    }

    /**
     * Validate uninstalled migration files.
     *
     * @param string|null $module
     * @return void
     */
    public function validate(?string $module = null)
    {

    }

    /**
     * Execute migration file.
     *
     * @param string $module
     * @param string $file
     * @return bool
     */
    public function execute(string $module, string $file): bool
    {
        $filepath = path(':modules/', $module, 'migrations', $file);

        /** @var Migrator */
        $migrator = citrus(Migrator::class, $module, $file);

        /** @var MigrationContract */
        $migration = include $filepath;
        $migration->install($migrator);

        $this->lastMigrator = $migrator;
        $this->lastMigration = $migration;
        
        if ($migrator->execute()) {
            /** @var Connection */
            $connection = citrus(ConnectionFactory::class)->make('crate');
            $connection->getDriver()->insert('migrations', [
                'uuid' => Str::uuid(),
                'module' => $module,
                'migration' => $file
            ]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Rollback installed migration files.
     *
     * @param string|null $module
     * @param integer|null $amount
     * @return void
     */
    public function rollback(?string $module = null, ?int $amount = null)
    {

    }

}
