<?php
namespace Configs;

use Interfaces\ConfigInterface;

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
            ],
            'errors' =>[
                'display_errors' => 1
            ]
        ];
    }
}