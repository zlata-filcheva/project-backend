<?php
require __DIR__ . "/inc/bootstrap.php";

const ALLOWED_URI = ["category", "comment", "post", "tag", "user"];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if (!isset($uri[3]) || !in_array($uri[3], ALLOWED_URI) || !isset($uri[4])) {
    header("HTTP/1.1 404 Not Found");

    exit();
}

require PROJECT_ROOT_PATH . "/controller/CategoryController.php";
require PROJECT_ROOT_PATH . "/controller/CommentController.php";
require PROJECT_ROOT_PATH . "/controller/PostController.php";
require PROJECT_ROOT_PATH . "/controller/TagController.php";
require PROJECT_ROOT_PATH . "/controller/UserController.php";

$categoryController = new CategoryController();
$commentController = new CommentController();
$postController = new PostController();
$tagController = new TagController();
$userController = new UserController();

$strMethodName = $uri[4];

if ($uri[3] === "category") {
    $categoryController->{$strMethodName}();
}

if ($uri[3] === "comment") {
    $commentController->{$strMethodName}();
}

if ($uri[3] === "post") {
    $postController->{$strMethodName}();
}

if ($uri[3] === "tag") {
    $tagController->{$strMethodName}();
}

if ($uri[3] === "user") {
    $userController->{$strMethodName}();
}