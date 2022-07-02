<?php declare(strict_types=1);

use Crate\Core\Contracts\MigrationContract;
use Crate\Core\Database\Migrations\Migrator;
use Crate\Core\Database\Scheme;

return new class implements MigrationContract
{

    /**
     * @inheritDoc
     */
    public function title(): string
    {
        return 'Create migrations scheme';
    }

    /**
     * @inheritDoc
     */
    public function install(Migrator $migrator)
    {

        $migrator->create('migrations', function (Scheme $scheme) {

            // Use 'crate' as database driver.
            $scheme->driver = 'crate';

            // Change core 'created_at' property name.
            $scheme->created_at = 'migrated_at';

            // Disable core 'updated_at' property name.
            $scheme->updated_at = null;

            // We don't need to store the migrations table scheme, since it is 
            // not used within the Database -> Model system of Crate.
            $scheme->store = false;

            // Register Scheme Fields
            $scheme->string('module')->required();
            $scheme->string('migration')->required();

        });

    }

    /**
     * @inheritDoc
     */
    public function uninstall(Migrator $migrator)
    {

        $migrator->delete('migrations');
        
    }

};
