<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const GET_SELECTED_TAGS_LIST_SQL = <<<'SQL'
SELECT 
    id,
    name
FROM tags 
WHERE id IN 
SQL;

const GET_TAGS_LIST_SQL = <<<'SQL'
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

class TagModel extends Database
{
    public function getSelectedTagsList($ids)
    {
        $sqlInOperator = '';

        $types = str_repeat('i', count($ids));
        $idsLength = count($ids);

        for ($i = 0; $i < count($ids); $i++) {
            $sqlInOperator .= '?';

            if (($i < $idsLength - 1)) {
                $sqlInOperator .= ', ';
            }
        }

        $query = GET_SELECTED_TAGS_LIST_SQL . '(' . $sqlInOperator . ')';

        return $this->selectData($query, $types, $ids);
    }

    public function getTagsList()
    {
        return $this->selectData(GET_TAGS_LIST_SQL);
    }

    public function createTags($tags = [])
    {
        foreach ($tags as $tag) {
            $params = [$tag];

            $this->modifyData(CREATE_TAGS_SQL, 's', $params);
        }
    }
}
