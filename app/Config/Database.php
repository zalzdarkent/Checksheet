<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations
     * and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to
     * use if no other is specified.
     */
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     */
    public array $default = [
        'DSN'      => '',
        'hostname' => 'localhost',
        'username' => 'sa',
        'password' => '1234',
        'database' => 'production_control_v2',
        'DBDriver' => 'sqlsrv',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 1433,
    ];

    public array $prodControlv2 = [
        'DSN'      => '',
        'hostname' => 'localhost',
        'username' => 'sa',
        'password' => '1234',
        'database' => 'production_control_v2',
        'DBDriver' => 'sqlsrv',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 1433,
    ];

    // public array $default = [
    //     'DSN' => '',
    //     'hostname' => 'localhost',
    //     'username' => 'root',
    //     'password' => '123',
    //     'database' => 'production_control_v2',
    //     'DBDriver' => 'sqlsrv',
    //     'DBPrefix' => '',
    //     'pConnect' => false,
    //     'DBDebug' => true,
    //     'charset' => 'utf8',
    //     'DBCollat' => 'utf8_general_ci',
    //     'swapPre' => '',
    //     'encrypt' => false,
    //     'compress' => false,
    //     'strictOn' => false,
    //     'failover' => [],
    //     'port' => 1433,
    // ];

    // public array $prodControlv2 = [
    //     'DSN' => '',
    //     'hostname' => '10.16.19.27',
    //     'username' => 'sa',
    //     'password' => 'User@new1',
    //     'database' => 'production_control_v2',
    //     'DBDriver' => 'SQLSRV',
    //     'DBPrefix' => '',
    //     'pConnect' => false,
    //     'DBDebug' => true,
    //     'charset' => 'utf8',
    //     'DBCollat' => 'utf8_general_ci',
    //     'swapPre' => '',
    //     'encrypt' => false,
    //     'compress' => false,
    //     'strictOn' => false,
    //     'failover' => [],
    //     'port' => 1433,
    // ];

    public function __construct()
    {
        parent::__construct();

        // Ensure that we always set the database group to 'tests' if
        // we are currently running an automated test suite, so that
        // we don't overwrite live data on accident.
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }

        // if (!isset($this->prodControlv2) || empty($this->prodControlv2)) {
        //     throw new \Exception('prodControlv2 configuration is not loaded.');
        // }
    }
}