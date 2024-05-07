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

        $arrQueryStringParams = $this->getQueryStringParams();

        try {
            $model = new UserModel();

            $id = $uri[4];

            $response = $model->getUser($id);
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
        $expectedPostVariables = [
            $_POST['id'],
            $_POST['nickName'],
            $_POST['name'],
            $_POST['surname'],
        ];

        foreach ($expectedPostVariables as $value) {
            if (!isset($value)) {
                $this->sendStatusCode422();

                return;
            }
        }

        try {
            $model = new UserModel();

            $id = $_POST['id'];
            $nickName = $_POST['nickName'];
            $name = $_POST['name'];
            $surname = $_POST['surname'];

            $uri = $this->getUri();

            $hasUser = $this->hasUser($id);

            if ($hasUser) {
                $this->sendStatusCode422();

                return;
            }

            $model->createUser($id, $nickName, $name, $surname);
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