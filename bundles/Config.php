<?php
namespace Bundles;

include __DIR__.'/../config/Dev.php';
include_once __DIR__.'/../interfaces/ConfigInterface.php';

use Interfaces\ConfigInterface;
use \Configs\Dev;


class Config
{
    /**
     * @var ConfigInterface $config
     */
    private static $config;

    private function __construct(){}

    public static function setEvironment(ConfigInterface $config)
    {
        self::$config = $config;
    }

    /**
     * @param $configName
     * @return string
     */
    public static function get($configName)
    {
        if( is_null(self::$config) )
        {
            self::setEvironment(new Dev());
        }

        $conf = self::$config::getConfigList();
        $confPath = explode(':',$configName);

        foreach ($confPath as $key)
        {
            $conf = $conf[$key];
        }

        return $conf;
    }
}