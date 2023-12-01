<?php
class BaseController
{

    protected SessionManager $sessionManager;

    public function __construct() {
       
        $this->sessionManager = new SessionManager();
        $this->chooseHttpRequestMethod();
    }

    public function __call(string $name, array $arguments) : void
    {
        $this->sendErrorResponse('Unidentified api method', 404);
    }

    protected function sendOutput(string $data, int $statusCode) : void
    {
        header_remove('Set-Cookie');
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo $data;
        exit;
    }

    protected function sendSuccessResponse(array $objects, int $statusCode) : void
    {
        $response = ['status' => 'success', 'data' => $objects];
        $this->sendOutput(json_encode($response), $statusCode);
    }

    protected function sendErrorResponse(string $message, int $statusCode): void
    {
        $response = ['status' => 'error', 'message' => $message];
        $this->sendOutput(json_encode($response), $statusCode);
    }

    protected function sendNoContentResponse(): void
    {
        $response = ['status' => 'success', 'message' => 'No content'];
        $this->sendOutput(json_encode($response), 204);
    }

    protected function getJsonAsObjects() : object
    {  
        $json = file_get_contents('php://input');
        return json_decode($json);
    }

    public function chooseHttpRequestMethod() : void
    {
       $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        try {
            switch ($requestMethod) {
                case 'GET':
                    $this->doGet();
                    break;
                case 'POST':
                    $this->doPost();
                    break;
                case 'PUT':
                    $this->doPut();
                    break;
                case 'PATCH':
                    $this->doPatch();
                    break;
                case 'DELETE':
                    $this->doDelete();
                    break;
                default:
                    $this->sendErrorResponse('Unidentified request method', 404);
            }
        } catch (Exception $e) {
            $this->sendErrorResponse('Unidentified request method', 404);
        }
    }
}
