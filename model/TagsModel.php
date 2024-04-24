<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const GET_TAGS_SQL = <<<'SQL'
SELECT 
    id, 
    name
FROM tags 
ORDER BY name ASC 
SQL;

class TagsModel extends Database
{
    public function getTags()
    {
        return $this->select(GET_TAGS_SQL);
    }
}
