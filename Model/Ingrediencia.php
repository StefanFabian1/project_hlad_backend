<?php

require_once PROJECT_ROOT_PATH . "\\Model\\DomainModel.php";

class Ingrediencia extends DomainModel implements JsonSerializable {

    private ?string $name;

    private string $tableName = "ingrediencia";

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }
}

?>