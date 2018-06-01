<?php
namespace Configs;
use Interfaces\ConfigInterface;

include_once __DIR__.'/../interfaces/ConfigInterface.php';

Class Dev implements ConfigInterface
{
    public static function getConfigList()
    {
        return [
            'db' => [
                'dbtype' => 'pgsql',
                'host' => 'localhost',
                'dbname' => 'postgres',
                'user' => 'root',
                'pass' => 'admin'
            ]
        ];
    }
}