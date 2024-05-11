<?php

class UserController extends BaseController
{
    public function hasUser($id): bool
    {
        $model = new UserModel();

        $response = $model->getUser($id);

        return (count($response) > 0);
    }

    public function get()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) === 'GET') {
            $this->getUser();

            return;
        }

        if (strtoupper($requestMethod) === 'POST') {
            $this->createUser();

            return;
        }

        $this->sendStatusCode422();
    }

    public function getUser()
    {
        $strErrorDesc = '';
        $responseData = "";
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );

        if (!array_key_exists(4, $uri)) {
            $this->sendStatusCode422();

            return;
        }

        try {
            $model = new UserModel();

            $id = $uri[4];
            $decodedId = urldecode($id);
            $lowerCaseId = strtolower($decodedId);

            $response = $model->getUser($lowerCaseId);
            $user = $response[0];

            $responseData = json_encode($user);
            $httpResponseHeader = self::HEADERS_200;
        }
        catch (Error $e) {
            $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support.';

            $responseData = json_encode(['error' => $strErrorDesc]);
            $httpResponseHeader = self::HEADERS_500;
        }
        finally {
            $this->sendOutput($responseData, $httpResponseHeader);
        }
    }

    public function createUser()
    {
        $responseData = "";
        $httpResponseHeader = "";

        try {
            $model = new UserModel();

            $inputData = file_get_contents('php://input');
            $decodedData = json_decode($inputData);

            $id = $decodedData->id ?? '';
            $nickname = $decodedData->nickname ?? '';
            $name = $decodedData->name ?? '';
            $surname = $decodedData->surname ?? '';

            $nickname = strtolower($nickname);

            if (
                !(strlen($id) > 0)
                || !(strlen($nickname) > 0)
                || !(strlen($name) > 0)
                || !(strlen($surname) > 0)
            ) {
                $this->sendStatusCode422();

                return;
            }

            $uri = $this->getUri();

            $hasUser = $this->hasUser($id);

            if ($hasUser) {
                $this->sendStatusCode422();

                return;
            }

            $model->createUser($id, $nickname, $name, $surname);
            $response = $model->getUser($id);

            $responseData = json_encode($response[0]);
            $httpResponseHeader = $this->getStatusHeader201($uri[3], $id);
        }
        catch (Error $e) {
            $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support.';

            $responseData = json_encode(['error' => $strErrorDesc]);
            $httpResponseHeader = self::HEADERS_500;

        }
        finally {
            $this->sendOutput($responseData, $httpResponseHeader);
        }
    }
}