<?php

require_once PROJECT_ROOT_PATH . "\\Model\\DomainModel.php";

class Obrazok extends DomainModel{

    private ?string $name = null;
    private string $original_name;
    private ?string $path = null;
    private ?int $recept_id = null;
    private ?int $uzivatel_id = null;

    private string $tableName = "image";

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getOriginal_name(): string
    {
        return $this->original_name;
    }

    public function setOriginal_name(string $original_name): void
    {
        $this->original_name = $original_name;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function getReceptId(): ?int
    {
        return $this->recept_id;
    }

    public function setReceptId(?int $recept_id): void
    {
        $this->recept_id = $recept_id;
    }

    public function getUzivatelId(): ?int
    {
        return $this->uzivatel_id;
    }

    public function setUzivatelId(?int $uzivatel_id): void
    {
        $this->uzivatel_id = $uzivatel_id;
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