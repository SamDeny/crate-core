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
        return 'Create users scheme';
    }

    /**
     * @inheritDoc
     */
    public function install(Migrator $migrator)
    {

        $migrator->create('users', function (Scheme $scheme) {
            $userStatus = ['pending', 'active', 'disabled', 'locked', 'blocked', 'ghost'];

            // Basic Fields
            $scheme->string('status')->enum($userStatus)->default('pending');
            $scheme->string('role')->default('guest');
            $scheme->string('group')->default('default');
            $scheme->string('username')->unique()->length(3, 64)->required();
            $scheme->string('email')->unique()->required();
            $scheme->string('password')->required();
            $scheme->string('session')->optional();

            // Additional Data
            $scheme->string('display_name')->optional()->length(3, 128);
            $scheme->string('about')->optional();

            // Meta Date/Time Fields
            $scheme->datetime('activated_at')->optional();
            $scheme->datetime('lastlogin_at')->optional();
            $scheme->string('lastlogin_by')->optional();
        });

    }

    /**
     * @inheritDoc
     */
    public function uninstall(Migrator $migrator)
    {
        
        $migrator->delete('users');

    }

};
