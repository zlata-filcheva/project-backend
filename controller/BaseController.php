<?php

use JetBrains\PhpStorm\NoReturn;

class BaseController
{
    const FRONT_END_URI = "https://127.0.0.1:5173";

    const HEADERS_200 = [
        "Content-Type: application/json",
        "HTTP/1.1 200 OK",
        "Access-Control-Allow-Origin: " . BaseController::FRONT_END_URI,
        "Access-Control-Allow-Methods: GET",
        "Access-Control-Allow-Headers: Content-Type",
        "Access-Control-Allow-Credentials: true",
        "Access-Control-Max-Age: 86400"
    ];

    const RESPONSE_DATA_DECODED_422 = ['error' => 'Method not supported'];
    const HEADERS_422 = ['Content-Type: application/json', 'HTTP/1.1 422 Unprocessable Entity'];

    const HEADERS_500 = ['Content-Type: application/json', 'HTTP/1.1 500 Internal Server Error'];

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
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        header_remove('Set-Cookie');

        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }

        if (strtoupper($requestMethod) === 'GET') {
            echo $data;
        }

        exit;
    }

    protected function parseFormData($formData) {
        $data = [];
        $matches = [];
        preg_match_all('/name="([^"]+)"\s*\r?\n\r?\n([^\r\n]*)/', $formData, $matches);

        for ($i = 0; $i < count($matches[0]); $i++) {
            $name = $matches[1][$i];
            $value = $matches[2][$i];
            $data[$name] = trim($value);
        }

        return $data;
    }
}