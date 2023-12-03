<?php

require_once PROJECT_ROOT_PATH . "\\Model\\DomainModel.php";

enum Typ: string
{
    case HMOTNOST = 'H';
    case POCET = 'P';
    case OBJEM = 'O';
}

class MernaJednotka extends DomainModel implements JsonSerializable {

    private ?string $unit;
    private ?string $nazov;
    //private Typ $typ;

    private string $tableName = "merna_jednotka";

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): void
    {
        $this->unit = $unit;
    }

    public function getNazov(): ?string
    {
        return $this->nazov;
    }

    public function setNazov(?string $nazov): void
    {
        $this->nazov = $nazov;
    }

    /*
    public function getTyp(): Typ
    {
        return $this->typ;
    }

    public function setTyp(Typ $typ): void
    {
        $this->typ = $typ;
    }
    */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'unit' => $this->unit,
            'nazov' => $this->nazov,
            //'typ' => $this->typ->value, // Access the underlying value of the enum
        ];
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

}

?>