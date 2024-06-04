<?php

$hasDevelopmentMode = $_SERVER['SERVER_NAME'] === '127.0.0.1';

if (!$hasDevelopmentMode) {
    //return;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: token, Content-Type');
    header('Access-Control-Max-Age: 86400');
    //header('Content-Length: 0');
    //header('Content-Type: text/plain');
    die();
}

header('Access-Control-Allow-Origin: https://127.0.0.1:5173');
header('Content-Type: application/json');