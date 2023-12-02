<?php

require_once PROJECT_ROOT_PATH . "\\Model\\DomainModel.php";

class Obrazok extends DomainModel{

    private string $name;
    private string $original_name;
    private string $path;

    private string $tableName = "recept";

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOriginalName(): string
    {
        return $this->original_name;
    }

    public function setOriginalName(string $original_name): void
    {
        $this->original_name = $original_name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'original_name' => $this->original_name,
            'path' => $this->path,
        ];
    }
}

?>