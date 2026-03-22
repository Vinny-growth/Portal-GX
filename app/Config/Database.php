<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to use if no other is specified.
     */
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     *
     * @var array<string, mixed>
     */
    public array $default = [
        'DSN'          => '',
        'hostname' => 'localhost',
        'username' => '',
        'password' => '',
        'database' => '',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        $this->default['hostname'] = env('database.default.hostname', $this->default['hostname']);
        $this->default['username'] = env('database.default.username', $this->default['username']);
        $this->default['password'] = env('database.default.password', $this->default['password']);
        $this->default['database'] = env('database.default.database', $this->default['database']);
        $this->default['DBDriver'] = env('database.default.DBDriver', $this->default['DBDriver']);
        $this->default['port'] = (int) env('database.default.port', $this->default['port']);
        $debug = env('database.default.DBDebug');
        if ($debug !== null) {
            $this->default['DBDebug'] = filter_var($debug, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $this->default['DBDebug'];
        } else {
            $this->default['DBDebug'] = ENVIRONMENT !== 'production';
        }

        if (empty($this->default['database']) || empty($this->default['username'])) {
            $root = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
            $root .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
            header('Location: ' . $root . 'install/welcome.php');
            exit();
        }
    }
}
