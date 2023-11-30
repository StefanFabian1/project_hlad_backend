<?php

require __DIR__ . "\\inc\\projectdef.php";

$sessionManager = new SessionManager();
$uri = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

//TODO v headeri budem dostavat jwt token, bude treba dekodovat a pustit alebo nepustit ku api

if (isset($uri[2]) && (strcasecmp($uri[2], 'login') == 0 || strcasecmp($uri[2], 'logout') == 0 || strcasecmp($uri[2], 'register') == 0 )) {
    //handling login, logout a register requestu
    require PROJECT_ROOT_PATH . "Controller\\Api\\UserController.php";
    require PROJECT_ROOT_PATH . "Model\\UserModel.php";
    new UserController($uri[2]);
} else if (isset($uri[2]) && file_exists(PROJECT_ROOT_PATH . "Controller\\Api\\" . ucfirst(strtolower($uri[2])) . "Controller.php")) {
    //handling controller requestov
    $controllerNameString = ucfirst(strtolower($uri[2])) . "Controller";
    $modelNameString = ucfirst(strtolower($uri[2])) . "Model";
    require PROJECT_ROOT_PATH . "Controller\\Api\\" . $controllerNameString . ".php";
    require PROJECT_ROOT_PATH . "Model\\" . $modelNameString . ".php";
    new $controllerNameString();
} else {
    header_remove('Set-Cookie');
        header('Content-Type: application/json');
        http_response_code(404);
        $response = ['status' => 'error', 'message' => 'Page Not Found'];
        echo json_encode($response);
        exit;
}
