<?php

class UserController extends BaseController
{
    public function has()
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
            if (!isset($arrQueryStringParams['oauthId']) && !$arrQueryStringParams['oauthId']) {
                throw new Error('No oauthId!');
            }

            $model = new UserModel();

            ['oauthId' => $oauthId] = $arrQueryStringParams;

            $arrUsers = $model->hasUser($oauthId);

            $responseData = json_encode($arrUsers[0]);
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

    public function add()
    {
        $responseData = "";
        $httpResponseHeader = "";
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) !== 'POST' && !isset($_POST["user"])) {
            $this->sendOutput(
                json_encode(self::RESPONSE_DATA_DECODED_422),
                self::HEADERS_422
            );

            return;
        }

        try {
            $model = new UserModel();

            $user = $_POST["user"];

            $response = $model->createUser($user);

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