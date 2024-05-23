<?php

/* Handle CORS */
// Specify domains from which requests are allowed
//header('Access-Control-Allow-Origin: https://127.0.0.1:5173');
// Specify which request methods are allowed
//header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
// Additional headers which may be sent along with the CORS request
//header('Access-Control-Allow-Headers: X-Requested-With,Authorization,Content-Type');
// Set the age to 1 day to improve speed/caching.
//header('Access-Control-Max-Age: 86400');

// Exit early so the page isn't fully loaded for options requests
//if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
    //exit();
//}

const PROJECT_ROOT_PATH = __DIR__ . "/../";

require PROJECT_ROOT_PATH . "/inc/config.php";

require PROJECT_ROOT_PATH . "/controller/BaseController.php";

require PROJECT_ROOT_PATH . "/model/CategoryModel.php";
require PROJECT_ROOT_PATH . "/model/CommentModel.php";
require PROJECT_ROOT_PATH . "/model/PostModel.php";
require PROJECT_ROOT_PATH . "/model/TagModel.php";
require PROJECT_ROOT_PATH . "/model/UserModel.php";