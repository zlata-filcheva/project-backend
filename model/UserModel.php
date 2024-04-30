<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const HAS_USER_SQL = <<<'SQL'
SELECT 
    oauthId
FROM users 
WHERE oauthId=?
SQL;

const CREATE_USER_SQL = <<<'SQL'
INSERT INTO tags (
    oauthId,
    nickName,
    name,
    surname
) VALUES (?)
SQL;

class UserModel extends Database
{
    public function hasUser($oauthId)
    {
        return $this->select(HAS_USER_SQL, 's', [$oauthId]);
    }

    public function createUser($user = [])
    {
        $this->insert(CREATE_TAGS_SQL, 'ssss', [$user]);
    }
}