<?php

class CategoryController extends BaseController
{
    public function hasCategory($id): bool
    {
        $model = new CategoryModel();

        $response = $model->getCategory($id);

        return (count($response) > 0);
    }

    public function get()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) === 'GET') {
            $this->getCategoriesList();

            return;
        }

        if (strtoupper($requestMethod) === 'POST') {
            $this->createCategory();

            return;
        }

        $this->sendStatusCode422();
    }

    public function getCategoriesList()
    {
        $responseData = "";
        $httpResponseHeader = "";

        try {
            $model = new CategoryModel();

            $response = $model->getCategoriesList();

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

    public function createCategory()
    {
        if (!isset($_POST["category"])) {
            $this->sendStatusCode422();

            return;
        }

        try {
            $model = new CategoryModel();

            $category = $_POST["category"];

            $uri = $this->getUri();

            $output = $model->createCategory($category);
            $insertId = $output['insert_id'];
            $response = $model->getCategory($insertId);

            $responseData = json_encode($response[0]);
            $httpResponseHeader = $this->getStatusHeader201($uri[3], $insertId);
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