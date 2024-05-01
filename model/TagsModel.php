<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const GET_TAGS_SQL = <<<'SQL'
SELECT 
    id, 
    name
FROM tags 
ORDER BY name ASC 
SQL;

const CREATE_TAGS_SQL = <<<'SQL'
INSERT INTO tags (
    name
) VALUES (?)
SQL;

class TagsModel extends Database
{
    public function getTags()
    {
        return $this->selectData(GET_TAGS_SQL);
    }

    public function createTags($tags = [])
    {
        foreach ($tags as $tag) {
            $this->modifyData(CREATE_TAGS_SQL, 's', [$tag]);
        }
    }
}
