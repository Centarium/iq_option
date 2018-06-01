<?php
namespace Bundles;

include_once __DIR__.'/Controller.php';

use Exception;

/**
 * Class Router (Registry)
 * @package Bundles
 */
class Router
{
    private static $instance;

    private $controllerPath;
    private $templatePath;
    private $extension='.php';

    private $controller;
    private $action;
    private $arguments;

    private $defaultController = 'index';
    private $defaultAction = 'index';

    static function getInstance()
    {
        if( !isset( self::$instance ) )
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->setRouteInfo();
    }

    public function setControllerPath(string $path):void {

        if (is_dir($path) === false) {
            throw new Exception ('Invalid controller path: `' . $path . '`');
        }

        $this->controllerPath = $path;
    }

    public function setViewPath(string $path):void {

        if (is_dir($path) === false) {
            throw new Exception ('Invalid view path: `' . $path . '`');
        }

        $this->templatePath = $path;
    }

    /**
     * @return string
     */
    public function getController():string
    {
        return ucfirst($this->controller).'Controller';
    }

    /**
     * @return string
     */
    public function getTemplatePath():string
    {
        return $this->templatePath;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getViewFolder():string
    {
        $templatePath = $this->getTemplatePath();
        $controller = $this->getController();
        $viewFolder = strtolower(str_replace('Controller','',$controller));

        $viewFolderPath = $templatePath.$viewFolder;

        if( is_dir($viewFolderPath) === false )
        {
            throw new Exception ('Invalid view path: `' . $viewFolderPath . '`');
        }

        return $viewFolderPath;
    }

    /**
     * @return string
     */
    public function getAction():string
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getArguments():array
    {
        return $this->arguments;
    }

    /**
     * @param string $controller
     * @return string
     */
    private function setController(string $controller):string
    {
        $this->controller = $controller;
    }

    private function setAction(string $action):void
    {
        $this->action = $action;
    }

    private function setArguments(array $args=[]):void
    {
        $this->arguments = $args;
    }

    private function setRouteInfo():void
    {
        $route = trim($_GET['route'], '/\\');
        $args = [];

        $parts = explode('/', $route);

        $this->setController( $parts[0] ?? $this->defaultController );
        $this->setAction( $parts[1] ?? $this->defaultAction );

        if( count($parts) > 2 )
        {
            $args = array_slice($parts,2);
        }

        $this->setArguments($args);
    }

    public function delegateRoute():void
    {
        /**
         * @var Controller $controllerClass
         */
        $controller = $this->getController();
        $action = $this->getAction();

        $file = $this->controllerPath.$controller.$this->extension;

        if (is_readable($file) == false) {
            die ('404 Not Found');
        }

        //Add controller file
        include ($file);

        //Init controller class
        $controllerClass = new $controller();

        //check action
        if (is_callable(array($controllerClass, $action)) == false) {
            die ('404 Not Found');
        }

        //Exec action
        $controllerClass->$action();
    }

}