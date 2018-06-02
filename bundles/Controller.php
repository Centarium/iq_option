<?php
namespace Bundles;

use Bundles\Router;

/**
 * Class Controller
 * @package Bundles
 */
abstract class Controller {

    protected $args;
    protected $controller;

    private $extension = '.php';

    public function __construct()
    {
        $this->setArgs();
        $this->setControllerName();
    }

    private function setControllerName():void
    {
        $this->controller = Router::getInstance()->getControllerName();
    }

    private function setArgs():void
    {
        $this->args = Router::getInstance()->getArguments();
    }

    /**
     * @param $view
     * @param array $args
     * @throws \Exception
     */
    protected function render(string $view, array $args=[]):void
    {
        $viewFolder= Router::getInstance()->getViewFolder();

        $viewFile = $viewFolder.'/'.$view.$this->extension;

        if( is_file($viewFile) === false  )
        {
            throw new Exception ('Invalid view path: `' . $viewFile . '`');
        }

        extract($args);
        include $viewFile;
    }

    abstract protected function index();
}