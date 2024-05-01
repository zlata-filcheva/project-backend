<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const GET_USER_SQL = <<<'SQL'
SELECT 
    id,
    nickName,
    name,
    surname
FROM users 
WHERE id = ?
SQL;

const CREATE_USER_SQL = <<<'SQL'
INSERT INTO users (
    id,
    nickName,
    name,
    surname
) VALUES (?, ?, ?, ?)
SQL;

class UserModel extends Database
{
    public function getUser($id)
    {
        return $this->selectData(GET_USER_SQL, 's', [$id]);
    }

    public function createUser($id, $nickName, $name, $surname)
    {
        $params = [$id, $nickName, $name, $surname];

        $this->modifyData(CREATE_USER_SQL, 'ssss', $params);
    }
}