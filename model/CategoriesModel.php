<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";

const GET_CATEGORIES_SQL = <<<'SQL'
SELECT 
    id, 
    name
FROM categories 
ORDER BY name ASC 
SQL;

class CategoriesModel extends Database
{
    public function getCategories()
    {
        return $this->select(GET_CATEGORIES_SQL);
    }
}