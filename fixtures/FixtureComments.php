<?php
namespace Fixtures;

use Models\Comments;

class FixtureComments extends Comments
{
    protected function setTable()
    {
        //$this->table = 'comments_test';
        parent::setTable();
    }

    /**
     *  Create fixture data
     */
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
     * Create Comments table
     * @return bool
     */
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

    /**
     * Tabula Rasa
     */
    public function migrateDown():void
    {
        $this->conn->query("DROP INDEX left_key");
        $this->conn->query("DROP TABLE comments");
    }

    /**
     * Expect false
     * @return mixed
     */
    public function checkTreeLeftLessThenRight()
    {
        $query = $this->conn->query("
            SELECT comment_id FROM comments WHERE left_key > right_key
        ");

        return  $query->fetchColumn();
    }

    /**
     * Expect false
     * @return mixed
     */
    public function checkReminderDivisionTwo()
    {
        $query = $this->conn->query("
            SELECT comment_id FROM comments WHERE ((right_key - left_key) %2) = 0 
        ");

        return  $query->fetchColumn();
    }

    /**
     * @return mixed
     */
    public function checkAddOdd()
    {
        $query = $this->conn->query("
            SELECT comment_id FROM comments 
              WHERE  
                  ((level % 2) != 0 AND  (left_key % 2) = 0)
                  OR 
                  ((level % 2) = 0 AND  (left_key % 2) != 0)
        ");

        return  $query->fetchColumn();
    }

    public function chekKeysUnique()
    {
        $query = $this->conn->query("
            SELECT left_key as key FROM comments GROUP BY left_key HAVING COUNT(*) > 1
            UNION
            SELECT right_key as key FROM comments GROUP BY right_key HAVING COUNT(*) > 1
            UNION
            SELECT left_key as key FROM comments WHERE left_key = right_key
        ");

        return  $query->fetchColumn();
    }

    /**
     * Expect 1
     * @return mixed
     */
    public function checkRightKeyDubleNodes()
    {
        $query = $this->conn->query("
            SELECT max(right_key), 2*COUNT(*) as double_nodes FROM comments
        ");

        return  $query->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Expect 1
     * @return mixed
     */
    public function checkMinLeftKey()
    {
        $query = $this->conn->query("
            SELECT min(left_key) FROM comments
        ");

        return  $query->fetchColumn();
    }

}