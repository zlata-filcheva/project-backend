<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const GET_TAG_SQL = <<<'SQL'
SELECT 
    id,
    name
FROM tags 
WHERE id = ?
SQL;

const GET_SELECTED_TAGS_LIST_BEGINNING_SQL = <<<'SQL'
SELECT 
    id, 
    name 
FROM tags 
WHERE id IN
SQL;

const GET_SELECTED_TAGS_LIST_END_SQL = <<<'SQL'
GROUP BY id
HAVING COUNT(*) = 1;
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
) VALUES
SQL;

class TagModel extends Database
{
    public function getTag($id)
    {
        return $this->selectData(GET_TAG_SQL, 'i', [$id]);
    }

    public function getSelectedTagsList($ids)
    {
        $sqlWhereInOperator = '';
        $query = '';

        $types = str_repeat('i', count($ids));
        $idsLength = count($ids);

        for ($i = 0; $i < $idsLength; $i++) {
            $sqlWhereInOperator .= '?';

            if (($i < $idsLength - 1)) {
                $sqlWhereInOperator .= ', ';
            }
        }

        $query .= GET_SELECTED_TAGS_LIST_BEGINNING_SQL;
        $query .= " (";
        $query .= $sqlWhereInOperator;
        $query .= ') ';
        $query .= GET_SELECTED_TAGS_LIST_END_SQL;

        
        echo $query;
        echo "\n";
        echo $types;
        echo "\n";
        print_r($ids);
        
        return $this->selectData($query, $types, $ids);
    }

    public function getTagsList()
    {
        return $this->selectData(GET_TAGS_LIST_SQL);
    }

    public function createTags($tags = [])
    {
        $query = CREATE_TAGS_SQL;
        $types = '';
        $params = $tags;

        $tagsLength = count($tags);

        for ($i = 0; $i < $tagsLength; $i++) {
            $query .= ' ( ? )';
            $types .= 's';

            if (($i < $tagsLength - 1)) {
                $query .= ',';
            }
        }

        return $this->modifyData($query, $types, $params);
    }
}
