<?php

class UserController extends BaseController
{
    public function hasUser($id): bool
    {
        $model = new UserModel();

        $response = $model->getUser($id);

        return (count($response) > 0);
    }

    public function get()
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) === 'PUT') {
            $this->updateUser();

            return;
        }

        $this->sendStatusCode422();
    }

    public function updateUser()
    {
        $responseData = "";
        $httpResponseHeader = "";

        try {
            $model = new UserModel();

            $inputData = file_get_contents('php://input');
            $decodedData = json_decode($inputData);

            $id = $decodedData->id ?? '';
            $name = $decodedData->name ?? '';
            $picture = $decodedData->picture ?? '';

            if (
                !(strlen($id) > 0)
                || !(strlen($name) > 0)
                || !(strlen($picture) > 0)
            ) {
                $this->sendStatusCode422();

                return;
            }

            $model->updateUser($id, $name, $picture);
            $response = $model->getUser($id);
            [$controllerUri] = $this->getUri();

            $responseData = json_encode($response[0]);
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
}