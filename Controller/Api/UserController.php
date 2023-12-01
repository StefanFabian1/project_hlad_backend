<?php
class UserController extends BaseController{

    public function __construct(string $operation) {

        $this->sessionManager = new SessionManager();
        
        switch($operation) {
            case 'login':
                $this->tryLogIn();
                break;
            case 'register':
                $this->register();
                break;
            case 'logout':
                $this->logOut();
                break;
            default:
                $this->sendErrorResponse('Method not found', 404);
        }
    }

    private function tryLogIn() : void {
        if (!strtoupper($_SERVER["REQUEST_METHOD"] === "POST")) {
            $this->sendErrorResponse('Unsupported request method', 404);
        } 
        $jsonObject = $this->getJsonAsObjects();
        $userModel = new UserModel();
        $userModel->setEmail($jsonObject->email);
        $userModel->setPassword($jsonObject->password);
        try {
            $user = $userModel->checkLogin();
            if ($user != null && $user->getId() != null && $user->getNick() != null) {
                $this->sessionManager->set('userid', $user->getId());
               
                $this->sendSuccessResponse(array((object)['nickname' => $user->getNick()]), 200);
            } else {
                $this->sendErrorResponse("Username or password incorrect", 401);
            }
        } catch (Exception $e) {
            $this->sendErrorResponse('Exception occured while logging user in:' . $e->getMessage(), 500);
        }
    }

    private function logOut() : void {
        if (strtoupper($_SERVER["REQUEST_METHOD"] === "GET")) {
            var_dump($this->sessionManager->get('userid'));
            if ($this->sessionManager->has('userid')) {
                $this->sessionManager->kill();
                $this->sendSuccessResponse(array((object)[]), 200);
            } else {
                $this->sendErrorResponse('Error logout user', 404);
            } 
        } else {
            $this->sendErrorResponse('Unsupported request method', 404);
        }
    }

    private function register() : void {
        if (!strtoupper($_SERVER["REQUEST_METHOD"] === "PUT")) {
            $this->sendErrorResponse('Unsupported request method', 404);
        }
        $jsonObject = $this->getJsonAsObjects();

        $userModel = new UserModel();
        $userModel->setNick($jsonObject->nick);
        $userModel->setEmail($jsonObject->email);
        $userModel->setPassword(password_hash($jsonObject->password, PASSWORD_DEFAULT));
        //TODO zatial takto, neskor statusy - active confirmed, active, inactive, ?last activity on account?

        //mozme vytiahnut list usernames, poslat vsetky a porovnavat to fastozne -live
        if (!$userModel->validate()) {
            $this->sendErrorResponse("Invalid user data", 400);
        }

        $nickExists = $userModel->nickExists();
        $mailExists = $userModel->emailExists();
        if ($nickExists && $mailExists) {
            $this->sendErrorResponse("Nickname and email already exists", 409);
        } else if (!$nickExists && $mailExists) {
            $this->sendErrorResponse("Email already exists", 409);
        } else if ($nickExists && !$mailExists) {
            $this->sendErrorResponse("Nickname already exists", 409);
        }
        try {
            $result = $userModel->saveUser();
        } catch (Exception $e) {
            $this->sendErrorResponse('Exception occured while saving user:' . $e->getMessage(), 500);
        }

        if (is_int($result)) {
            $this->sendSuccessResponse(array((object)['user_id' => $result]), 201);
        } else {
            $this->sendErrorResponse('Error saving user', 500);
        }

    }
}
?>