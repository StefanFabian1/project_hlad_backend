<?php

require_once PROJECT_ROOT_PATH . "\\Model\\DomainModel.php";

class Ingrediencia extends DomainModel implements JsonSerializable {

    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}

?>