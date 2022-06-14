<?php declare(strict_types=1);

namespace Crate\Core\Commands;

use Citrus\Console\Console;
use Citrus\Console\Writer;
use Citrus\Contracts\CommandContract;

class SetupCommand implements CommandContract
{

    /**
     * Describe the command including all available methods and arguments.
     *
     * @return array
     */
    static public function describe(): array
    {
        return [
            'setup:key' => [

            ],
            'setup:install' => [

            ],
            'setup:upgrade' => [

            ]
        ];
    }


    /**
     * Console Instance
     *
     * @var Console
     */
    protected Console $console;

    /**
     * Writer Instance
     *
     * @var Writer
     */
    protected Writer $writer;

    /**
     * Create a new AboutCommand instance.
     *
     * @param Console $console
     * @param Writer $writer
     */
    public function __construct(Console $console, Writer $writer)
    {
        $this->console = $console;
        $this->writer = $writer;
    }

    public function key()
    {
        print('test');
    }

    public function install()
    {

        $this->writer->line($this->writer->yellow('Install your new Crate CMS installation'));
        $this->writer->line();

        // Create a new Console Form
        $form = new Form();

        // Select Database Driver Question
        $form->addQuestion('driver', [
            'intro' => [
                'Set-Up the desired default database driver',
                $this->writer->yellow('  [0]') . ' SQLite           (using: SQLite3)',
                $this->writer->yellow('  [1]') . ' MariaDB / MySQL  (using: MYSQLi)',
                $this->writer->yellow('  [2]') . ' MongoDB          (using: mongodb with mongodb/mongodb)',
                $this->writer->dim($this->writer->yellow('  [x]')) . $this->writer->dim(' Postgres         (Not available - Work in Progress)'),
                $this->writer->dim($this->writer->yellow('  [x]')) . $this->writer->dim(' PDO SQLite       (Not available - Work in Progress)'),
                $this->writer->dim($this->writer->yellow('  [x]')) . $this->writer->dim(' PDO MySQL        (Not available - Work in Progress)'),
                $this->writer->dim($this->writer->yellow('  [x]')) . $this->writer->dim(' PDO Postgres     (Not available - Work in Progress)'),
            ],
            'default' => '0',
            'question' => 'Select (default: 0): ',
            'validate' => fn($val) => in_array($val, ['0', '1', '2']),
            'onValid' => function($val) {
                if ($val === '0') {
                    return 'goto:sqlite_database_name';
                } else if ($val === '1') {
                    return 'goto:mysql_hostname_port';
                } else if ($val === '2') {
                    return 'goto:mongodb_dns';
                }
            },
            'onInvalid' => function(Writer $writer) {
                $writer->line(
                    $writer->red('Please select one of the available options.')
                );
                return 'repeat';
            }
        ]);

        // Configure SQLite - Database Name
        $form->addQuestion('sqlite_database_name', [
            'question' => '',
            'detault' => '$/storage/data/database.sqlite',
            'onInput' => function() {
                return 'goto:sqlite_foreign_keys';
            } 
        ]);

        // Configure SQLite - Foreign Keys
        $form->addQuestion('sqlite_foreign_keys', [
            'question' => '',
            'detault' => '$/storage/data/database.sqlite',
            'onInput' => function() {
                return 'goto:sqlite_journal_mode';
            } 
        ]);

        // Configure SQLite - Foreign Keys
        $form->addQuestion('sqlite_journal_mode', [
            'question' => '',
            'detault' => '$/storage/data/database.sqlite',
            'onInput' => function() {
                return 'goto:crate_root_url';
            } 
        ]);


        

        // Configure Crate - Root Domain
        $form->addQuestion('crate_root_domain', [
            'question' => '',
            'detault' => '$/storage/data/database.sqlite',
            'onInput' => function() {
                return 'goto:sqlite_foreign_keys';
            } 
        ]);
        
        // Configure Crate - Base Path
        $form->addQuestion('crate_base_path', [
            'question' => '',
            'detault' => '$/storage/data/database.sqlite',
            'onInput' => function() {
                return 'goto:sqlite_foreign_keys';
            } 
        ]);
        
        // Configure Crate - SuperAdmin
        $form->addQuestion('crate_superadmin_username', [
            'question' => '',
            'detault' => '$/storage/data/database.sqlite',
            'onInput' => function() {
                return 'goto:sqlite_foreign_keys';
            } 
        ]);
        
        // Configure Crate - SuperAdmin
        $form->addQuestion('crate_superadmin_password', [
            'question' => '',
            'detault' => '$/storage/data/database.sqlite',
            'onInput' => function() {
                return 'goto:sqlite_foreign_keys';
            } 
        ]);

        // Execute Form
        $data = $this->writer->form();
        var_dump($data);
    }

    public function upgrade()
    {
        print('test');
    }
}
