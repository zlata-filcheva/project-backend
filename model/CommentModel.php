<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const GET_COMMENTS_SQL = <<<'SQL'
SELECT 
    id, 
    content, 
    creationDate,
    updateDate,
    topic,
    categoryId,
    userId,
    tags
FROM comments 
ORDER BY creationDate DESC 
LIMIT ?,?
SQL;

const CREATE_COMMENT_SQL = <<<'SQL'
INSERT INTO comments (
    userId, 
    content,
    postId
) VALUES (?, ?, ?)
SQL;

const UPDATE_COMMENT_CONTENT_SQL = <<<'SQL'
UPDATE comments SET
                    content = ?
WHERE
    userId = ? AND postId = ?
SQL;

const UPDATE_COMMENT_ADD_LIKE_SQL = <<<'SQL'
UPDATE comments SET
                    likes = likes + 1 
WHERE
    userId = ? AND postId = ?
SQL;

const UPDATE_COMMENT_REMOVE_LIKE_SQL = <<<'SQL'
UPDATE comments SET
                    likes = likes - 1 
WHERE
    userId = ? AND postId = ?
SQL;

const UPDATE_COMMENT_ADD_DISLIKE_SQL = <<<'SQL'
UPDATE comments SET
                    dislikes = dislikes + 1 
WHERE
    userId = ? AND postId = ?
SQL;

const UPDATE_COMMENT_REMOVE_DISLIKE_SQL = <<<'SQL'
UPDATE comments SET
                    dislikes = dislikes - 1 
WHERE
    userId = ? AND postId = ?
SQL;

class CommentModel extends Database
{
    public function getComments($rowCount, $offset)
    {
        $params = [$rowCount, $offset];

        return $this->selectData(GET_COMMENTS_SQL, 'ii', $params);
    }

    public function createComment($userId, $content, $postId)
    {
        $params = [$userId, $content, $postId];

        $this->modifyData(CREATE_COMMENT_SQL, 'isi', $params);
    }

    public function updateCommentContent($content, $userId, $postId) {
        $params = [$content, $userId, $postId];

        $this->modifyData(UPDATE_COMMENT_CONTENT_SQL, 'sii', $params);
    }

    public function updateCommentAddLike($userId, $postId) {
        $params = [$userId, $postId];

        $this->modifyData(UPDATE_COMMENT_ADD_LIKE_SQL, 'sii', $params);
    }

    public function updateCommentRemoveLike($userId, $postId) {
        $params = [$userId, $postId];

        $this->modifyData(UPDATE_COMMENT_REMOVE_LIKE_SQL, 'sii', $params);
    }

    public function updateCommentAddDislike($userId, $postId) {
        $params = [$userId, $postId];

        $this->modifyData(UPDATE_COMMENT_ADD_DISLIKE_SQL, 'sii', $params);
    }

    public function updateCommentRemoveDislike($userId, $postId) {
        $params = [$userId, $postId];

        $this->modifyData(UPDATE_COMMENT_REMOVE_DISLIKE_SQL, 'sii', $params);
    }
}