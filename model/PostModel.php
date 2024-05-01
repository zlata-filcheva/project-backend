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
LIMIT ? OFFSET ?
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
    public function getPosts($rowCount, $offset)
    {
        $params = [$rowCount, $offset];

        return $this->selectData(GET_POST_SQL, 'ii', $params);
    }

    public function createPost($content, $topic, $categoryId, $userId, $tags)
    {
        $params = [$content, $topic, $categoryId, $userId, $tags];

        $this->modifyData(CREATE_POST_SQL, 'ssiis', $params);
    }
}