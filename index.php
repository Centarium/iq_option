<?php
include_once __DIR__.'/vendor/autoload.php';

use Bundles\Config;
use Bundles\Router;

ini_set('display_errors', Config::get('errors:display_errors'));

$router = Router::getInstance();

$router->setControllerPath (__DIR__.'/controllers/');
$router->setViewPath (__DIR__.'/view/');
$router->delegateRoute();