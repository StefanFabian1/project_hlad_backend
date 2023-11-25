<?php

require __DIR__ . "\\inc\\projectdef.php";

$uri = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if (isset($uri[2]) && file_exists(PROJECT_ROOT_PATH . "Controller\\Api\\" . ucfirst(strtolower($uri[2])) . "Controller.php")) {
    $controllerNameString = ucfirst(strtolower($uri[2])) . "Controller";
    require PROJECT_ROOT_PATH . "Controller\\Api\\" . $controllerNameString . ".php";
    new $controllerNameString();
} else {
    header("HTTP/1.1 404 Not Found", false, http_response_code(404));
    exit();
}
