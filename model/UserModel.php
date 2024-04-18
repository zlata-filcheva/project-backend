<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const HAS_USER_SQL = <<<'SQL'
SELECT 
    id, 
    email,
    role
FROM users 
WHERE email=? AND password=?
SQL;

class UserModel extends Database
{
    public function hasUser($email, $password)
    {
        return $this->select(HAS_USER_SQL, 'ss', [$email, $password]);
    }
}