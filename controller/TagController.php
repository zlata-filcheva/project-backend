<?php

class TagController extends BaseController
{
    public function getSelectedTagsList($ids)
    {
        $model = new TagModel();

        return $model->getSelectedTagsList($ids);
    }

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
        
        echo $requestMethod;
        
        if (strtoupper($requestMethod) === 'GET') {
            echo 333;
            
            $this->getTagsList();

            return;
        }

        if (strtoupper($requestMethod) === 'POST') {
            $this->createTags();

            return;
        }

        $this->sendStatusCode422();
    }

    public function getTagsList()
    {
        $responseData = '';
        $httpResponseHeader = '';
        
        try {
            $model = new TagModel();

            echo 111;
            
            $response = $model->getTagsList();

            echo 222;
            
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

    public function createTags()
    {
        $responseData = '';
        $httpResponseHeader = '';
        
        try {
            $model = new TagModel();

            $inputData = file_get_contents('php://input');
            $decodedData = json_decode($inputData);

            $tags = $decodedData->tags ?? '';

            foreach ($tags as &$value) {
                $value = strtolower($value);
            }

            unset($value);

            if (!(count($tags) > 0)) {
                $this->sendStatusCode422();

                return;
            }

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