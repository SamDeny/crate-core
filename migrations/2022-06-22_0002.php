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
        return 'Create privileges scheme';
    }

    /**
     * @inheritDoc
     */
    public function install(Migrator $migrator)
    {

        $migrator->create('privileges', function (Scheme $scheme) {

            // Use 'crate' as database driver
            $scheme->driver = 'crate';

            // Privileges are not managed using Crate's default Model system.
            $scheme->model = false;



            $scheme->driver = 'crate';                                          // Use 'crate' database driver
            $scheme->store = false;                                             // Don't store this scheme locally
            
            $scheme->string('privilege')->length(3, 64)->required();            // Privilege Name
            $scheme->string('group')->length(3, 128);                           // Privilege Group
            $scheme->string('value')->default(0);                               // Privilege Value
        });

        $migrator->select('privileges', function (Connection $connection) {

            // Policies and Privileges do provide a policy-able structure, but 
            // since it wouldn't make any sense to handle the permissions this 
            // way, both are just validated using the Privileges system only.
            $connection->insert('privileges', [
                [
                    'privilege' => 'policies',
                    'group'     => 'role:developer',
                    'value'     => 1
                ],
                [
                    'privilege' => 'policies',
                    'group'     => 'role:admin',
                    'value'     => 1
                ],
                [
                    'privilege' => 'privileges',
                    'group'     => 'role:developer',
                    'value'     => 1
                ],
                [
                    'privilege' => 'privileges',
                    'group'     => 'role:admin',
                    'value'     => 1
                ]
            ]);

        });

    }

    /**
     * @inheritDoc
     */
    public function uninstall(Migrator $migrator)
    {
        
        $migrator->delete('privileges');

    }

};
