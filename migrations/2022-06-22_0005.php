<?php declare(strict_types=1);

use Crate\Core\Contracts\MigrationContract;
use Crate\Core\Database\Migrator;
use Crate\Core\Database\Scheme;

return new class implements MigrationContract
{

    /**
     * @inheritDoc
     */
    public function title(): string
    {
        return 'Create tokens scheme';
    }

    /**
     * @inheritDoc
     */
    public function install(Migrator $migrator)
    {

        $migrator->create('tokens', function (Scheme $scheme) {

            $scheme->string('nonce')->unique()->length(3, 128);
            $scheme->string('token');
            $scheme->string('check');
            $scheme->datetime('valid_until')->optional();

        });

    }

    /**
     * @inheritDoc
     */
    public function uninstall(Migrator $migrator)
    {

        $migrator->delete('tokens');
        
    }

};
