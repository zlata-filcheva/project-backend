<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const ADD_POST_SQL = <<<'SQL'
INSERT INTO posts (
    content, 
    topic,
    categoryId,
    userId,
    tags
) VALUES (?, ?, ?, ?, ?)
SQL;

class UserModel extends Database
{
    public function addPost($content, $topic, $categoryId, $userId, $tags)
    {
        $params = [$content, $topic, $categoryId, $userId, $tags];

        return $this->insert(ADD_POST_SQL, 'ssiis', $params);
    }
}