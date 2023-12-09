<?php
require __DIR__ . "\\inc\\projectdef.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Credentials: true');

    $http_origin = $_SERVER['HTTP_ORIGIN'];
    if ($http_origin == " http://localhost:8080" || $http_origin == "http://localhost:5173") {
        header("Access-Control-Allow-Origin: $http_origin");
    }
    header("Access-Control-Allow-Methods: POST, PUT, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    http_response_code(204);
    exit();
}
$sessionManager = new SessionManager();
$uri = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

//TODO v headeri budem dostavat jwt token, bude treba dekodovat a pustit alebo nepustit ku api

if (isset($uri[2]) && (strcasecmp($uri[2], 'login') == 0 || strcasecmp($uri[2], 'logout') == 0 || strcasecmp($uri[2], 'register') == 0 || strcasecmp($uri[2], 'isLoggedIn') == 0)) {
    //handling login, logout a register requestu
    require PROJECT_ROOT_PATH . "Controller\\Api\\UserController.php";
    require PROJECT_ROOT_PATH . "Model\\DAO\\UserDAO.php";
    new UserController($uri[2]);
} else if (isset($uri[2]) && file_exists(PROJECT_ROOT_PATH . "Controller\\Api\\" . ucfirst(strtolower($uri[2])) . "Controller.php")) {
    //handling controller requestov
    $controllerNameString = ucfirst(strtolower($uri[2])) . "Controller";
    $daoNameString = ucfirst(strtolower($uri[2])) . "DAO";
    require PROJECT_ROOT_PATH . "Controller\\Api\\" . $controllerNameString . ".php";
    require PROJECT_ROOT_PATH . "Model\\DAO\\" . $daoNameString . ".php";
    new $controllerNameString();
} else {
    header_remove('Set-Cookie');
    header('Access-Control-Allow-Credentials: true');
    if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
        $http_origin = $_SERVER['HTTP_ORIGIN'];
        if ($http_origin == " http://localhost:8080" || $http_origin == "http://localhost:5173") {
            header("Access-Control-Allow-Origin: $http_origin");
        }
    }
    header('Content-Type: application/json');
    http_response_code(404);
    $response = ['status' => 'error', 'message' => 'Page Not Found'];
    echo json_encode($response);
    exit;
}
