<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const IS_COMMENT_AUTHOR_SQL = <<<'SQL'
SELECT 
    id
FROM comments 
WHERE 
    id = ?
    AND userId = ?
    AND isDeleted = 0
ORDER BY parentId ASC
SQL;

const GET_COMMENT_SQL = <<<'SQL'
SELECT 
    id, 
    userId, 
    content,
    likedBy,
    dislikedBy,
    postId,
    parentId
FROM comments 
WHERE 
    id = ?
    AND isDeleted = 0
ORDER BY parentId ASC
SQL;

const GET_COMMENTS_LIST_SQL = <<<'SQL'
SELECT 
    id, 
    userId, 
    content,
    likedBy,
    dislikedBy,
    postId,
    parentId
FROM comments 
WHERE 
    postId = ?
    AND isDeleted = 0
ORDER BY parentId ASC
SQL;

const CREATE_COMMENT_SQL = <<<'SQL'
INSERT INTO comments (
    userId, 
    content,
    postId,
    parentId
) VALUES (?, ?, ?, ?)
SQL;

const UPDATE_COMMENT_CONTENT_SQL = <<<'SQL'
UPDATE comments 
SET content = ?
WHERE
    userId = ? 
    AND id = ?
    AND isDeleted = 0
SQL;

const UPDATE_COMMENT_LIKES_LISTS_SQL = <<<'SQL'
UPDATE comments 
SET 
    likedBy = ?,
    dislikedBy = ?
WHERE
    id = ?
AND isDeleted = 0
SQL;

const DELETE_COMMENT_SQL = <<<'SQL'
UPDATE comments 
SET 
    isDeleted = 1
WHERE
    id = ?
    AND userId = ?
SQL;

class CommentModel extends Database
{
    public function getComment($id, $userId = '') {
        $trimmedUserId = trim($userId);
        $userIdLength =  strlen($trimmedUserId);
        $hasUserId = $userIdLength > 0;

        $query = !($hasUserId > 0) ? GET_COMMENT_SQL : IS_COMMENT_AUTHOR_SQL;
        $types = !($hasUserId > 0) ? 'i' : 'is';
        $params = !($hasUserId > 0) ? [$id] : [$id, $userId];

        return $this->selectData($query, $types, $params);
    }

    public function getCommentsList($postId) {
        return $this->selectData(GET_COMMENTS_LIST_SQL, 'i', [$postId]);
    }

    public function createComment($userId, $content, $postId, $parentId) {
        $types = 'ssii';
        $params = [$userId, $content, $postId, $parentId];

        return $this->modifyData(CREATE_COMMENT_SQL, $types, $params);
    }

    public function updateCommentContent($content, $userId, $id) {
        $types = 'ssi';
        $params = [$content, $userId, $id];

        return $this->modifyData(UPDATE_COMMENT_CONTENT_SQL, $types, $params);
    }

    public function updateCommentLikesList($likedByList, $dislikedByList, $id) {
        $types = 'ssi';
        $params = [$likedByList, $dislikedByList, $id];

        return $this->modifyData(UPDATE_COMMENT_LIKES_LISTS_SQL, $types, $params);
    }

    public function deleteComment($id, $userId) {
        $types = 'is';
        $params = [$id, $userId];

        return $this->modifyData(DELETE_COMMENT_SQL, $types, $params);
    }
}