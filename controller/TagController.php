<?php

class TagController extends BaseController
{
    public function hasTags($ids): bool
    {
        if (count($ids) > 5) {
            return false;
        }

        $model = new TagModel();

        $response = $model->getSelectedTagsList($ids);

        return (count($response) > 0);
    }

    public function get()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) === 'GET') {
            $this->getTagsList();

            return;
        }

        if (strtoupper($requestMethod) === 'POST') {
            $this->createTag();

            return;
        }

        $this->sendStatusCode422();
    }

    public function getTagsList()
    {
        $responseData = "";
        $httpResponseHeader = "";

        try {
            $model = new TagModel();

            $response = $model->getTagsList();

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

    public function createTag()
    {
        $responseData = "";
        $httpResponseHeader = "";
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (!isset($_POST["tags"])) {
            $this->sendStatusCode422();

            return;
        }

        try {
            $model = new TagModel();

            $tags = $_POST["tags"];

            $output = $model->createTags($tags);

            $insertTagIds = [];

            $insertId = $output['insert_id'];
            $affected_rows = $output['affected_rows'];

            for ($i = $insertId; $i < $insertId + $affected_rows; $i++) {
                $insertTagIds = [...$insertTagIds, $i];
            }

            $response = $model->getSelectedTagsList($insertTagIds);

            $responseData = json_encode($response);
            $httpResponseHeader = $this->getStatusHeader201();
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