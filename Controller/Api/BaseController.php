<?php
class BaseController
{
    public function __call($name, $arguments)
    {
        $this->sendOutput('', array(http_response_code(404)));
    }

    protected function sendOutput($data, $httpHeaders = array())
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

    protected function getQueryStringParams()
    {
        return parse_str($_SERVER['QUERY_STRING'], $query);
    }

    public function chooseHttpRequestMethod()
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
                    $this->sendOutput('', array(http_response_code(404)));
            }
        } catch (Exception $e) {
            $this->sendOutput($e->getMessage(), array(http_response_code(404)));
        }
    }
}
