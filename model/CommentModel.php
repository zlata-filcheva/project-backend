<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const GET_COMMENTS_SQL = <<<'SQL'
SELECT 
    id, 
    userId, 
    content,
    likedBy,
    dislikedBy,
    postId,
    parentId
FROM comments 
WHERE postId = ?
LIMIT ? OFFSET ?
SQL;

const CREATE_COMMENT_SQL = <<<'SQL'
INSERT INTO comments (
    userId, 
    content,
    postId
) VALUES (?, ?, ?)
SQL;

const UPDATE_COMMENT_CONTENT_SQL = <<<'SQL'
UPDATE comments 
SET content = ?
WHERE
    userId = ? AND postId = ?
SQL;

Remove

UPDATE posts
    SET tagIds = JSON_REMOVE(
    tagIds, JSON_UNQUOTE(
        REPLACE(
            JSON_SEARCH( tagIds, 'one', '27', null, '$**.tagId' )
            , '.tagId'
            , ''
        )
    )
) WHERE id = 3
and JSON_SEARCH( tagIds, 'one', '27', null, '$**.tagId' ) IS NOT NULL ;


[{"tagId":"27"},{"tagId":"28"}]


Add


UPDATE `posts` SET `tagIds` = '[{\"tagId\":\"27\"},{\"tagId\":\"28\"}]' WHERE `posts`.`id` = 3;


UPDATE posts
SET tagIds = CONCAT(tagIds, ', {"tagId": "55"}')

WHERE id = 3 AND tagIds IS NOT NULL;

const UPDATE_COMMENT_ADD_LIKE_SQL = <<<'SQL'
UPDATE comments
SET likedBy = JSON_REMOVE(likedBy, JSON_UNQUOTE(JSON_SEARCH(likedBy, 'one', '{"likedBy": 26}')))
WHERE JSON_CONTAINS(likedBy, '{"likedBy": 26}');
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
    public function getCommentsList($postId, $rowCount, $offset)
    {
        $params = [$postId, $rowCount, $offset];

        return $this->selectData(GET_COMMENTS_SQL, 'iii', $params);
    }

    public function createComment($userId, $content, $postId)
    {
        $params = [$userId, $content, $postId];

        $this->modifyData(CREATE_COMMENT_SQL, 'ssi', $params);
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