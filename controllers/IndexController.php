<?php

use Bundles\Controller;

Class IndexController extends Controller
{
    function index()
    {
        echo 'Hello from my MVC system';
    }

    function view()
    {
        $this->render('index', [
            'ver' => 'fred'
        ]);
    }
}