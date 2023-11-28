<?php
class BaseController
{
    public function __call(string $name, array $arguments) : void
    {
        $this->sendEr404Output("BaseController");
    }

    protected function sendOutput(string $data, array $httpHeaders = array()) : void
    {
        header_remove('Set-Cookie');

        if (is_array($httpHeaders) && !empty($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }
        echo $data;
        exit;
    }

     protected function sendEr404Output(string $message) : void
     {
        $this->sendOutput($message, array(http_response_code(404)));
     }

    protected function sendEr40Output(string $message): void
    {
        $this->sendOutput($message, array(http_response_code(400)));
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
                    $this->sendEr404Output("BaseController-chooseHttpRequestMethod()");
            }
        } catch (Exception $e) {
            $this->sendOutput($e->getMessage(), array(http_response_code(404)));
        }
    }
}
