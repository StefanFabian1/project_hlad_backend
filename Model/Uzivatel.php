<?php

require_once PROJECT_ROOT_PATH . "\\Model\\DomainModel.php";

class Uzivatel extends DomainModel implements JsonSerializable {

    private string $nick;
    private string $email;
    private string $password;

    private string $tableName = "uzivatel";

    public function getNick(): string
    {
        return $this->nick;
    }

    public function setNick(string $nick): void
    {
        $this->nick = $nick;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'nick' => $this->nick,
        ];
    }
}

?>