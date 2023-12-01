<?php

require_once PROJECT_ROOT_PATH . "\\Model\\DomainModel.php";

class Recept extends DomainModel implements JsonSerializable{

    private string $name;
    private string $description;
    private bool $sukromny;
    private int $poc_zobrazeni;
    private int $poc_likes;
    private array $ingrediencie = array();
    private string $imageUrl;

    private string $tableName = "recept";

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getSukromny(): bool
    {
        return $this->sukromny;
    }

    public function setSukromny(bool $sukromny): void
    {
        $this->sukromny = $sukromny;
    }

    public function getPoc_zobrazeni(): int
    {
        return $this->poc_zobrazeni;
    }

    public function setPoc_zobrazeni(int $poc_zobrazeni): void
    {
        $this->poc_zobrazeni = $poc_zobrazeni;
    }

    public function getPoc_likes(): int
    {
        return $this->poc_likes;
    }

    public function setPoc_likes(int $poc_likes): void
    {
        $this->poc_likes = $poc_likes;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function setIngrediencie(array $ingrediencie): void
    {
        $this->ingrediencie = $ingrediencie;
    }

    public function getIngrediencie(): array
    {
        return $this->ingrediencie;
    }

    public function setImageUrl(int $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function jsonSerialize() : array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'imageUrl' => $this->imageUrl,
            'description' => $this->description,
            'sukromny' => $this->sukromny,
            'poc_zobrazeni' => $this->poc_zobrazeni,
            'poc_likes' => $this->poc_likes,
            'ingrediencie' => $this->ingrediencie,
        ];
    }
}
?>