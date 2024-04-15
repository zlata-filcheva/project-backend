<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const HAS_USER_SQL = <<<'SQL'
SELECT 1
FROM users 
LIMIT ?
SQL;

class UserModel extends Database
{
    public function hasUser()
    {
        return $this->select(HAS_USER_SQL, ["i", 1]);
    }
}