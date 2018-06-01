<?php
namespace Bundles;

include_once __DIR__.'/Controller.php';

use Exception;

class Router
{
    private $controllerPath;
    private $templatePath;
    private $extension='.php';

    private $controller;
    private $action;
    private $arguments;

    private $defaultController = 'index';
    private $defaultAction = 'index';


    public function __construct() {
        $this->setRouteInfo();
    }

    public function setControllerPath($path) {

        if (is_dir($path) == false) {
            throw new Exception ('Invalid controller path: `' . $path . '`');
        }

        $this->controllerPath = $path;
    }

    public function setViewPath($path) {

        if (is_dir($path) == false) {
            throw new Exception ('Invalid view path: `' . $path . '`');
        }

        $this->templatePath = $path;
    }


    /**
     * @return mixed
     */
    public function getController()
    {
        return ucfirst($this->controller).'Controller';
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return mixed
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    protected function setController($controller)
    {
        $this->controller = $controller;
    }

    protected function setAction($action)
    {
        $this->action = $action;
    }

    protected function setArguments($args=[])
    {
        $this->arguments = $args;
    }

    public function setRouteInfo()
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

    public function delegateRoute()
    {
        $controller = $this->getController();
        $action = $this->getAction();

        $file = $this->path.$controller.$this->extension;

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

        $controllerClass->setArgs( $this->getArguments() );
        $controllerClass->setTemplate( $this->templatePath );

        //Exec action
        $controllerClass->$action();
    }

}