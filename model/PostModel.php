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
    tagIds
FROM posts 
WHERE id = ?
SQL;

const GET_POSTS_LIST_SQL = <<<'SQL'
SELECT 
    id, 
    content, 
    creationDate,
    updateDate,
    topic,
    categoryId,
    userId,
    tagIds
FROM posts 
ORDER BY creationDate DESC 
LIMIT ? OFFSET ?
SQL;

const CREATE_POST_SQL = <<<'SQL'
INSERT INTO posts (
    content, 
    topic,
    categoryId,
    userId,
    tagIds
) VALUES (?, ?, ?, ?, ?)
SQL;



/*
$sql = "UPDATE posts SET tagIds = CONCAT(tagIds, ', {\"tagId\": \"$newTagId\"}') WHERE tagIds IS NOT NULL";

const CREATE_UPDATE_POST_ADD_TAGS_SQL = <<<'SQL'
UPDATE posts
SET tagIds = CONCAT(tagIds, ', {"tagId": "55"}')

WHERE id = 3 AND tagIds IS NOT NULL;

UPDATE posts 
SET tagIds = CONCAT(
    tagIds, 
    ', {"tagId": \"$newTagId\"}') 
WHERE 
    id = ? AND
    tagIds IS NOT NULL
SQL;
*/

class PostModel extends Database
{
    public function getPost($id)
    {
        $params = [$id];

        return $this->selectData(GET_POST_SQL, 'i', $params);
    }

    public function getPostsList($rowCount, $offset)
    {
        $params = [$rowCount, $offset];

        return $this->selectData(GET_POSTS_LIST_SQL, 'ii', $params);
    }

    public function createPost($content, $topic, $categoryId, $userId, $tagIds)
    {
        $params = [$content, $topic, $categoryId, $userId, $tagIds];

        $this->modifyData(CREATE_POST_SQL, 'ssiss', $params);
    }
}