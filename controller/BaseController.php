<?php

use JetBrains\PhpStorm\NoReturn;

class BaseController
{
    const FRONT_END_URI = "https://127.0.0.1:5173";

    const HEADERS_200 = [
        "HTTP/1.1 200 OK",
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

    #[NoReturn] protected function sendOutput($data, $httpHeaders = [])
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

    protected function parseFormData($formData) {
        $data = [];
        $array = [];
        $matches = [];
        preg_match_all('/name="([^"]+)"\s*\r?\n\r?\n([^\r\n]*)/', $formData, $matches);

        for ($i = 0; $i < count($matches[0]); $i++) {
            $name = $matches[1][$i];
            $value = $matches[2][$i];

            if (str_contains($name, '[')) {
                $name = explode("[", $name)[0];

                $array[$name] = [...$array[$name] ?? [], $value];
                $data[$name] = $array[$name];

                continue;
            }

            $data[$name] = trim($value);
        }

        return $data;
    }

    protected function getStatusHeader201($path = '', $value = '')
    {
        $locationHeader = strlen($path) > 0
            ? "Location: " . BaseController::FRONT_END_URI . "/" . $path . "/" . $value
            : '';

        return [
            "HTTP/1.1 201 Created",
            $locationHeader,
            "Cache-Control: no-cache",
        ];
    }

    protected function sendStatusCode422($message = '')
    {
        $this->sendOutput(
            $message ?? json_encode(self::RESPONSE_DATA_DECODED_422),
            self::HEADERS_422
        );
    }

    protected function getUri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        return explode( '/', $uri );
    }

    public function restoreInitialData($initialData) {
        $outputData = [];

        foreach ($initialData as $key => $object) {
            if (!is_array($object)) {
                $outputData[$key] = $object;
                
                continue;
            }

            $newObject = [];

            foreach ($object as $objectKey => $objValue) {
                $decodedData = json_decode($objValue, true);

                if (!is_array($decodedData)) {
                    $newObject[$objectKey] = stripslashes($objValue);

                    continue;
                }

                foreach ($decodedData as $arrKey => $arrValue) {
                    foreach ($arrValue as $item) {
                        $decodedData[$arrKey] = $item;
                    }
                }

                $newObject[$objectKey] = $decodedData;
            }

            $outputData[$key] = $newObject;
        }

        return $outputData;
    }
}