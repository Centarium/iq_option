<?php
namespace Models;

use Interfaces\CommentsData;
use PDO;
use Bundles\Config;
use Exception;

/**
 * Базовая Xss и SQL - injection protection, базовая защита от concurrency query в пределах
 * одной унитарной операции, но не разных.
 * Class Comments
 * @package Models
 */
class Comments
{
    protected $conn;
    public $error=false;
    protected $userErrorMessage;
    protected $logMessage;
    protected $error_uuid;

    public function __construct()
    {
        $this->conn = $this->getConnection(Config::get('db:user'), Config::get('db:pass'));
    }

    /**
     * todo logger
     * todo composite inheritance
     * @param string $logMessage
     * @param string $UserMessage
     * @param int $code
     * @throws Exception
     */
    protected function setError(string $logMessage, string $UserMessage,int $code=100)
    {
        $this->error_uuid = uniqid(true);
        $this->error = true;
        $this->userErrorMessage = "{$UserMessage} Contact Administrator with code {$this->error_uuid}";
        $this->logMessage = $logMessage;

        throw new Exception($UserMessage, 100);
    }

    public function getErrorMessage()
    {
        return $this->userErrorMessage;
    }

    public function getTreeLevel($level)
    {
        $query = $this->conn->prepare("
              SELECT comment_id, comment, left_key, right_key, level, parent_id, timestamp
              FROM comments
              WHERE level = :level
              ORDER BY comment_id,parent_id, level
        ");

        $query->bindParam(':level', $level );
        $query->execute();

        $res = $query->fetchAll(\PDO::FETCH_ASSOC);

        return $res;
    }


    private function addMessageToComment(int $right_key):\PDOStatement
    {
        $query = $this->conn->prepare("
                  UPDATE comments SET right_key = right_key + 2, 
                   left_key = CASE 
                    WHEN left_key > :right_key THEN left_key +2
                    ELSE left_key
                  END
                  WHERE right_key >= :right_key
                ");

        $query->bindParam(':right_key', $right_key );

        return $query;
    }

    private function addNewMessage(int $right_key):\PDOStatement
    {
        $query = $this->conn->prepare("
            UPDATE comments SET right_key = right_key + 2 WHERE right_key >= :right_key AND left_key < :right_key
        ");

        $query->bindParam(':right_key', $right_key );

        return $query;
    }

    public function getMaxRightKey(){
        $query = $this->conn->query("
            SELECT max(right_key) FROM comments
        ");

        return  $query->fetchColumn();
    }

    //todo refactor by three methods?
    /**
     * @param int $right_key
     * @param int $level
     * @return bool
     */
    public function addNode(CommentsData $data):bool
    {
        $parent_id = $data->getCommentID();
        $right_key = $data->getRightKey();
        $message = $data->getComment();
        $level = $data->getLevel();

        try{
            $this->conn->beginTransaction();

            if( $parent_id !== 0 )
            {
                $query1 = $this->addMessageToComment($right_key);

            }
            else{
                $right_key = $this->getMaxRightKey();
                if(is_null($right_key)) $right_key = 1;
                $query1 = $this->addNewMessage($right_key);
            }

            $res = $query1->execute();

            if(!$res){
                $this->setError( json_encode($query1->errorInfo()), "Can`t update nodes ");
            }

            $query2 = $this->conn->prepare("
              INSERT INTO comments(user_id, comment, left_key, right_key, level, parent_id )
               VALUES (1, :message, :left_key, :right_key+1, :level+1, :parent_id )
            ");

            $query2->bindParam(':right_key', $right_key );
            $query2->bindParam(':level', $level );
            $query2->bindParam(':message', $message );
            $query2->bindParam(':left_key', $right_key );
            $query2->bindParam(':parent_id', $parent_id );

            $res = $query2->execute();

            if(!$res){
                $this->setError( json_encode($query2->errorInfo()), "Can`t insert new node ");
            }

            $this->conn->commit();
            return true;
        }catch (Exception $e)
        {
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * @param CommentsData $data
     * @return bool
     * @throws Exception
     */
    public function editNode(CommentsData $data):bool
    {
        try{

            $query = $this->conn->prepare("
                UPDATE comments SET comment = :comment WHERE comment_id = :commentID
            ");

            $query->bindParam(':commentID', $data->getCommentID() );
            $query->bindParam(':comment', $data->getComment() );

            $res = $query->execute();

            if(!$res){
                $this->setError( json_encode($query->errorInfo()), "Can`t update message ");
            }

        }catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param $left_key
     * @param $right_key
     * @return bool
     */
    public function deleteNode($left_key, $right_key):bool
    {
        $diff = $right_key - $left_key;

        try{
            $this->conn->beginTransaction();

            $query1 = $this->conn->prepare("
              DELETE FROM comments WHERE left_key >= :left_key AND  right_key <= :right_key
            ");

            $query1->bindParam(':left_key', $left_key );
            $query1->bindParam(':right_key', $right_key );
            $query1->execute();

            $res = $query1->execute();

            if(!$res){
                $this->setError( json_encode($query1->errorInfo()), "Can`t delete node ");
            }

            $query2 = $this->conn->prepare("
             UPDATE comments SET left_key = CASE 
                WHEN left_key > :left_key THEN left_key - (:diff + 1)
                ELSE left_key
              END, 
             right_key = right_key - (:diff + 1) 
             WHERE right_key > :right_key
            ");

            $query2->bindParam(':left_key', $left_key );
            $query2->bindParam(':right_key', $right_key );
            $query2->bindParam(':diff', $diff );

            $res = $query2->execute();

            if(!$res){
                $this->setError( json_encode($query2->errorInfo()), "Can`t update node ");
            }

            $this->conn->commit();
            return true;
        }catch (Exception $e)
        {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getSubTree($left_key, $right_key)
    {
        $query = $this->conn->prepare("
              SELECT comment_id, comment, left_key, right_key, level, parent_id, timestamp
              FROM comments
              WHERE left_key > :left_key AND  right_key < :right_key
              ORDER BY comment_id,parent_id, level
        ");

        $query->bindParam(':left_key', $left_key );
        $query->bindParam(':right_key', $right_key );
        $query->execute();

        $res = $query->fetchAll(\PDO::FETCH_ASSOC);

        return $res;
    }

    public function migrateUp()
    {
        $query = $this->conn->query("
              SELECT table_catalog 
              FROM information_schema.tables 
              WHERE table_schema = 'public' AND table_name = 'comments'"
        );

        $res = $query->fetch(\PDO::FETCH_ASSOC);

        if($res) return true;

        $this->conn->query(
            "CREATE TABLE comments (
              comment_id SERIAL NOT NULL, 
              user_id INT NOT NULL,
              comment TEXT, 
              left_key INT,
              right_key INT,
              level INT,
              parent_id INT,
              timestamp TIMESTAMP DEFAULT current_timestamp, 
              
              PRIMARY KEY(comment_id) )"
        );

        $this->conn->query("
            CREATE INDEX left_key ON comments (left_key,right_key,level)
        ");

    }

    public function createData()
    {
        $array = [

                [
                    'id' => 1,
                    'parent_id' => 0,
                    'level' => 1,
                    'left_key' => 1,
                    'right_key' => 32
                ],
                [
                    'id' => 2,
                    'parent_id' => 1,
                    'level' => 2,
                    'left_key' => 2,
                    'right_key' => 9
                ],
                [
                    'id' => 3,
                    'parent_id' => 1,
                    'level' => 2,
                    'left_key' => 10,
                    'right_key' => 23
                ],
                [
                    'id' => 4,
                    'parent_id' => 1,
                    'level' => 2,
                    'left_key' => 24,
                    'right_key' => 31
                ],
                    //---------------
                    [
                        'id' => 5,
                        'parent_id' => 2,
                        'level' => 3,
                        'left_key' => 3,
                        'right_key' => 8
                    ],
                    [
                        'id' => 6,
                        'parent_id' => 3,
                        'level' => 3,
                        'left_key' => 11,
                        'right_key' => 12
                    ],
                    [
                        'id' => 7,
                        'parent_id' => 3,
                        'level' => 3,
                        'left_key' => 13,
                        'right_key' => 20
                    ],
                    [
                        'id' => 8,
                        'parent_id' => 3,
                        'level' => 3,
                        'left_key' => 21,
                        'right_key' => 22
                    ],
                    [
                        'id' => 9,
                        'parent_id' => 4,
                        'level' => 3,
                        'left_key' => 25,
                        'right_key' => 30
                    ],
                        //-----------------
                        [
                            'id' => 10,
                            'parent_id' => 5,
                            'level' => 4,
                            'left_key' => 4,
                            'right_key' => 5
                        ],
                        [
                            'id' => 11,
                            'parent_id' => 5,
                            'level' => 4,
                            'left_key' => 6,
                            'right_key' => 7
                        ],
                        [
                            'id' => 12,
                            'parent_id' => 7,
                            'level' => 4,
                            'left_key' => 14,
                            'right_key' => 15
                        ],
                        [
                            'id' => 13,
                            'parent_id' => 7,
                            'level' => 4,
                            'left_key' => 16,
                            'right_key' => 17
                        ],
                        [
                            'id' => 14,
                            'parent_id' => 7,
                            'level' => 4,
                            'left_key' => 18,
                            'right_key' => 19
                        ],
                        [
                            'id' => 15,
                            'parent_id' => 9,
                            'level' => 4,
                            'left_key' => 26,
                            'right_key' => 27
                        ],
                        [
                            'id' => 16,
                            'parent_id' => 9,
                            'level' => 4,
                            'left_key' => 28,
                            'right_key' => 29
                        ],


        ];

        foreach ($array as $item)
        {
            $query = $this->conn->prepare("
            INSERT INTO comments(user_id,comment,left_key,right_key,level,parent_id)
            VALUES (:user_id, :comment, :left_key, :right_key,:level,:parent_id  )
        ");

            $user_id = 1;


            $Lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod 
            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud 
            exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor 
            in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit 
            anim id est laborum';

            $arr = explode(' ',$Lorem);
            shuffle($arr);

            $comment = implode(' ',$arr);
            $left_key = $item['left_key'];
            $right_key = $item['right_key'];
            $level = $item['level'];
            $parent_id = $item['parent_id'];

            $query->bindParam(':user_id', $user_id );
            $query->bindParam(':comment', $comment );
            $query->bindParam(':left_key', $left_key );
            $query->bindParam(':right_key', $right_key );
            $query->bindParam(':level', $level );
            $query->bindParam(':parent_id', $parent_id );

            $query->execute();
        }
    }

    /**
     * @param string $user
     * @param string $pass
     * @return PDO
     */
    protected function getConnection(string $user, string $pass): PDO
    {
        $dbType = Config::get('db:dbtype');
        $host = Config::get('db:host');
        $dbname = Config::get('db:dbname');

        return new PDO("$dbType:host=$host;dbname=$dbname", $user, $pass);
    }

    public function migrateDown()
    {
        $this->conn->query("DROP INDEX left_key");
        $this->conn->query("DROP TABLE comments");
    }
}
