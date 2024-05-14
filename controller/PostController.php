<?php

class PostController extends BaseController
{
    public function restoreInitialData($initialData) {
        $outputData = [];

        foreach ($initialData as $key => $object) {
            $newObject = [];
            $newTagIds = [];

            foreach ($object as $objectKey => $value) {
                $newObject[$objectKey] = stripslashes($value);
            }

            $tagIds = json_decode($newObject['tagIds'], true);

            foreach ($tagIds as $value) {
                $newTagIds = [...$newTagIds, $value['tagId']];
            }

            $newObject['tagIds'] = $newTagIds;

            $outputData[$key] = $newObject;
        }

        return $outputData;
    }

    public function hasPost($id, $userId): bool
    {
        $model = new PostModel();

        $response = $model->getPost($id, $userId);

        return (count($response) > 0);
    }

    public function get()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) === 'GET') {
            $this->getPostsList();

            return;
        }

        if (strtoupper($requestMethod) === 'POST') {
            $this->createPost();

            return;
        }

        if (strtoupper($requestMethod) === 'PATCH') {
            $this->updatePost();

            return;
        }

        $this->sendStatusCode422();
    }

    public function getPostsList()
    {
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

            $normalizedData = $this->restoreInitialData($response);

            $responseData = json_encode($normalizedData);
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

    public function createPost()
    {
        $responseData = "";
        $httpResponseHeader = "";



        try {
            $model = new PostModel();

            $userController = new UserController();
            $categoryController = new CategoryController();
            $tagController = new TagController();

            $inputData = file_get_contents('php://input');
            $decodedData = json_decode($inputData);

            $title = $decodedData->title ?? '';
            $content = $decodedData->content ?? '';
            $categoryId = $decodedData->categoryId ?? '';
            $userId = $decodedData->userId ?? '';
            $tagIds = $decodedData->tagIds ?? '';

            if (
                !(strlen($title) > 0)
                || !(strlen($content) > 0)
                || !(strlen($categoryId) > 0)
                || !(strlen($userId) > 0)
                || !(count($tagIds) > 0)
            ) {
                $this->sendStatusCode422();

                return;
            }

            $uri = $this->getUri();

            $hasUser = $userController->hasUser($userId);
            $hasCategory = $categoryController->hasCategory($categoryId);
            $hasTags = $tagController->hasTags($tagIds);

            if (!$hasUser || !$hasCategory || !$hasTags) {
                $this->sendStatusCode422();

                return;
            }

            $assocTagIds = [];

            foreach ($tagIds as $value) {
                $assocTagIds[] = ["tagId" => $value];
            }

            $output = $model->createPost($content, $title, $categoryId, $userId, $assocTagIds);
            $insertId = $output['insert_id'];

            $response = $model->getPost($insertId);

            $normalizedData = $this->restoreInitialData($response);

            $responseData = json_encode($normalizedData[0]);
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

    public function updatePost()
    {
        $responseData = "";
        $httpResponseHeader = "";

        $inputData = file_get_contents('php://input');
        $parsedData = $this->parseFormData($inputData);

        $expectedPutKeys = ['postId', 'userId'];
        $expectedOptionalKeys = ['content', 'title', 'tagIds'];
        $hasOptionalKey = false;

        foreach ($expectedPutKeys as $value) {
            if (!array_key_exists($value, $parsedData)) {
                $this->sendStatusCode422();

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
            $this->sendStatusCode422();

            return;
        }

        try {
            $model = new PostModel();

            $postId = $parsedData['postId'];
            $userId = $parsedData['userId'];

            $isAuthor = $this->hasPost($postId, $userId);

            if (!$isAuthor) {
                $this->sendStatusCode422();

                return;
            }

            if (isset($parsedData['content']) || isset($parsedData['title'])) {
                $content = $parsedData['content'] ?? '';
                $title = $parsedData['title'] ?? '';

                $model->updatePostContent($content, $title, $postId, $userId);
            }

            if (isset($parsedData['tagIds'])) {
                $tagIds = $parsedData['tagIds'];

                $tagController = new TagController();

                $hasTags = $tagController->hasTags($tagIds);

                if (!$hasTags) {
                    $this->sendStatusCode422();

                    return;
                }

                $assocTagIds = [];

                foreach ($tagIds as $value) {
                    $assocTagIds[] = ["tagId" => $value];
                }

                $model->updatePostTags($assocTagIds, $postId, $userId);
            }

            $postData = $model->getPost($postId);

            $normalizedData = $this->restoreInitialData($postData);

            $responseData = json_encode($normalizedData[0]);
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