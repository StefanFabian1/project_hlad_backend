<?php
require_once PROJECT_ROOT_PATH . "\\Model\\Database\\Database.php";

class UserModel extends Database implements ModelInterface
{

    private string $nick;
    private string $email;
    private string $password;

    private string $tableName = "uzivatel";

    public function __construct(?string $nick, string $email, string $password)
    {
        parent::__construct();
        if ($nick != null) {
            $this->nick = $nick;
        }
        $this->email = $email;
        $this->password = $password;
    }

    public function saveUser()
    {
        return $this->save($this);
    }

    public function checkLogin()
    {
        $existsLogin = !empty($this->select($this->getTableName(), array('id'), array(new WhereClause('AND', 'email', $this->email, false))));
        if ($existsLogin) {
            $storedHashedPassword = $this->select($this->getTableName(), array('password'), array(new WhereClause('AND', 'email', $this->email, false)));
            if (password_verify($this->password, $storedHashedPassword[0]['password'])) {
                var_dump("Sucess");
            } else {
                var_dump("Failed");
            }
        }
    }

    public function nickExists() : bool {
        return !empty($this->select($this->getTableName(), array('id'), array(new WhereClause('AND', 'nick', $this->nick, false))));
    }

    public function emailExists() : bool {
        return !empty($this->select($this->getTableName(), array('id'), array(new WhereClause('AND', 'email', $this->email, false))));
    }

    public function getNick() : string{
        return $this->nick;
    }

    public function getEmail() : string {
        return $this->email;
    }

    public function getTableName() : string
    {
        return $this->tableName;
    }

    public function validate() : bool {
        return !StringUtils::isEmpty($this->nick) && !StringUtils::isEmpty($this->email) && !StringUtils::isEmpty($this->password);
    }
}
?>