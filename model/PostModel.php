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

const UPDATE_POST_CONTENT_START_SQL = <<<'SQL'
UPDATE posts 
SET
SQL;

const UPDATE_POST_CONTENT_END_SQL = <<<'SQL'
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

        $trimmedUserId = trim($userId);
        $userIdLength =  strlen($trimmedUserId);
        $hasUserId = $userIdLength > 0;

        $query = !($hasUserId > 0) ? GET_POST_SQL : IS_AUTHOR_SQL;
        $types = !($hasUserId > 0) ? 'i' : 'is';
        $params = !($hasUserId > 0) ? [$id] : [$id, $userId];

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

        return $this->modifyData(CREATE_POST_SQL, 'ssiss', $params);
    }

    public function updatePostContent($content, $topic, $id, $userId)
    {
        $query = '';
        $types = '';

        $params = [];

        $trimmedContent = trim($content);
        $contentLength =  strlen($trimmedContent);
        $hasContent = $contentLength > 0;

        $trimmedTopic = trim($topic);
        $topicLength =  strlen($trimmedTopic);
        $hasTopic = $topicLength > 0;

        $query .= UPDATE_POST_CONTENT_START_SQL;

        if ($hasContent) {
            $query .= ' content = ?, ';
            $types .= 's';
            $params = [...$params, $content];
        }

        if ($hasTopic) {
            $query .= ' topic = ?, ';
            $types .= 's';
            $params = [...$params, $topic];
        }

        $types .= 'is';
        $params = [...$params, $id, $userId];

        $query .= UPDATE_POST_CONTENT_END_SQL;

        $this->modifyData($query, $types, $params);
    }

    public function updatePostTags($tagIds, $id, $userId)
    {
        $params = [$tagIds, $id, $userId];

        $this->modifyData(UPDATE_POST_TAGS_SQL, 'sis', $params);
    }
}