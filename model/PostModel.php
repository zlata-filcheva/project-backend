<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const GET_POSTS_COUNT = <<<'SQL'
SELECT 
    COUNT(id) AS count
FROM posts
WHERE isDeleted = 0
SQL;

const IS_POST_AUTHOR_SQL = <<<'SQL'
SELECT 
    id, 
    content, 
    creationDate,
    updateDate,
    title,
    categoryId,
    userId,
    tagIds
FROM posts 
WHERE 
    id = ?
    AND userId = ?
    AND isDeleted = 0
SQL;

const GET_POST_SQL = <<<'SQL'
SELECT 
    posts.id, 
    posts.content, 
    posts.creationDate,
    posts.updateDate,
    posts.title,
    posts.categoryId,
    posts.tagIds,
    posts.userId,
    users.name as userName,
    users.picture as userPicture
FROM posts 
INNER JOIN users
ON posts.userId = users.id
WHERE 
    posts.id = ?
    AND posts.isDeleted = 0;
SQL;

const GET_POSTS_LIST_SQL = <<<'SQL'
SELECT 
    posts.id, 
    posts.content, 
    posts.creationDate,
    posts.updateDate,
    posts.title,
    posts.categoryId,
    posts.tagIds,
    posts.userId,
    users.name as userName,
    users.picture as userPicture
FROM posts 
INNER JOIN users
ON posts.userId = users.id
WHERE isDeleted = 0
ORDER BY creationDate DESC 
LIMIT ? OFFSET ?
SQL;

const CREATE_POST_SQL = <<<'SQL'
INSERT INTO posts (
    content, 
    title,
    categoryId,
    userId,
    tagIds
) VALUES (?, ?, ?, ?, ?)
SQL;

const UPDATE_POST_SQL = <<<'SQL'
UPDATE posts 
SET
    updateDate = NOW(),
    title = ?,
    content = ?,
    categoryId = ?,
    tagIds = ?
WHERE 
    id = ?
    AND userId = ?
    AND isDeleted = 0
SQL;

const DELETE_POST_SQL = <<<'SQL'
UPDATE posts 
SET
    updateDate = NOW(),
    isDeleted = 1
WHERE 
    id = ?
    AND userId = ?
SQL;

class PostModel extends Database
{
    public function getPostsCount()
    {
        return $this->selectData(GET_POSTS_COUNT);
    }

    public function getPost($id, $userId = '')
    {
        $trimmedUserId = trim($userId);
        $userIdLength =  strlen($trimmedUserId);
        $hasUserId = $userIdLength > 0;

        $query = !($hasUserId > 0) ? GET_POST_SQL : IS_POST_AUTHOR_SQL;
        $types = !($hasUserId > 0) ? 'i' : 'is';
        $params = !($hasUserId > 0) ? [$id] : [$id, $userId];

        return $this->selectData($query, $types, $params);
    }

    public function getPostsList($rowCount = 20, $offset = 0)
    {
        $params = [$rowCount, $offset];

        return $this->selectData(GET_POSTS_LIST_SQL, 'ii', $params);
    }

    public function createPost($content, $title, $categoryId, $userId, $tagIds)
    {
        $params = [$content, $title, $categoryId, $userId, $tagIds];

        return $this->modifyData(CREATE_POST_SQL, 'ssiss', $params);
    }

    public function updatePost(
        $title,
        $content,
        $categoryId,
        $tagIds,
        $id,
        $userId
    ) {
        $types = 'ssisis';
        $params = [
            $title,
            $content,
            $categoryId,
            $tagIds,
            $id,
            $userId
        ];

        return $this->modifyData(UPDATE_POST_SQL, $types, $params);
    }

    public function deletePost(
        $id,
        $userId
    ) {
        $types = 'is';
        $params = [
            $id,
            $userId
        ];

        return $this->modifyData(DELETE_POST_SQL, $types, $params);
    }
}