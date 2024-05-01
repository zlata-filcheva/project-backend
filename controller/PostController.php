<?php

class PostController extends BaseController
{
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
            if (!isset($arrQueryStringParams['rowCount']) && !$arrQueryStringParams['rowCount']) {
                throw new Error('No rowCount');
            }

            if (!isset($arrQueryStringParams['offset']) && !$arrQueryStringParams['offset']) {
                throw new Error('No offset');
            }

            $model = new PostModel();

            [
                'rowCount' => $rowCount,
                'offset' => $offset
            ] = $arrQueryStringParams;

            $response = $model->getPosts($rowCount, $offset);

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
            $_POST['content'],
            $_POST['topic'],
            $_POST['categoryId'],
            $_POST['userId'],
            $_POST['tagIds']
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
            $model = new PostModel();

            $userController = new UserController();
            $categoryController = new CategoryController();
            $tagController = new TagController();

            $content = $_POST['content'];
            $topic = $_POST['topic'];
            $categoryId = $_POST['categoryId'];
            $userId = $_POST['userId'];
            $tagIds = $_POST['tagIds'];

            $hasUser = $userController->hasUser($userId);

            if (!$hasUser) {
                $this->sendOutput(
                    json_encode(self::RESPONSE_DATA_DECODED_422),
                    self::HEADERS_422
                );

                return;
            }

            $hasCategory = $categoryController->hasCategory($categoryId);

            if (!$hasCategory) {
                $this->sendOutput(
                    json_encode(self::RESPONSE_DATA_DECODED_422),
                    self::HEADERS_422
                );

                return;
            }

            $hasTags = $tagController->hasTags($tagIds);

            if (!$hasTags) {
                $this->sendOutput(
                    json_encode(self::RESPONSE_DATA_DECODED_422),
                    self::HEADERS_422
                );

                return;
            }

            $assoc_array = array();

            foreach ($tagIds as $value) {
                $assoc_array[] = array("tagId" => $value);
            }

            $response = $model->createPost($content, $topic, $categoryId, $userId, $assoc_array);

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