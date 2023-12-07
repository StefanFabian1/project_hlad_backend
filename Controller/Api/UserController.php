<?php
class UserController extends BaseController{

    private UserDAO $userDAO;

    public function __construct(string $operation) {

        
        $this->sessionManager = new SessionManager();
        $this->userDAO = new UserDAO();
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
            case 'isLoggedIn':
                $this->isLoggedIn();
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
        $user = new Uzivatel();
        $user->setEmail($jsonObject->email);
        $user->setPassword($jsonObject->password);
        try {
            $userDB = $this->userDAO->checkLogin($user);
            if ($userDB != null && $userDB->getId() != null && $userDB->getNick() != null) {
                $this->sessionManager->set('userid', $userDB->getId());
                $this->sendSuccessResponse(array((object)['nickname' => $userDB->getNick()]), 200);
            } else {
                $this->sendErrorResponse("Username or password incorrect", 401);
            }
        } catch (Exception $e) {
            $this->sendErrorResponse('Exception occured while logging user in:' . $e->getMessage(), 500);
        }
    }

    private function logOut() : void {
        if (strtoupper($_SERVER["REQUEST_METHOD"] === "GET")) {
            if ($this->sessionManager->has('userid')) {
                $this->sessionManager->kill();
                $this->sendSuccessResponse(array((object)[]), 200);
            } else {
                $this->sessionManager->kill();
                $this->sendErrorResponse('User not logged in', 404);
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

        $user = new Uzivatel();
        $user->setNick($jsonObject->nick);
        $user->setEmail($jsonObject->email);
        $user->setPassword(password_hash($jsonObject->password, PASSWORD_DEFAULT));
        //TODO zatial takto, neskor statusy - active confirmed, active, inactive, ?last activity on account?

        //mozme vytiahnut list usernames, poslat vsetky a porovnavat to fastozne -live
        if (!$this->userDAO->validate($user)) {
            $this->sendErrorResponse("Invalid user data", 400);
        }

        $nickExists = $this->userDAO->nickExists($user->getNick());
        $mailExists = $this->userDAO->emailExists($user->getEmail());
        if ($nickExists && $mailExists) {
            $this->sendErrorResponse("Nickname and email already exists", 409);
        } else if (!$nickExists && $mailExists) {
            $this->sendErrorResponse("Email already exists", 409);
        } else if ($nickExists && !$mailExists) {
            $this->sendErrorResponse("Nickname already exists", 409);
        }
        try {
            $result = $this->userDAO->saveUser($user);
        } catch (Exception $e) {
            $this->sendErrorResponse('Exception occured while saving user:' . $e->getMessage(), 500);
        }

        if (is_int($result)) {
            $this->sendSuccessResponse(array(['user_id' => $result]), 201);
        } else {
            $this->sendErrorResponse('Error saving user', 500);
        }
    }

    private function isLoggedIn(): void
    {
        if ($this->sessionManager->has('userid')) {
            $nickName = $this->userDAO->getNick((int) $this->sessionManager->get('userid'));
            $this->sendSuccessResponse(array(['nickname' => $nickName]), 200);
        } else {
            $this->sendSuccessResponse(array(['nickname' => null]), 200);
        }
    }
}
?>