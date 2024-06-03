<?php
const PROJECT_ROOT_PATH = __DIR__ . "/../";

require PROJECT_ROOT_PATH . "/inc/cors.php";
require PROJECT_ROOT_PATH . "/inc/config.php";

require PROJECT_ROOT_PATH . "/controller/BaseController.php";

require PROJECT_ROOT_PATH . "/model/CategoryModel.php";
require PROJECT_ROOT_PATH . "/model/CommentModel.php";
require PROJECT_ROOT_PATH . "/model/PostModel.php";
//require PROJECT_ROOT_PATH . "/model/TagModel.php";
require PROJECT_ROOT_PATH . "/model/UserModel.php";