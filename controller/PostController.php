<?php

class PostController extends BaseController
{
    public function hasPost($id, $userId = ''): bool
    {
        $model = new PostModel();

        $response = $model->getPost($id, $userId);

        return (count($response) > 0);
    }

    public function get()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
        [$controllerUri, $hasInfoUri, $infoUri] = $this->getUri();

        if (strtoupper($requestMethod) === 'GET') {
            if ($hasInfoUri) {
                if ($infoUri === 'count') {
                    $this->getPostsCount();

                    return;
                }

                $this->getPost($infoUri);
                
                return;
            }

            $this->getPostsList();

            return;
        }

        if (strtoupper($requestMethod) === 'POST') {
            $this->createPost();

            return;
        }

        if (strtoupper($requestMethod) === 'PUT') {
            $this->updatePost();

            return;
        }

        if (strtoupper($requestMethod) === 'DELETE') {
            $this->deletePost();

            return;
        }

        $this->sendStatusCode422();
    }

    public function getPost($id) {
        $responseData = '';
        $httpResponseHeader = '';
        
        try {
            $model = new PostModel();

            $tagController = new TagController();
            
            $response = $model->getPost($id);

            $normalizedData = $this->restoreInitialData($response);
            $normalizedData = $normalizedData[0];

            $tagIds = $normalizedData['tagIds'];
            $tagList = $tagController->getSelectedTagsList($tagIds);

            $normalizedData['tagList'] = $tagList;

            $normalizedData['user']['id'] = $normalizedData['userId'];
            $normalizedData['user']['name'] = $normalizedData['userName'];
            $normalizedData['user']['picture'] = $normalizedData['userPicture'];

            unset($normalizedData['tagIds']);

            unset($normalizedData['userId']);
            unset($normalizedData['userName']);
            unset($normalizedData['userPicture']);

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

    public function getPostsList()
    {
        $responseData = '';
        $httpResponseHeader = '';
        
        $arrQueryStringParams = $this->getQueryStringParams();

        try {
            if (!isset($arrQueryStringParams['rowCount'])) {
                $this->sendStatusCode422('No rowCount');

                return;
            }

            if (!isset($arrQueryStringParams['offset'])) {
                $this->sendStatusCode422('No offset');

                return;
            }

            $model = new PostModel();

            $tagController = new TagController();

            [
                'rowCount' => $rowCount,
                'offset' => $offset
            ] = $arrQueryStringParams;

            $response = $model->getPostsList($rowCount, $offset);

            $normalizedData = $this->restoreInitialData($response);

            foreach ($normalizedData as &$value) {
                $tagIds = $value['tagIds'];

                $tagList = $tagController->getSelectedTagsList($tagIds);

                $value['tagList'] = $tagList;

                $value['user']['id'] = $value['userId'];
                $value['user']['name'] = $value['userName'];
                $value['user']['picture'] = $value['userPicture'];

                unset($value['tagIds']);

                unset($value['userId']);
                unset($value['userName']);
                unset($value['userPicture']);
            }

            unset($value);

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

    public function getPostsCount() {
        $responseData = '';
        $httpResponseHeader = '';
        
        try {
            $arrQueryStringParams = $this->getQueryStringParams();

            if (!isset($arrQueryStringParams['rowCount'])) {
                $this->sendStatusCode422('No rowCount');

                return;
            }

            ['rowCount' => $rowCount] = $arrQueryStringParams;

            $model = new PostModel();

            $response = $model->getPostsCount();
            $pagesAmount = ceil(($response[0]['count'] ?? 1) / $rowCount);

            $output = [
                'pagesTotal' => $pagesAmount
            ];

            $responseData = json_encode($output);
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
            [$controllerUri] = $this->getUri();

            $responseData = json_encode($normalizedData[0]);
            $httpResponseHeader = $this->getStatusHeader201($controllerUri, $insertId);
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

        [$controllerUri, $hasInfoUri, $infoUri] = $this->getUri();
        
        if (!$hasInfoUri) {
            $this->sendStatusCode422();

            return;
        }

        try {
            $model = new PostModel();

            $tagController = new TagController();
            $categoryController = new CategoryController();

            $id = $infoUri;

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
                || !(count($tagIds) > 0)
            ) {
                $this->sendStatusCode422();

                return;
            }

            $isAuthor = $this->hasPost($id, $userId);

            if (!$isAuthor) {
                $this->sendStatusCode422();

                return;
            }

            $hasTags = $tagController->hasTags($tagIds);
            $hasCategory = $categoryController->hasCategory($categoryId);

            if (!$hasTags || !$hasCategory) {
                $this->sendStatusCode422();

                return;
            }

            $assocTagIds = [];

            foreach ($tagIds as $value) {
                $assocTagIds[] = ["tagId" => $value];
            }

            $model->updatePost(
                $title,
                $content,
                $categoryId,
                $assocTagIds,
                $id,
                $userId
            );

            $response = $model->getPost($id);

            $normalizedData = $this->restoreInitialData($response);

            $responseData = json_encode($normalizedData[0]);
            $httpResponseHeader = $this->getStatusHeader201($controllerUri, $id);
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

    public function deletePost()
    {
        $responseData = "";
        $httpResponseHeader = "";

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );

        if (!array_key_exists(4, $uri)) {
            $this->sendStatusCode422();

            return;
        }

        try {
            $model = new PostModel();

            $id = $uri[4];

            $inputData = file_get_contents('php://input');
            $decodedData = json_decode($inputData);

            $userId = $decodedData->userId ?? '';

            $isAuthor = $this->hasPost($id, $userId);

            if (!$isAuthor) {
                $this->sendStatusCode422();

                return;
            }

            $model->deletePost(
                $id,
                $userId
            );

            $responseData = "Post has been deleted";
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