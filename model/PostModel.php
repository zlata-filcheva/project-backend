<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const IS_AUTHOR_SQL = <<<'SQL'
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
WHERE 
    id = ?
    AND userId = ?
SQL;

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

const UPDATE_POST_CONTENT_SQL = <<<'SQL'
UPDATE posts 
SET content = ?, 
updateDate = NOW() 
WHERE 
    id = ?
    AND userId = ?
SQL;

const UPDATE_POST_TAGS_SQL = <<<'SQL'
UPDATE posts
SET tagIds = ?
WHERE
    id = ?
    AND userId = ?
SQL;

class PostModel extends Database
{
    public function getPost($id, $userId = '')
    {
        $params = [$id];

        $query = !$userId ? GET_POST_SQL : IS_AUTHOR_SQL;
        $types = !$userId ? 'i' : 'is';

        return $this->selectData($query, $types, $params);
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

    public function updatePostContent($content, $id, $userId)
    {
        $params = [$content, $id, $userId];

        $this->modifyData(UPDATE_POST_CONTENT_SQL, 'sis', $params);
    }

    public function updatePostTags($tagIds, $id, $userId)
    {
        $params = [$tagIds, $id, $userId];

        $this->modifyData(UPDATE_POST_TAGS_SQL, 'sis', $params);
    }
}