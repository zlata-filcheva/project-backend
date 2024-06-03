<?php

const ALLOWED_URI = ["categories", "comments", "posts", "tags", "users"];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$hasDevelopmentMode = $_SERVER['SERVER_NAME'] === '127.0.0.1';

$controllerUri = $hasDevelopmentMode ? $uri[3] : $uri[1];

if (!isset($controllerUri) || !in_array($controllerUri, ALLOWED_URI)) {
    header("HTTP/1.1 404 Not Found");

    exit();
}

require __DIR__ . "/inc/bootstrap.php";
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

if ($controllerUri === "categories") {
    $categoryController->get();
}

if ($controllerUri === "comments") {
    $commentController->get();
}

if ($controllerUri === "posts") {
    $postController->get();
}

if ($controllerUri === "tags") {
    $tagController->get();
}

if ($controllerUri === "users") {
    $userController->get();
}