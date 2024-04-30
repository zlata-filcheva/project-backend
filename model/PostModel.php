<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const GET_POST_SQL = <<<'SQL'
SELECT 
    id, 
    content, 
    creationDate,
    updateDate,
    topic,
    categoryId,
    userId,
    tags
FROM tags 
ORDER BY creationDate DESC 
LIMIT ?,?
SQL;

const CREATE_POST_SQL = <<<'SQL'
INSERT INTO posts (
    content, 
    topic,
    categoryId,
    userId,
    tags
) VALUES (?, ?, ?, ?, ?)
SQL;

class PostModel extends Database
{
    public function getPosts($startPos, $endPos)
    {
        return $this->select(GET_POST_SQL, 'ii', [$startPos, $endPos]);
    }

    public function addPost($content, $topic, $categoryId, $userId, $tags)
    {
        $params = [$content, $topic, $categoryId, $userId, $tags];

        return $this->insert(CREATE_POST_SQL, 'ssiis', $params);
    }
}