<?php
class BaseController
{

    protected SessionManager $sessionManager;
    protected ?int $userId;

    public function __construct()
    {
        $this->sessionManager = new SessionManager();
        if ($this->sessionManager->has('userid')) {
            $this->userId = (int) $this->sessionManager->get('userid') ?? null;
        } else {
            $this->userId = null;
        }
        $this->chooseHttpRequestMethod();
    }

    public function __call(string $name, array $arguments): void
    {
        $this->sendErrorResponse('Unidentified api method', 404);
    }

    protected function sendOutput(string $data, int $statusCode): void
    {
        header('Access-Control-Allow-Credentials: true');
        if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
            $http_origin = $_SERVER['HTTP_ORIGIN'];
            if ($http_origin == " http://localhost:8080" || $http_origin == "http://localhost:5173") {
                header("Access-Control-Allow-Origin: $http_origin");
            }
        }
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo $data;
        exit;
    }

    protected function sendSuccessResponse(array $data, int $statusCode): void
    {
        $response = ['status' => 'success', 'data' => $data];
        $this->sendOutput(json_encode($response), $statusCode);
    }

    protected function sendErrorResponse(string $message, int $statusCode): void
    {
        $response = ['status' => 'error', 'message' => $message];
        $this->sendOutput(json_encode($response), $statusCode);
    }

    protected function sendNoContentResponse(): void
    {
        $message = 'No content';
        $response = ['status' => 'success', 'message' => $message];
        $this->sendOutput(json_encode($response), 204);
    }

    protected function getJsonAsObjects(): object
    {
        $json = file_get_contents('php://input');
        return json_decode($json);
    }

    protected function getJsonAsArray(): array
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    public function chooseHttpRequestMethod(): void
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
