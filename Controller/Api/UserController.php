<?php
class UserController extends BaseController{

private UserModel $userModel;

    public function __construct(string $operation) {
       
        if (!strtoupper($_SERVER["REQUEST_METHOD"] === "POST")) {
            $this->sendErrorResponse('Unsupported request method', 404);
        }
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
        $jsonObject = $this->getJsonAsObjects();

        $this->userModel = new UserModel(null, $jsonObject->email, $jsonObject->password);

        try {
            $result = $this->userModel->checkLogin();
        } catch (Exception $e) {
            $this->sendErrorResponse('Username or password incorrect', 500);
        }
    }

    private function logOut() {
        if (strtoupper($_SERVER["REQUEST_METHOD"] === "GET")) {

        } else {
            $this->sendErrorResponse('Error logout user', 404);
        }
    }

    private function register() {

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