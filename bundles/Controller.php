<?php
namespace Bundles;

abstract class Controller {

    protected $args;
    protected $viewPath;

    public function setArgs($args=[])
    {
        $this->$args = $args;
    }

    public function setTemplate($view)
    {
        $this->viewPath = $view;
    }

    protected function render($view, $args)
    {

    }

    abstract function index();
}