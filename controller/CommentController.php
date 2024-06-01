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
            if (array_key_exists(4, $uri)) {
                if ($uri[4] === 'count') {
                    $this->getCommentsCount();

                    return;
                }
            }
            
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

    public function getCommentsCount() {
        $arrQueryStringParams = $this->getQueryStringParams();

        if (!isset($arrQueryStringParams['postId'])) {
            $this->sendStatusCode422('No post id');

            return;
        }

        ['postId' => $postId] = $arrQueryStringParams;
        
        try {
            $model = new CommentModel();

            $response = $model->getCommentsCount($postId);

            $output = [
                'commentsTotal' => $response[0]['count']
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
    
    public function getCommentsList()
    {
        $arrQueryStringParams = $this->getQueryStringParams();

        if (!isset($arrQueryStringParams['postId'])) {
            $this->sendStatusCode422('No post id');

            return;
        }

        ['postId' => $postId] = $arrQueryStringParams;

        try {
            $model = new CommentModel();

            $response = $model->getCommentsList($postId);

            $normalizedData = $this->restoreInitialData($response);

            foreach ($normalizedData as &$value) {
                $value['user']['id'] = $value['userId'];
                $value['user']['name'] = $value['userName'];
                $value['user']['picture'] = $value['userPicture'];

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

                $normalizedData = $normalizedData[0];
                $likedByList = $normalizedData['likedBy'];
                $dislikedByList = $normalizedData['dislikedBy'];

                if (in_array($likedByUserId, $likedByList)) {
                    $this->sendStatusCode422();

                    return;
                }

                $isCurrentlyCommentDisliked = in_array($likedByUserId, $dislikedByList);

                $newLikedByList = [...$likedByList, $likedByUserId];
                $newDislikedByList = $isCurrentlyCommentDisliked
                    ? array_filter($dislikedByList, function ($value) use ($likedByUserId) {
                        return $value !== $likedByUserId;
                    })
                    : [];

                $assocLikedByList = [];
                $assocDislikedByList = [];

                foreach ($newLikedByList as $value) {
                    $assocLikedByList[] = ["likedBy" => $value];
                }

                foreach ($newDislikedByList as $value) {
                    $assocDislikedByList[] = ["dislikedBy" => $value];
                }

                if (count($assocDislikedByList) < 1) {
                    $assocDislikedByList = '[]';
                }

                $model->updateCommentLikesList($assocLikedByList, $assocDislikedByList, $id);

                $output = $model->getComment($id);

                $outputData = $this->restoreInitialData($output);

                $responseData = json_encode($outputData[0]);
                $httpResponseHeader = $this->getStatusHeader201($uri[3], $id);

                return;
            }

            if (strlen($dislikedByUserId) > 0) {
                $response = $model->getComment($id);

                $normalizedData = $this->restoreInitialData($response);

                $normalizedData = $normalizedData[0];
                $likedByList = $normalizedData['likedBy'];
                $dislikedByList = $normalizedData['dislikedBy'];

                if (in_array($dislikedByUserId, $dislikedByList)) {
                    $this->sendStatusCode422();

                    return;
                }

                $isCurrentlyCommentLiked = in_array($dislikedByUserId, $likedByList);

                $newDislikedByList = [...$dislikedByList, $dislikedByUserId];
                $newLikedByList = $isCurrentlyCommentLiked
                    ? array_filter($likedByList, function ($value) use ($dislikedByUserId) {
                        return $value !== $dislikedByUserId;
                    })
                    : [];

                $assocLikedByList = [];
                $assocDislikedByList = [];

                foreach ($newLikedByList as $value) {
                    $assocLikedByList[] = ["likedBy" => $value];
                }

                foreach ($newDislikedByList as $value) {
                    $assocDislikedByList[] = ["dislikedBy" => $value];
                }

                if (count($assocLikedByList) < 1) {
                    $assocLikedByList = '[]';
                }

                $model->updateCommentLikesList($assocLikedByList, $assocDislikedByList, $id);

                $output = $model->getComment($id);

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

    public function deleteComment()
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

            $userId = $decodedData->userId ?? '';

            if (!(strlen($id) > 0)) {
                $this->sendStatusCode422();

                return;
            }

            $isAuthor = $this->hasComment($id, $userId);

            if (!$isAuthor) {
                $this->sendStatusCode422();

                return;
            }

            $model->deleteComment($id, $userId);

            $responseData = "Comment has been deleted";
            $httpResponseHeader = $this->getStatusHeader201($uri[3], $id);
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