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
                if ($receptData['image_id'] != null) {
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

                    if ($receptData['image_id'] != null) {
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

    public function saveRecept(array $receptData, int $userId): ?int
    {
        $recept = $this->createReceptObject($receptData, $userId);
        try {
            $this->connection->begin_transaction();

            $receptId = $this->insertRecept($recept);
            if ($receptId < 1) {
                $this->connection->rollback();
                throw new Exception("Failed to insert RECEPT, rollback executed");
            }

            if ($recept->getImage() != null) {
                $this->processReceptImage($recept);
            }
            $this->processIngrediencieReceptu($recept);
            $this->connection->commit();
            $recept->setId($receptId);
            return $recept->getId();
        } catch (Exception $e) {
            $this->connection->rollback();
            throw $e;
        }
    }

    private function createReceptObject(array $receptData, int $userId): Recept
    {
        $recept = new Recept();

        // Recept
        $recept->setId($receptData['id'] ?? null);
        $recept->setName($receptData['name']);
        $recept->setDescription($receptData['description'] ?? null);
        $recept->setSukromny($receptData['sukromny']);

        // vlastnik
        $uzivatel = new Uzivatel();
        $uzivatel->setId($userId);
        $recept->setVlastnik($uzivatel);

        // image
        $imageData = $receptData['image'] ?? [];
        if (!empty($imageData)) {
            $obrazok = new Obrazok();
            $obrazok->setId($imageData['id'] ?? null);
            $obrazok->setOriginal_name($imageData['name'] ?? null);
            $recept->setImage($obrazok);
        } else {
            $recept->setImage(null);
        }

        // ingrediencie
        $ingrediencieData = $receptData['ingrediencie'];
        $ingrediencieArray = [];
        foreach ($ingrediencieData as $ingrediencieData) {

            $ingredienciaReceptu = new IngredienciaReceptu();
            $ingredienciaReceptu->setId($ingrediencieData['id'] ?? null);
            $ingredienciaReceptu->setMnozstvo($ingrediencieData['mnozstvo'] ?? null);

            $ingredienciaData = $ingrediencieData['ingrediencia'];
            $ingrediencia = new Ingrediencia();
            $ingrediencia->setId($ingredienciaData['id'] ?? null);
            $ingrediencia->setName($ingredienciaData['name'] ?? null);
            $ingredienciaReceptu->setIngrediencia($ingrediencia);

            $mernaJednotkaData = $ingrediencieData['mernaJednotka'];
            $mernaJednotka = new MernaJednotka();
            $mernaJednotka->setId($mernaJednotkaData['id'] ?? null);
            $mernaJednotka->setUnit($mernaJednotkaData['unit'] ?? null);
            $mernaJednotka->setNazov($mernaJednotkaData['nazov'] ?? null);
            $ingredienciaReceptu->setMernaJednotka($mernaJednotka);

            $ingrediencieArray[] = $ingredienciaReceptu;
        }
        $recept->setIngrediencie($ingrediencieArray);
        return $recept;
    }

    private function insertRecept(Recept $recept): int
    {
        $query = "INSERT INTO RECEPT (name, description, sukromny, poc_zobrazeni, poc_likes, uzivatel_id) VALUES (?, ?, ?, ?, ?, ?)";
        $values = [$recept->getName(), $recept->getDescription(), (int) $recept->isSukromny(), 0, 0, $recept->getVlastnik()->getId()];
        return $this->insert($query, $values);
    }

    private function processReceptImage(Recept $recept): void
    {
        $obrazokToSave = $recept->getImage();
        $obrazokToSave->setReceptId($recept->getId());
        $obrazokToSave->setPath(PATH_TO_IMAGE_STORAGE);
        try {
            $imageId = $this->save($obrazokToSave);

            if ($imageId < 1) {
                throw new Exception("Failed to save image");
            }

            $obrazokToSave->setId($imageId);
            $obrazokToSave->setName("recept_image_" . $obrazokToSave->getId());
            $this->update($obrazokToSave);
            //TODO fyzicky ulozit obrazok, respektive to nechat na fe
            $recept->setImage($obrazokToSave);
        } catch (Exception $e) {
            $this->connection->rollback();
            throw new Exception("Image processing failed: " . $e->getMessage());
        }
    }

    private function processIngrediencieReceptu(Recept $recept): void
    {
        $spracovaneIngrediencieReceptu = [];
        $query = "INSERT INTO ingrediencia_receptu (recept_id, ingrediencia_id, mnozstvo, merna_jednotka_id) VALUES (?, ?, ?, ?)";
        foreach ($recept->getIngrediencie() as $ingredienciaReceptu) {
            if ($ingredienciaReceptu->getIngrediencia()->getId() == null) {
                //musim savnut novu ingredienciu
                $ingrediencia = new Ingrediencia();
                $ingrediencia->setName($ingredienciaReceptu->getIngrediencia()->getName());
                $ingredienciaId = $this->save($ingrediencia);
                if ($ingredienciaId < 1) {
                    $this->connection->rollback();
                    throw new Exception("Failed to save ingrediencia");
                }
                $ingrediencia->setId($ingredienciaId);
                $ingredienciaReceptu->setIngrediencia($ingrediencia);
            }

            if ($ingredienciaReceptu->getMernaJednotka()->getId() == null) {
                //musim savnut novu mernu jednotku
                $mernaJednotka = new MernaJednotka();
                $mernaJednotka->setUnit($ingredienciaReceptu->getMernaJednotka()->getUnit());
                $mernaJednotka->setNazov($ingredienciaReceptu->getMernaJednotka()->getNazov());
                $mernaJednotkaId = $this->save($mernaJednotka);
                if ($mernaJednotkaId < 1) {
                    $this->connection->rollback();
                    throw new Exception("Failed to save merna jednotka");
                }
                $mernaJednotka->setId($mernaJednotkaId);
                $ingredienciaReceptu->setMernaJednotka($mernaJednotka);
            }
            $values = [$recept->getId(), $ingredienciaReceptu->getIngrediencia()->getId(), $ingredienciaReceptu->getMnozstvo(), $ingredienciaReceptu->getMernaJednotka()->getId()];
            $ingredienciaReceptuId = $this->insert($query, $values);
            if ($ingredienciaReceptuId < 1) {
                $this->connection->rollback();
                throw new Exception("Failed to save ingrediencia receptu");
            }
            $ingredienciaReceptu->setId($ingredienciaReceptuId);
            array_push($spracovaneIngrediencieReceptu, $ingredienciaReceptu);
        }
        $recept->setIngrediencie($spracovaneIngrediencieReceptu);
    }

    public function deleteRecept(int $receptId, int $userId): ?int
    {
        //najprv selectneme userid where receptid
        $query = "SELECT uzivatel_id FROM recept WHERE id = ?";
        $values = [$receptId];
        $data = $this->select($query, $values);
        var_dump($userId);
        if (!empty($data)) {
            if ($data[0]['uzivatel_id'] == $userId) {
                try {
                    $this->connection->begin_transaction();

                    $query = "DELETE FROM image WHERE recept_id = ?";
                    $this->delete($query, $values);

                    $query = "DELETE FROM ingrediencia_receptu WHERE recept_id = ?";
                    $this->delete($query, $values);

                    $query = "DELETE FROM uzivatel_can_see_recept WHERE recept_id = ?";
                    $this->delete($query, $values);

                    $query = "DELETE FROM skupina_can_see_recept WHERE recept_id = ?";
                    $this->delete($query, $values);

                    $query = "DELETE FROM recept WHERE id = ?";
                    $this->delete($query, $values);

                    $this->connection->commit();
                    return 1;
                } catch (Exception $e) {
                    $this->connection->rollback();
                    throw $e;
                }
            } else {
                return null;
            }
        } else {
            return 0;
        }
    }

    public function updateRecept(array $receptData, int $receptId, int $userId): ?int
    {
        $query = "SELECT uzivatel_id FROM recept WHERE id = ?";
        $values = [$receptId];
        $data = $this->select($query, $values);
        var_dump($userId);
        if (!empty($data)) {
            if ($data[0]['uzivatel_id'] == $userId && $receptId == $receptData['id']) {
                $this->connection->begin_transaction();
                $recept = $this->createReceptObject($receptData, $userId);
                $query = "UPDATE recept SET name = ?, description = ?, sukromny = ? WHERE id = ?";
                $values = [$recept->getName(), $recept->getDescription(), (int) $recept->isSukromny(), $recept->getId()];
                $this->updateWithQuery($query, $values);
                //image - ak pride id je to ten isty, ak pride name, jedna sa o novy, ak nepride nic, jedna sa o zmazanie povodneho
                if ($recept->getImage() == null || ($recept->getImage()->getName() != null && $recept->getImage()->getId() != null)) {
                    $query = "DELETE FROM image WHERE recept_id = ?";
                    $this->delete($query, array($$recept->getId()));
                } else {
                    if ($recept->getImage()->getName() != null) {
                        $query = "DELETE FROM image WHERE recept_id = ?";
                        $this->delete($query, array($$recept->getId()));
                        $this->processReceptImage($recept);
                    }
                }
                //ingrediencia receptu - povodne zmazem, pridam nove
                $query = "DELETE FROM ingrediencia_receptu WHERE recept_id = ?";
                $this->delete($query, array($$recept->getId()));
                $this->processIngrediencieReceptu($recept);

                $this->connection->commit();
            } else {
                return null;
            }
        } else {
            return 0;
        }
    }

    public function validate(): bool
    {
        //TODO implementation
        return true;
    }
}
