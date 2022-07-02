<?php declare(strict_types=1);

namespace Crate\Core\Contracts;

use Crate\Core\Database\Migrations\Migrator;

interface MigrationContract
{

    /**
     * Migration Title
     *
     * @var string
     */
    public function title(): string;
    
    /**
     * Migration Install Step
     *
     * @var string
     */
    public function install(Migrator $migrator);
    
    /**
     * Migration Uninstall Step
     *
     * @var string
     */
    public function uninstall(Migrator $migrator);

}
