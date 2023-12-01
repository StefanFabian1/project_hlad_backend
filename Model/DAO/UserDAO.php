<?php
require_once PROJECT_ROOT_PATH . "\\Model\\Database\\Database.php";
require_once PROJECT_ROOT_PATH . "\\Model\\Uzivatel.php";

class UserDAO extends Database {

    public function __construct()
    {
        parent::__construct();
    }

    public function saveUser(Uzivatel $user)
    {
        return $this->save($user);
    }

    public function checkLogin(Uzivatel $user) : ?Uzivatel
    {
        $query = "SELECT password, nick, id FROM uzivatel WHERE email LIKE ?";
        $values = array($user->getEmail());
        $data = $this->select($query, $values);
        if (!empty($data)) {
            if (password_verify($user->getPassword(), $data[0]['password'])) {
                $result = new Uzivatel();
                $result->setNick($data[0]['nick']);
                $result->setId($data[0]['id']);
                return $result;
            }
        }
        return null;
    }

    public function nickExists(string $nick) : bool {
        $query = "SELECT id FROM uzivatel WHERE nick LIKE ?";
        $values = array($nick);
        return !empty($this->select($query, $values));
    }

    public function emailExists(string $email) : bool {
        $query = "SELECT id FROM uzivatel WHERE email LIKE ?";
        $values = array($email);
        return !empty($this->select($query, $values));
    }

    public function validate(Uzivatel $user) : bool {
        return !StringUtils::isEmpty($user->getNick()) && !StringUtils::isEmpty($user->getEmail()) && !StringUtils::isEmpty($user->getPassword());
    }
}
?>