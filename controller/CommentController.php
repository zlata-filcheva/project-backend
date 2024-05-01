<?php

class CommentController extends BaseController
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

            $model = new CommentModel();

            [
                'rowCount' => $rowCount,
                'offset' => $offset
            ] = $arrQueryStringParams;

            $response = $model->getComments($rowCount, $offset);

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
            $_POST['userId'],
            $_POST['content'],
            $_POST['postId']
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
            $model = new CommentModel();

            $userController = new UserController();
            $postController = new PostController();

            $userId = $_POST['userId'];
            $content = $_POST['content'];
            $postId = $_POST['postId'];

            $hasUser = $userController->hasUser($userId);

            if (!$hasUser) {
                $this->sendOutput(
                    json_encode(self::RESPONSE_DATA_DECODED_422),
                    self::HEADERS_422
                );

                return;
            }

            $hasPost = $postController->hasPost($postId);

            if (!$hasPost) {
                $this->sendOutput(
                    json_encode(self::RESPONSE_DATA_DECODED_422),
                    self::HEADERS_422
                );

                return;
            }

            $response = $model->createComment($userId, $content, $postId);

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

    /*
     * Very big logic
     */
    /*
    public function update()
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

        $obligatoryPostVariables = [
            $_POST['userId'],
            $_POST['postId']
        ];

        $expectedPostVariables = [
            $_POST['content'],
            $_POST['likes'],
            $_POST['dislikes']
        ];

        foreach ($obligatoryPostVariables as $value) {
            if (!isset($value)) {
                $this->sendOutput(
                    json_encode(self::RESPONSE_DATA_DECODED_422),
                    self::HEADERS_422
                );

                return;
            }
        }

        $hasExpectedPostVariable = false;

        foreach ($expectedPostVariables as $value) {
            if (isset($value)) {
                $hasExpectedPostVariable = true;

                break;
            }
        }

        if (!isset($hasExpectedPostVariable)) {
            $this->sendOutput(
                json_encode(self::RESPONSE_DATA_DECODED_422),
                self::HEADERS_422
            );

            return;
        }

        try {
            $model = new CommentsModel();

            $response = $model->createComment($userId, $content, $postId);

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
    */
}