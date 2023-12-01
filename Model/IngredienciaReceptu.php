<?php

require_once PROJECT_ROOT_PATH . "\\Model\\DomainModel.php";

class IngredienciaReceptu extends DomainModel implements JsonSerializable {

    private Ingrediencia $ingrediencia;
    private MernaJednotka $mernaJednotka;
    private float $mnozstvo;

    public function setIngrediencia(Ingrediencia $ingrediencia): void
    {
        $this->ingrediencia = $ingrediencia;
    }

    public function getMernaJednotka(): MernaJednotka
    {
        return $this->mernaJednotka;
    }

    public function setMernaJednotka(MernaJednotka $mernaJednotka): void
    {
        $this->mernaJednotka = $mernaJednotka;
    }

    public function getMnozstvo(): float
    {
        return $this->mnozstvo;
    }

    public function setMnozstvo(float $mnozstvo): void
    {
        $this->mnozstvo = $mnozstvo;
    }

    public function jsonSerialize(): array
    {
        return [
            'ingrediencia' => $this->ingrediencia,
            'mernaJednotka' => $this->mernaJednotka,
            'mnozstvo' => $this->mnozstvo,
        ];
    }
}
?>