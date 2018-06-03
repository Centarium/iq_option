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
        $this->render('index',[]);
    }

    function getActions()
    {
        echo json_encode(
            [
                'addNodeAction' => self::ADD_NODE_ACTION,
                'indexAction' => self::INDEX_ACTION,
                'deleteNodeAction' => self::DELETE_NODE_ACTION,
                'getSubTreeAction' => self::GET_SUB_TREE_ACTION,
                'getLevelAction' => self::GET_LEVEL_ACTION
            ]
        );
    }

    function getLevel()
    {
        $level = $_POST['level'];

        if( empty($level) || $level==0 ) return;

        $model = new CommentModel();
        echo json_encode($model->getTreeLevel($level));
    }

    function addNode()
    {

    }

    function deleteNode()
    {

    }

    function getSubTree()
    {
        $left_key = $_POST['left_key'];
        $right_key = $_POST['right_key'];

        $left_key = 1;
        $right_key = 32;

        $model = new CommentModel();
        $subTree = $model->getSubTree($left_key, $right_key);

        $tree = $this->createTree($subTree, $subTree[0]['parent_id']);


        //$this->array = $subTree;
        //$s = $this->buildTree([],$subTree[0]['parent_id']);

echo "ho";

        //echo json_encode($this->buildSubTree($subTree, $subTree[0]['parent_id']));
    }

    public function createTree(&$tree, $parentID, $array =[])
    {
        foreach ($tree as $key => $item)
        {
            if($item['parent_id'] == $parentID  )
            {
                if(!isset($array[$item['comment_id']]) )
                {
                    $array[$item['comment_id']] = [
                        'value' =>[],
                        'childs' => []
                    ];
                }

                $array[$item['comment_id']]['value'] = $item;

                array_shift($tree);

                foreach ($tree as $k => $i) {
                    if( $i['parent_id'] == $item['comment_id'] )
                    {
                        $array[$item['comment_id']]['childs'][ $i['comment_id'] ] = $i;
                    }
                }
            }
        }


        if( count($tree) ==0 ) return $array;

        foreach ($array as $key => $item) {

            if(count( $item['childs'])  ==0 ) continue;
            $array[$key]['childs']/*[$k_c]*/ = $this->createTree($tree,$key);

        }

        /*if( count($tree) != 0 )
        {
            $array = $this->createTree($tree, $tree[0]['comment_id'], $array );
        }*/

        return $array;
    }

}