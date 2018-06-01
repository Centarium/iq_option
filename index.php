<?php
include_once __DIR__.'/bundles/Router.php';


use Bundles\Router;

$router = Router::getInstance();

$router->setControllerPath (__DIR__.'/controllers/');
$router->setViewPath (__DIR__.'/view/');
$router->delegateRoute();