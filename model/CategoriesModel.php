<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const GET_CATEGORIES_SQL = <<<'SQL'
SELECT 
    id, 
    name
FROM categories 
ORDER BY name ASC 
SQL;

const CREATE_CATEGORIES_SQL = <<<'SQL'
INSERT INTO categories (
    name
) VALUES (?)
SQL;

class CategoriesModel extends Database
{
    public function getCategories()
    {
        return $this->selectData(GET_CATEGORIES_SQL);
    }

    public function createCategories($categories = [])
    {
        foreach ($categories as $category) {
            $this->modifyData(CREATE_CATEGORIES_SQL, 's', [$category]);
        }
    }
}