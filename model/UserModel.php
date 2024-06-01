<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const GET_USER_SQL = <<<'SQL'
SELECT 
    id,
    name,
    picture,
    creationDate,
    updateDate
FROM users 
WHERE id = ?
SQL;

const UPSERT_USER_SQL = <<<'SQL'
INSERT INTO
users (
    id,
    name,
    picture
) VALUES
(?, ?, ?)
ON DUPLICATE KEY UPDATE name = ?, picture = ?
SQL;

class UserModel extends Database
{
    public function getUser($id)
    {
        $params = [$id];

        return $this->selectData(GET_USER_SQL, 's', $params);
    }

    public function updateUser($id, $name, $picture)
    {
        $params = [$id, $name, $picture, $name, $picture];

        return $this->modifyData(UPSERT_USER_SQL, 'sssss', $params);
    }
}