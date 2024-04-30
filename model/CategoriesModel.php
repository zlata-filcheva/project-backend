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
        return $this->select(GET_CATEGORIES_SQL);
    }

    public function createCategories($categories = [])
    {
        foreach ($categories as $category) {
            $this->insert(CREATE_CATEGORIES_SQL, 's', [$category]);
        }
    }
}