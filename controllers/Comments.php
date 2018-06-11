<?php
namespace Controllers;

use Bundles\CommentsREQUESTData;
use Bundles\Controller;
use Models\Comments as CommentModel;
use Exception;

/**
 * todo One Action - one method instead switch - case. May be refactor
 * Class Comments
 * @package Controllers
 */
class Comments extends Controller
{
    const INDEX_ACTION = '/index';
    const ADD_NODE_ACTION = '/addNode';
    const DELETE_NODE_ACTION = '/deleteNode';
    const EDIT_NODE = '/editNode';
    const GET_SUB_TREE_ACTION = '/getSubTree';
    const GET_LEVEL_ACTION = '/getLevel';

    function index()
    {
        $this->render('index',[]);
    }


    /**
     * get actions urls
     */
    function getActions()
    {
        echo json_encode(
            [
                'indexAction' => self::INDEX_ACTION,
                'addNodeAction' => self::ADD_NODE_ACTION,
                'deleteNodeAction' => self::DELETE_NODE_ACTION,
                'editNodeAction' => self::EDIT_NODE,
                'getSubTreeAction' => self::GET_SUB_TREE_ACTION,
                'getLevelAction' => self::GET_LEVEL_ACTION
            ]
        );
    }

    /**
     * get tree level
     */
    function getLevel():void
    {
        $request = new CommentsREQUESTData();
        $level = $request->getLevel();

        if( empty($level) || $level==0 ) return;

        $model = new CommentModel();
        $treeLevel = $model->getTreeLevel( $level );

        if( count($treeLevel) !== 0 )
        {
            $tree = $this->createTree($treeLevel, $treeLevel[0]['parent_id']);
        }
        else $tree = [];

        echo json_encode($tree);
    }

    function addNode():void
    {
        $model = new CommentModel();
        if(!$model->addNode( new CommentsREQUESTData() )){
            throw new Exception($model->getErrorMessage(), 500);
        };

        echo json_encode(["status"=>'Complete']);
    }

    function deleteNode():void
    {
        $left_key = $_POST['left_key'];
        $right_key = $_POST['right_key'];

        $model = new CommentModel();
        if(!$model->deleteNode($left_key,$right_key)){
            throw new Exception($model->getErrorMessage(), 500);
        };

        echo json_encode(["status"=>'Complete']);
    }

    function editNode():void
    {
        $strategy = new CommentsREQUESTData();

        $model = new CommentModel();
        if(!$model->editNode($strategy)){
            throw new \Exception($model->getErrorMessage(),500);
        };

        echo json_encode(["status"=>'Complete']);
    }

    /**
     * node childs(sub tree)
     */
    function getSubTree():void
    {
        $comment_id = $_POST['comment_id'];
        $left_key = $_POST['left_key'];
        $right_key = $_POST['right_key'];

        $model = new CommentModel();
        //todo filter
        $subTreeList = $model->getSubTree($left_key, $right_key);

        $tree = $this->createTree($subTreeList, $comment_id);

        echo json_encode(['tree' => $tree, 'comment_id' => $comment_id]);
    }

    /**
     * Create tree - array instead list - array
     * @param array $tree
     * @param int $parentID
     * @return array
     */
    private function createTree(array $tree,int $parentID):array
    {
        $array=[];

        for($i=0;$i<count($tree);$i++)
        {
            $item = $tree[$i];

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

                array_splice($tree, $i,1);
                --$i;
            }
        }

        if( count($tree) ==0 ) return $array;

        foreach ($array as $key => $item) {
            $array[$key]['childs'] = $this->createTree($tree,$key);
        }

        return $array;
    }

}