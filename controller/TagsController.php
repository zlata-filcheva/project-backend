<?php

class TagsController extends BaseController
{
    public function get()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) !== 'GET') {
            $this->sendOutput(
                json_encode($this->RESPONSE_DATA_DECODED_422),
                $this->HEADERS_422
            );

            return;
        }

        try {
            $model = new TagsModel();

            $response = $model->getTags();

            $responseData = json_encode($response);
            $httpResponseHeader = $this->HEADERS_200;
        }
        catch (Error $e) {
            $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support.';

            $responseData = json_encode(['error' => $strErrorDesc]);
            $httpResponseHeader = $this->HEADERS_500;

        }
        finally {
            $this->sendOutput($responseData, $httpResponseHeader);
        }
    }

    public function add()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) !== 'POST' && !isset($_POST["tags"])) {
            $this->sendOutput(
                json_encode($this->RESPONSE_DATA_DECODED_422),
                $this->HEADERS_422
            );

            return;
        }

        try {
            $model = new TagsModel();

            $tags = $_POST["tags"];

            $response = $model->createTags($tags);

            $responseData = json_encode($response);
            $httpResponseHeader = $this->HEADERS_200;
        }
        catch (Error $e) {
            $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support.';

            $responseData = json_encode(['error' => $strErrorDesc]);
            $httpResponseHeader = $this->HEADERS_500;

        }
        finally {
            $this->sendOutput($responseData, $httpResponseHeader);
        }
    }
}