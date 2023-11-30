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

    private function tryLogIn() {
        if (!strtoupper($_SERVER["REQUEST_METHOD"] === "POST")) {
            $this->sendErrorResponse('Unsupported request method', 404);
        }
        $jsonObject = $this->getJsonAsObjects();

        $this->userModel = new UserModel(null, $jsonObject->email, $jsonObject->password);

        try {
            $nickName = $this->userModel->checkLogin();
            if ($nickName != null) {
                $sessionManager = new SessionManager();
                $sessionManager->set('user', $nickName);
                $this->sendSuccessResponse((object)['nickname' => $nickName], 200);
            } else {
                $this->sendErrorResponse("Username or password incorrect", 401);
            }
        } catch (Exception $e) {
            $this->sendErrorResponse('Exception occured while logging user in', 500);
        }
    }

    private function logOut() {
        if (strtoupper($_SERVER["REQUEST_METHOD"] === "GET")) {

        } else {
            $this->sendErrorResponse('Error logout user', 404);
        }
    }

    private function register() {
        if (!strtoupper($_SERVER["REQUEST_METHOD"] === "PUT")) {
            $this->sendErrorResponse('Unsupported request method', 404);
        }
        $jsonObject = $this->getJsonAsObjects();

        $this->userModel = new UserModel($jsonObject->nick, $jsonObject->email , password_hash($jsonObject->password, PASSWORD_DEFAULT));
        //TODO zatial takto, neskor statusy - active confirmed, active, inactive, ?last activity?

        //mozme vytiahnut list usernames a porovnavat to fastozne - bez odoslania

        if (!$this->userModel->validate()) {
            $this->sendErrorResponse("Invalid user data", 400);
        }

        if ($this->userModel->nickExists()) {
            $this->sendErrorResponse("Username already exists", 409);
        }

        if ($this->userModel->emailExists()) {
            $this->sendErrorResponse("Email already exists", 409);
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