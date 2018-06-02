<?php
namespace Controllers;

use Bundles\Controller;
use Models\Comments as CommentModel;

class Comments extends Controller
{
    const INDEX_ACTION = '/index';
    const ADD_NODE_ACTION = '/addNode';
    const DELETE_NODE_ACTION = '/delete';
    const GET_SUB_TREE_ACTION = '/getSubTree';
    const GET_LEVEL_ACTION = '/getLevel';

    function index()
    {
        $this->render('index', [
            'addNodeAction' => self::ADD_NODE_ACTION,
            'indexAction' => self::INDEX_ACTION,
            'deleteNodeAction' => self::DELETE_NODE_ACTION,
            'getSubTreeAction' => self::GET_SUB_TREE_ACTION,
            'getLevelAction' => self::GET_LEVEL_ACTION
        ]);
    }

    function getLevel()
    {
        $level = 2;
        $model = new CommentModel();
        $firstLevel = json_encode($model->getTreeLevel($level));
        echo json_encode($firstLevel);
    }

    function addNode()
    {

    }

    function deleteNode()
    {

    }

    function getSubTree()
    {

    }

}