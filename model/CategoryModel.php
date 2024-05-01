<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const GET_CATEGORY_SQL = <<<'SQL'
SELECT 
    id,
    name
FROM categories 
WHERE id = ?
SQL;

const GET_CATEGORIES_LIST_SQL = <<<'SQL'
SELECT 
    id, 
    name
FROM categories 
ORDER BY name ASC 
SQL;

const CREATE_CATEGORY_SQL = <<<'SQL'
INSERT INTO categories (
    name
) VALUES (?)
SQL;

class CategoryModel extends Database
{
    public function getCategory($id)
    {
        return $this->selectData(GET_CATEGORY_SQL, 'i', [$id]);
    }

    public function getCategoriesList()
    {
        return $this->selectData(GET_CATEGORIES_LIST_SQL);
    }

    public function createCategory($category = '')
    {
        $this->modifyData(CREATE_CATEGORY_SQL, 's', [$category]);
    }
}