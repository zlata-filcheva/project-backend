<?php

class CategoriesController extends BaseController
{
    public function get()
    {
        $responseData = "";
        $httpResponseHeader = "";
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) !== 'GET') {
            $this->sendOutput(
                json_encode(self::RESPONSE_DATA_DECODED_422),
                self::HEADERS_422
            );

            return;
        }

        try {
            $model = new CategoriesModel();

            $response = $model->getCategories();
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

        if (strtoupper($requestMethod) !== 'POST' && !isset($_POST["tags"])) {
            $this->sendOutput(
                json_encode(self::RESPONSE_DATA_DECODED_422),
                self::HEADERS_422
            );

            return;
        }

        try {
            $model = new CategoriesModel();

            $categories = $_POST["categories"];

            $response = $model->createTags($categories);

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