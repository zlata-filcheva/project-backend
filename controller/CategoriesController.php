<?php

class CategoriesController extends BaseController
{
    public function get()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $responseData = "";

        if (strtoupper($requestMethod) == 'GET') {
            try {
                $model = new CategoriesModel();

                $response = $model->getCategories();
                $responseData = json_encode($response);
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
                array(
                    'Content-Type: application/json',
                    'HTTP/1.1 200 OK',
                    "Access-Control-Allow-Origin: https://127.0.0.1:5173",
                    "Access-Control-Allow-Methods: GET",
                    "Access-Control-Allow-Headers: Content-Type",
                    'Access-Control-Allow-Credentials: true',
                    'Access-Control-Max-Age: 86400'
                )
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }
}