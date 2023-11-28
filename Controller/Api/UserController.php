<?php
class UserController extends BaseController{

    private UserModel $userModel;

    public function __construct(string $operation) {
       
        if (!strtoupper($_SERVER["REQUEST_METHOD"] === "POST")) {
            $this->sendEr404Output("UserController");
        }
        $this->userModel = new UserModel();
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
                $this->sendEr404Output("UserController");
        }
    }

    private function tryLogIn() {
        
    }

    private function logOut() {
        if (strtoupper($_SERVER["REQUEST_METHOD"] === "GET")) {

        } else {
            $this->sendEr404Output("UserController-logOut()");
        }
    }

    private function register() {
        
        $values =  $this->getJsonAsObjects();
       
        //TODO zatial takto, neskor statusy - active confirmed, active, inactive, ?last activity?
        //name
        //email
        //password
        if (StringUtils::isEmpty($values->name) || StringUtils::isEmpty($values->email) || StringUtils::isEmpty($values->password)) {
            $this->sendEr400Output("UserController-register()");
        }
        //validate mail?
        //call userModel
        //check if name is in the db
        //check if mail in db
        //hash pass

        echo $values->name;
    }
}
?>