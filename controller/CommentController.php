<?php

//TODO: requires changes
class CommentController extends BaseController
{
    public function hasComment($id, $userId): bool
    {
        $model = new CommentModel();

        $response = $model->getComment($id, $userId);

        return (count($response) > 0);
    }

    public function get()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );

        if (strtoupper($requestMethod) === 'GET') {
            $this->getCommentsList();

            return;
        }

        if (strtoupper($requestMethod) === 'POST') {
            $this->createComment();

            return;
        }

        if (strtoupper($requestMethod) === 'PATCH') {
            $this->updateComment();

            return;
        }

        if (strtoupper($requestMethod) === 'DELETE') {
            $this->deleteComment();

            return;
        }

        $this->sendStatusCode422();
    }

    public function getCommentsList()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );

        if (!array_key_exists(4, $uri)) {
            $this->sendStatusCode422();

            return;
        }

        try {
            $model = new CommentModel();

            $id = $uri[4];

            $response = $model->getCommentsList($id);

            $responseData = json_encode($response[0]);
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

    public function createComment()
    {
        $responseData = "";
        $httpResponseHeader = "";

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );

        try {
            $model = new CommentModel();

            $userController = new UserController();
            $postController = new PostController();

            $inputData = file_get_contents('php://input');
            $decodedData = json_decode($inputData);

            $userId = $decodedData->userId ?? '';
            $content = $decodedData->content ?? '';
            $postId = $decodedData->postId ?? '';
            $parentId = $decodedData->parentId ?? 0;

            if (
                !(strlen($userId) > 0)
                || !(strlen($content) > 0)
                || !(strlen($postId) > 0)
            ) {
                $this->sendStatusCode422();

                return;
            }

            $hasUser = $userController->hasUser($userId);
            $hasPost = $postController->hasPost($postId);

            if (!$hasUser || !$hasPost) {
                $this->sendOutput(
                    json_encode(self::RESPONSE_DATA_DECODED_422),
                    self::HEADERS_422
                );

                return;
            }

            $response = $model->createComment($userId, $content, $postId, $parentId);
            $insertId = $response['insert_id'];

            $output = $model->getComment($insertId);

            $responseData = json_encode($output[0]);
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

    public function updateComment()
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
            $model = new CommentModel();

            $id = $uri[4];

            $inputData = file_get_contents('php://input');
            $decodedData = json_decode($inputData);

            $postId = $decodedData->postId ?? '';
            $userId = $decodedData->userId ?? '';
            $content = $decodedData->content ?? '';
            $likedByUserId = $decodedData->likedByUserId ?? '';
            $dislikedByUserId = $decodedData->dislikedByUserId ?? '';

            if (
                !(strlen($id) > 0)
                || (
                    !(strlen($content) > 0) &&
                    !(strlen($likedByUserId) > 0) &&
                    !(strlen($dislikedByUserId) > 0)
                )
            ) {
                $this->sendStatusCode422();

                return;
            }

            if (strlen($content) > 0 && strlen($userId) > 0) {
                $isAuthor = $this->hasComment($id, $userId);

                if (!$isAuthor) {
                    $this->sendStatusCode422();

                    return;
                }

                $model->updateCommentContent($content, $userId, $id);

                $output = $model->getComment($id);

                $responseData = json_encode($output[0]);
                $httpResponseHeader = $this->getStatusHeader201($uri[3], $id);

                return;
            }

            if (strlen($likedByUserId) > 0) {
                $response = $model->getComment($id);

                $normalizedData = $this->restoreInitialData($response);
                $likedByList = $normalizedData[0]['likedBy'];

                if (in_array($likedByUserId, $likedByList)) {
                    $this->sendStatusCode422();

                    return;
                }

                $output = $model->updateCommentsLikedBy($likedByUserId, $postId);

                $outputData = $this->restoreInitialData($output);

                $responseData = json_encode($outputData[0]);
                $httpResponseHeader = $this->getStatusHeader201($uri[3], $id);

                return;
            }

            if (strlen($dislikedByUserId) > 0) {
                $response = $model->getComment($id);

                $normalizedData = $this->restoreInitialData($response);
                $dislikedByList = $normalizedData[0]['dislikedBy'];

                if (in_array($dislikedByUserId, $dislikedByList)) {
                    $this->sendStatusCode422();

                    return;
                }

                $output = $model->updateCommentsLikedBy($dislikedByUserId, $postId);

                $outputData = $this->restoreInitialData($output);

                $responseData = json_encode($outputData[0]);
                $httpResponseHeader = $this->getStatusHeader201($uri[3], $id);

                return;
            }



            $this->sendStatusCode422();
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