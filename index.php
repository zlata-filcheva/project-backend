<?php
require __DIR__ . "/inc/bootstrap.php";

const ALLOWED_URI = ["categories", "comments", "posts", "tags", "users"];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if (!isset($uri[3]) || !in_array($uri[3], ALLOWED_URI)) {
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

if ($uri[3] === "categories") {
    $categoryController->get();
}

if ($uri[3] === "comments") {
    $commentController->get();
}

if ($uri[3] === "posts") {
    $postController->get();
}

if ($uri[3] === "tags") {
    $tagController->get();
}

if ($uri[3] === "users") {
    $userController->get();
}