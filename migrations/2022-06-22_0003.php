<?php declare(strict_types=1);

use Crate\Core\Contracts\MigrationContract;
use Crate\Core\Database\Connection;
use Crate\Core\Database\Migrator;
use Crate\Core\Database\Scheme;

return new class implements MigrationContract
{

    /**
     * @inheritDoc
     */
    public function title(): string
    {
        return 'Create policies scheme';
    }

    /**
     * @inheritDoc
     */
    public function install(Migrator $migrator)
    {

        $migrator->create('policies', function (Scheme $scheme) {
            $scheme->driver = 'crate';                                          // Use 'crate' database driver
            $scheme->store = false;                                             // Don't store this scheme locally
            
            $scheme->string('policy')->length(3, 64)->required();               // Policy Name
            $scheme->string('group')->length(3, 64)->default('public');         // Policy Group
            $scheme->string('create')->enum([0, 1, 'own', 'all'])->default(0);  // Policy C Permission
            $scheme->string('read')->enum([0, 1, 'own', 'all'])->default(0);    // Policy R Permission
            $scheme->string('update')->enum([0, 1, 'own', 'all'])->default(0);  // Policy U Permission
            $scheme->string('delete')->enum([0, 1, 'own', 'all'])->default(0);  // Policy D Permission

            $scheme->unique(['policy', 'group']);                               // policies_policy_group unique key
        });

        $migrator->select('policies', function (Connection $connection) {
            $connection->insert('policies', [
                [
                    'policy'    => 'users',
                    'group'     => 'role:developer',
                    'create'    => 1,
                    'read'      => 1,
                    'update'    => 1,
                    'delet'     => 1
                ],
                [
                    'policy'    => 'users',
                    'group'     => 'role:admin',
                    'create'    => 1,
                    'read'      => 1,
                    'update'    => 1,
                    'delet'     => 1
                ],
                [
                    'policy'    => 'users',
                    'group'     => 'role:author',
                    'create'    => 0,
                    'read'      => 'own',
                    'update'    => 'own',
                    'delet'     => 0
                ]
            ]);
        });

    }

    /**
     * @inheritDoc
     */
    public function uninstall(Migrator $migrator)
    {

        $migrator->delete('policies');
        
    }

};
