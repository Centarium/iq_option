<?php
namespace Models;

include_once __DIR__.'/../bundles/Config.php';

use PDO;
use Bundles\Config;

class Comments
{
    protected $conn;

    public function __construct()
    {
        $this->conn = $this->getConnection(Config::get('db:user'), Config::get('db:pass'));
    }

    public function migrateUp()
    {
        $query = $this->conn->query("
              SELECT table_catalog 
              FROM information_schema.tables 
              WHERE table_schema = 'public' AND table_name = 'comments'"
        );
        $query->execute();

        $res = $query->fetch(\PDO::FETCH_ASSOC);

        if($res) return true;

        $this->conn->query(
            "CREATE TABLE comments (
              comment_id SERIAL NOT NULL, 
              user_id INT NOT NULL,
              comment TEXT, 
              left_key INT,
              right_ket INT,
              level INT,
              parent_id INT,
              timestamp TIMESTAMP DEFAULT current_timestamp, 
              
              PRIMARY KEY(comment_id) )"
        );

        $this->conn->query("
            CREATE INDEX left_key ON comments (left_key,right_ket,level)
        ");

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
