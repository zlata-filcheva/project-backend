<?php

class UserController extends BaseController
{
    public function check()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        $responseData = "";

        if (strtoupper($requestMethod) == 'GET') {
            try {
                if (!isset($arrQueryStringParams['email']) && $arrQueryStringParams['email']) {
                    throw new Error('No email and password!');
                }

                if (!isset($arrQueryStringParams['password']) && $arrQueryStringParams['password']) {
                    throw new Error('No email and password!');
                }

                $userModel = new UserModel();

                [
                    'email' => $email,
                    'password' => $password
                ] = $arrQueryStringParams;

                $arrUsers = $userModel->hasUser($email, $password);
                $responseData = json_encode($arrUsers);
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        // send output
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }
}