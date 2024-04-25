<?php

use JetBrains\PhpStorm\NoReturn;

class BaseController
{
    const FRONT_END_URI = "https://127.0.0.1:5173";
    const SUCCESS_HEADERS = [
        "Content-Type: application/json",
        "HTTP/1.1 200 OK",
        "Access-Control-Allow-Origin: " . BaseController::FRONT_END_URI,
        "Access-Control-Allow-Methods: GET",
        "Access-Control-Allow-Headers: Content-Type",
        "Access-Control-Allow-Credentials: true",
        "Access-Control-Max-Age: 86400"
    ];


    #[NoReturn] public function __call($name, $arguments)
    {
        $this->sendOutput('', array('HTTP/1.1 404 Not Found'));
    }

    protected function getQueryStringParams()
    {
        parse_str($_SERVER['QUERY_STRING'], $query);

        return $query;
    }

    #[NoReturn] protected function sendOutput($data, $httpHeaders=array())
    {
        header_remove('Set-Cookie');

        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }

        echo $data;
        exit;
    }
}