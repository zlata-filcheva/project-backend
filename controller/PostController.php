<?php

class PostController extends BaseController
{
    public function hasPost($id): bool
    {
        $model = new PostModel();

        $response = $model->getPost($id);

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

            $response = $model->getPostsList($rowCount, $offset);

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

            $assocTagIds = [];

            foreach ($tagIds as $value) {
                $assocTagIds[] = ["tagId" => $value];
            }

            $response = $model->createPost($content, $topic, $categoryId, $userId, $assocTagIds);

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

    public function update()
    {
        $response = "";
        $responseData = "";
        $httpResponseHeader = "";
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) !== 'PUT') {
            $this->sendOutput(
                json_encode(self::RESPONSE_DATA_DECODED_422),
                self::HEADERS_422
            );

            return;
        }

        $inputData = file_get_contents('php://input');
        $parsedData = $this->parseFormData($inputData);

        $expectedPutKeys = ['postId', 'userId'];
        $expectedOptionalKeys = ['content', 'tagIds'];
        $hasOptionalKey = false;

        foreach ($expectedPutKeys as $value) {
            if (!array_key_exists($value, $parsedData)) {
                $this->sendOutput(
                    json_encode(self::RESPONSE_DATA_DECODED_422),
                    self::HEADERS_422
                );

                return;
            }
        }

        foreach ($expectedOptionalKeys as $value) {
            if (array_key_exists($value, $parsedData)) {
                $hasOptionalKey = true;

                break;
            }
        }

        if (!$hasOptionalKey) {
            $this->sendOutput(
                json_encode(self::RESPONSE_DATA_DECODED_422),
                self::HEADERS_422
            );

            return;
        }

        try {
            $model = new PostModel();

            $postId = $parsedData['postId'];
            $userId = $parsedData['userId'];

            $isAuthor = $this->hasPost($postId, $userId);

            if (!$isAuthor) {
                $this->sendOutput(
                    json_encode(self::RESPONSE_DATA_DECODED_422),
                    self::HEADERS_422
                );

                return;
            }

            if (isset($parsedData['content'])) {
                $content = $parsedData['content'];

                $response = $model->updatePostContent($content, $postId, $userId);
            }

            if (isset($parsedData['tagIds'])) {
                $tagIds = $parsedData['tagIds'];

                $tagController = new TagController();

                $hasTags = $tagController->hasTags($tagIds);

                if (!$hasTags) {
                    $this->sendOutput(
                        json_encode(self::RESPONSE_DATA_DECODED_422),
                        self::HEADERS_422
                    );

                    return;
                }

                $assocTagIds = [];

                foreach ($tagIds as $value) {
                    $assocTagIds[] = ["tagId" => $value];
                }

                $response = $model->updatePostTags($assocTagIds, $postId, $userId);
            }

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