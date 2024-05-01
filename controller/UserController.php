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
        $strErrorDesc = '';
        $responseData = "";
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) !== 'GET') {
            $this->sendOutput(
                json_encode(self::RESPONSE_DATA_DECODED_422),
                self::HEADERS_422
            );

            return;
        }

        $arrQueryStringParams = $this->getQueryStringParams();

        try {
            if (!isset($arrQueryStringParams['id']) && !$arrQueryStringParams['id']) {
                throw new Error('No oauthId!');
            }

            $model = new UserModel();

            ['id' => $id] = $arrQueryStringParams;

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

    public function create()
    {
        $responseData = "";
        $httpResponseHeader = "";
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) !== 'POST') {
            $this->sendOutput(
                json_encode(self::RESPONSE_DATA_DECODED_422),
                self::HEADERS_422
            );

            return;
        }

        $expectedPostVariables = [
            $_POST['id'],
            $_POST['nickName'],
            $_POST['name'],
            $_POST['surname'],
        ];

        foreach ($expectedPostVariables as $value) {
            if (!isset($value)) {
                $this->sendOutput(
                    json_encode(self::RESPONSE_DATA_DECODED_422),
                    self::HEADERS_422
                );

                return;
            }
        }

        try {
            $model = new UserModel();

            $id = $_POST['id'];
            $nickName = $_POST['nickName'];
            $name = $_POST['name'];
            $surname = $_POST['surname'];

            $response = $model->createUser($id, $nickName, $name, $surname);

            $responseData = json_encode($response);
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
}