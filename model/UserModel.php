<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const GET_USER_SQL = <<<'SQL'
SELECT 
    id,
    nickname,
    name,
    surname
FROM users 
WHERE id = ?
SQL;

const CREATE_USER_SQL = <<<'SQL'
INSERT INTO users (
    id,
    nickname,
    name,
    surname
) VALUES (?, ?, ?, ?)
SQL;

class UserModel extends Database
{
    public function getUser($id)
    {
        $params = [$id];

        return $this->selectData(GET_USER_SQL, 's', $params);
    }

    public function createUser($id, $nickname, $name, $surname)
    {
        $params = [$id, $nickname, $name, $surname];

        return $this->modifyData(CREATE_USER_SQL, 'ssss', $params);
    }
}