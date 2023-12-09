<?php

require_once PROJECT_ROOT_PATH . "\\Model\\DomainModel.php";

class Recept extends DomainModel implements JsonSerializable {

    private string $name;
    private string $description;
    private bool $sukromny;
    private int $poc_zobrazeni;
    private int $poc_likes;
    private array $ingrediencie = [];
    private ?Obrazok $image;
    private ?Uzivatel $vlastnik;

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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function isSukromny(): bool
    {
        return $this->sukromny;
    }

    public function setSukromny(bool $sukromny): void
    {
        $this->sukromny = $sukromny;
    }

    public function getPocZobrazeni(): int
    {
        return $this->poc_zobrazeni;
    }

    public function setPocZobrazeni(int $poc_zobrazeni): void
    {
        $this->poc_zobrazeni = $poc_zobrazeni;
    }

    public function getPocLikes(): int
    {
        return $this->poc_likes;
    }

    public function setPocLikes(int $poc_likes): void
    {
        $this->poc_likes = $poc_likes;
    }

    public function getIngrediencie(): array
    {
        return $this->ingrediencie;
    }

    public function setIngrediencie(array $ingrediencie): void
    {
        $this->ingrediencie = $ingrediencie;
    }

    public function getImage(): ?Obrazok
    {
        return $this->image;
    }

    public function setImage(?Obrazok $image): void
    {
        $this->image = $image;
    }

    public function getVlastnik(): ?Uzivatel
    {
        return $this->vlastnik;
    }

    public function setVlastnik(?Uzivatel $vlastnik): void
    {
        $this->vlastnik = $vlastnik;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'sukromny' => $this->sukromny,
            'poc_zobrazeni' => $this->poc_zobrazeni,
            'poc_likes' => $this->poc_likes,
            'ingrediencie' => $this->ingrediencie,
            //'image' => $this->image,
            'vlastnik' => $this->vlastnik,
        ];
    }
}
?>