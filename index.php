<?php
require __DIR__ . "/inc/bootstrap.php";

const ALLOWED_URI = ["user", "tags", "categories"];
//const PROJECT_ROOT_PATH = __DIR__ . "/../";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if (!isset($uri[3]) || !in_array($uri[3], ALLOWED_URI) || !isset($uri[4])) {
    header("HTTP/1.1 404 Not Found");

    exit();
}


//require PROJECT_ROOT_PATH . "/controller/UserController.php";
//require PROJECT_ROOT_PATH . "/controller/CategoriesController.php";
require PROJECT_ROOT_PATH . "/controller/TagsController.php";

//$userController = new UserController();
//$categoriesFeedController = new CategoriesController();
$tagsFeedController = new TagsController();

/*
$objFeedController = new UserController();
$strMethodName = $uri[4];
$objFeedController->{$strMethodName}();
*/

$controller = $uri[3] . 'Controller';
$strMethodName = $uri[4];


//$controller->{$strMethodName}();


$tagsFeedController->{$strMethodName}();
