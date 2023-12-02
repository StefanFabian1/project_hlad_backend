<?php
require_once PROJECT_ROOT_PATH . "\\Model\\Database\\Database.php";
require_once PROJECT_ROOT_PATH . "\\Model\\Recept.php";
require_once PROJECT_ROOT_PATH . "\\Model\\Ingrediencia.php";
require_once PROJECT_ROOT_PATH . "\\Model\\IngredienciaReceptu.php";
require_once PROJECT_ROOT_PATH . "\\Model\\MernaJednotka.php";
require_once PROJECT_ROOT_PATH . "\\Model\\Obrazok.php";
require_once PROJECT_ROOT_PATH . "\\Model\\Uzivatel.php";

class ReceptDAO extends Database
{

    public function __construct()
    {
        parent::__construct();
    }

    public function findAll(?int $userId): ?array
    {

        $filterMoje = true;
        $filterVerejne = true;
        $filterZdielane = true;
        $filterSkupinove = true;

        $values = array();
        $query = "SELECT recept.id AS 'recept_id', recept.name AS 'recept_name', recept.description, recept.sukromny, recept.poc_zobrazeni, recept.poc_likes, 
            image.id AS 'image_id', image.path
            FROM recept LEFT JOIN image ON image.recept_id = recept.id ";
        if ($userId == null) {
            $query .= "WHERE sukromny IS FALSE";
        } else {
            $partQuery = array();
            $query .= "WHERE ";
            if ($filterMoje) {
                array_push($partQuery, "recept.uzivatel_id = ?");
                array_push($values, $userId);
            }
            if ($filterVerejne) {
                array_push($partQuery, "recept.sukromny IS FALSE");
            }
            if ($filterZdielane) {
                array_push($partQuery, "EXISTS (SELECT uzivatel_can_see_recept.uzivatel_id FROM uzivatel_can_see_recept WHERE uzivatel_can_see_recept.uzivatel_id = ? AND uzivatel_can_see_recept.recept_id = recept.id)");
                array_push($values, $userId);
            }
            if ($filterSkupinove) {
                array_push($partQuery, "EXISTS (
                    SELECT skupina_can_see_recept.skupina_uzivatelov_id FROM skupina_can_see_recept WHERE skupina_can_see_recept.skupina_uzivatelov_id 
                        IN (SELECT skupina_uzivatelov_has_uzivatel.skupina_uzivatelov_id FROM skupina_uzivatelov_has_uzivatel WHERE skupina_uzivatelov_has_uzivatel.uzivatel_id = ?) 
                        AND skupina_can_see_recept.recept_id = recept.id)");
                array_push($values, $userId);
            }
            $query .= implode(" OR ", $partQuery);
        }
        $data = $this->select($query, $values);

        if (!empty($data)) {
            $recepty = array();
            foreach ($data as $receptData) {
                $recept = new Recept();
                $recept->setId($receptData['recept_id']);
                $recept->setName($receptData['recept_name']);
                $recept->setDescription($receptData['description']);
                $recept->setSukromny($receptData['sukromny']);
                $recept->setPocZobrazeni($receptData['poc_zobrazeni']);
                $recept->setPocLikes($receptData['poc_likes']);
                if($receptData['image_id'] != null) {
                    $image = new Obrazok();
                    $image->setId($receptData['image_id']);
                    $image->setId($receptData['path']);
                } else {
                    $recept->setImage(null);
                }
                $recept->setVlastnik(null);
                array_push($recepty, $recept);
            }
            return $recepty;
        }
        return null;
    }

    public function find(?int $userId, int $receptId): ?Recept
    {

        $values = array();
        $query = "SELECT id FROM recept WHERE id = ?";
        array_push($values, $receptId);
        $data = $this->select($query, $values);

        if (empty($data)) {
            return null;
        }

        $query = "SELECT recept.id AS 'recept_id', recept.name AS 'recept_name', recept.description, recept.sukromny, recept.poc_zobrazeni, recept.poc_likes,
                uzivatel.id AS 'pouzivatel_id', uzivatel.nick, image.id AS 'image_id', image.path, merna_jednotka.id AS 'merna_jednotka_id', merna_jednotka.unit, merna_jednotka.nazov,
                ingrediencia.id AS 'ingrediencia_id', ingrediencia.name AS 'ingrediencia_name', ingrediencia_receptu.id AS 'ingrediencia_receptu_id', ingrediencia_receptu.mnozstvo FROM recept 
                LEFT JOIN ingrediencia_receptu ON recept.id = ingrediencia_receptu.recept_id 
                LEFT JOIN ingrediencia ON ingrediencia.id = ingrediencia_receptu.ingrediencia_id 
                LEFT JOIN merna_jednotka ON merna_jednotka.id = ingrediencia_receptu.merna_jednotka_id
                LEFT JOIN image ON image.recept_id = recept.id
                LEFT JOIN uzivatel ON uzivatel.id = recept.uzivatel_id";

        $values = array();
        if ($userId == null) {
            $query .= " WHERE sukromny IS FALSE AND recept.id = ?";
            array_push($values, $receptId);
        } else {
            $partQuery = array();
            $query .= " WHERE (";
            array_push($partQuery, "recept.uzivatel_id = ?");
            array_push($values, $userId);
            array_push($partQuery, "sukromny IS FALSE");
            array_push($partQuery, "EXISTS (SELECT uzivatel_can_see_recept.uzivatel_id FROM uzivatel_can_see_recept WHERE uzivatel_can_see_recept.uzivatel_id = ? AND uzivatel_can_see_recept.recept_id = recept.id)");
            array_push($values, $userId);
            array_push($partQuery, "EXISTS (SELECT skupina_can_see_recept.skupina_uzivatelov_id FROM skupina_can_see_recept WHERE skupina_can_see_recept.skupina_uzivatelov_id IN (SELECT skupina_uzivatelov_has_uzivatel.skupina_uzivatelov_id FROM skupina_uzivatelov_has_uzivatel WHERE skupina_uzivatelov_has_uzivatel.uzivatel_id = ?) AND skupina_can_see_recept.recept_id = recept.id)");
            array_push($values, $userId);
            $query .= implode(" OR ", $partQuery) . ")";
            $query .= " AND recept.id = ?";
            array_push($values, $receptId);
        }
        $data = $this->select($query, $values);
        if (!empty($data)) {
            $spracovanyRecept = [];
            foreach ($data as $receptData) {
                if (!in_array($receptData['recept_id'], $spracovanyRecept)) {
                    $recept = new Recept();
                    $recept->setId($receptData['recept_id']);
                    $recept->setName($receptData['recept_name']);
                    $recept->setDescription($receptData['description']);
                    $recept->setSukromny($receptData['sukromny']);
                    $recept->setPocZobrazeni($receptData['poc_zobrazeni']);
                    $recept->setPocLikes($receptData['poc_likes']);

                    $uzivatel = new Uzivatel();
                    $uzivatel->setId($receptData['pouzivatel_id']);
                    $uzivatel->setNick($receptData['nick']);
                    $recept->setVlastnik($uzivatel);

                    if($receptData['image_id'] != null) {
                        $obrazok = new Obrazok();
                        $obrazok->setId($receptData['image_id']);
                        $obrazok->setPath(($receptData['path']));
                        $recept->setImage($obrazok);
                    } else {
                        $recept->setImage(null);
                    }

                    array_push($spracovanyRecept, $recept->getId());
                }
                if ($receptData['ingrediencia_receptu_id'] != null) {
                    $ingrediencieArray = $recept->getIngrediencie();
                    $ingredienciaReceptu = new IngredienciaReceptu();
                    $ingredienciaReceptu->setId($receptData['ingrediencia_receptu_id']);
                    $ingredienciaReceptu->setMnozstvo($receptData['mnozstvo']);
                    $ingrediencia = new Ingrediencia();
                    $ingrediencia->setId($receptData['ingrediencia_id']);
                    $ingrediencia->setName($receptData['ingrediencia_name']);
                    $ingredienciaReceptu->setIngrediencia($ingrediencia);
                    $mernaJednotka = new MernaJednotka();
                    $mernaJednotka->setId($receptData['merna_jednotka_id']);
                    $mernaJednotka->setUnit($receptData['unit']);
                    $mernaJednotka->setNazov($receptData['nazov']);
                    $ingredienciaReceptu->setMernaJednotka($mernaJednotka);
                    array_push($ingrediencieArray, $ingredienciaReceptu);
                    $recept->setIngrediencie($ingrediencieArray);
                }
            }
            return $recept;
        }
        return new Recept();
    }

    public function saveRecept(array $receptData) {

        $recept = new Recept();

        // Recept
        $recept->setName($receptData['name']);
        $recept->setDescription($receptData['description']);
        $recept->setSukromny($receptData['sukromny']);

        // vlastnik
        $uzivatelData = $receptData['vlastnik'];
        $uzivatel = new Uzivatel();
        $uzivatel->setId($uzivatelData['id']);
        $recept->setVlastnik($uzivatel);

        // image
        $imageData = $receptData['image'] ?? [];
        if (!empty($imageData)) {
            $obrazok = new Obrazok();
            $obrazok->setOriginalName($imageData['name']);
            //TODO po zapisani receptu ziskane id, cestu a novy nazov zapisem do obrazku
            $recept->setImage($obrazok);
        } else {
            $recept->setImage(null);
        }

        // ingrediencie
        $ingrediencieData = $receptData['ingrediencie'];
        $ingrediencieArray = [];
        foreach ($ingrediencieData as $ingrediencieData) {

            $ingredienciaReceptu = new IngredienciaReceptu();
            $ingredienciaReceptu->setMnozstvo($ingrediencieData['mnozstvo']);

            $ingredienciaData = $ingrediencieData['ingrediencia'];
            $ingrediencia = new Ingrediencia();
            $ingrediencia->setId($ingredienciaData['id'] ?? null);
            $ingrediencia->setName($ingredienciaData['name']);
            $ingredienciaReceptu->setIngrediencia($ingrediencia);

            $mernaJednotkaData = $ingrediencieData['mernaJednotka'];
            $mernaJednotka = new MernaJednotka();
            $mernaJednotka->setId($mernaJednotkaData['id'] ?? null);
            $mernaJednotka->setUnit($mernaJednotkaData['unit']);
            $mernaJednotka->setNazov($mernaJednotkaData['nazov']);
            $ingredienciaReceptu->setMernaJednotka($mernaJednotka);

            $ingrediencieArray[] = $ingredienciaReceptu;
        }
        $recept->setIngrediencie($ingrediencieArray);

        //TODO mozeme veselo zapisovat do DB
        var_dump($recept);
    }

    public function validate(): bool
    {
        //TODO implementation
        return true;
    }
}
