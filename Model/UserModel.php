<?php
require_once PROJECT_ROOT_PATH . "\\Model\\Database\\Database.php";

class UserModel extends Database implements ModelInterface
{

    private int $id;
    private string $nick;
    private string $email;
    private string $password;

    private string $tableName = "uzivatel";

    public function __construct()
    {
        parent::__construct();
    }

    public function saveUser()
    {
        return $this->save($this);
    }

    public function checkLogin() : ?UserModel
    {
        $data = $this->select($this->getTableName(), array('password, nick, id'), array(new WhereClause('AND', 'email', $this->email, false)));
        if (!empty($data)) {
            if (password_verify($this->password, $data[0]['password'])) {
                $result = new UserModel();
                $result->setNick($data[0]['nick']);
                $result->setId($data[0]['id']);
                return $result;
            }
        }
        return null;
    }

    public function nickExists() : bool {
        return !empty($this->select($this->getTableName(), array('id'), array(new WhereClause('AND', 'nick', $this->nick, false))));
    }

    public function emailExists() : bool {
        return !empty($this->select($this->getTableName(), array('id'), array(new WhereClause('AND', 'email', $this->email, false))));
    }

    public function getNick() : ?string{
        return $this->nick;
    }

    public function setNick(string $nick): void
    {
        $this->nick = $nick;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEmail() : string {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
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