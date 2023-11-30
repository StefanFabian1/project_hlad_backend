<?php
class UserController extends BaseController{

private UserModel $userModel;

    public function __construct(string $operation) {
       
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
        $this->userModel = new UserModel();
        $this->userModel->setEmail($jsonObject->email);
        $this->userModel->setPassword($jsonObject->password);
        try {
            $user = $this->userModel->checkLogin();
            if ($user != null && $user->getId() != null && $user->getNick() != null) {
                $sessionManager = new SessionManager();
                $sessionManager->set('userid', $user->getId());
               
                $this->sendSuccessResponse((object)['nickname' => $user->getNick()], 200);
            } else {
                $this->sendErrorResponse("Username or password incorrect", 401);
            }
        } catch (Exception $e) {
            $this->sendErrorResponse('Exception occured while logging user in', 500);
        }
    }

    private function logOut() : void {
        if (strtoupper($_SERVER["REQUEST_METHOD"] === "GET")) {
            $sessionManager = new SessionManager();
            var_dump($sessionManager->get('user'));
            if ($sessionManager->has('user')) {
                $sessionManager->kill();
                $this->sendSuccessResponse((object)[], 200);
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

        $this->userModel = new UserModel($jsonObject->nick, $jsonObject->email , password_hash($jsonObject->password, PASSWORD_DEFAULT));
        //TODO zatial takto, neskor statusy - active confirmed, active, inactive, ?last activity on account?

        //mozme vytiahnut list usernames, poslat vsetky a porovnavat to fastozne -live

        if (!$this->userModel->validate()) {
            $this->sendErrorResponse("Invalid user data", 400);
        }

        $nickExists = $this->userModel->nickExists();
        $mailExists = $this->userModel->emailExists();
        if ($nickExists && $mailExists) {
            $this->sendErrorResponse("Nickname and email already exists", 409);
        } else if (!$nickExists && $mailExists) {
            $this->sendErrorResponse("Email already exists", 409);
        } else if ($nickExists && !$mailExists) {
            $this->sendErrorResponse("Nickname already exists", 409);
        }
        try {
            $result = $this->userModel->saveUser();
        } catch (Exception $e) {
            $this->sendErrorResponse('Exception occured while saving user', 500);
        }

        if (is_int($result)) {
            $this->sendSuccessResponse((object)['user_id' => $result], 201);
        } else {
            $this->sendErrorResponse('Error saving user', 500);
        }

    }
}
?>