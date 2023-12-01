<?php
require_once PROJECT_ROOT_PATH . "\\Model\\Database\\Database.php";
require_once PROJECT_ROOT_PATH . "\\Model\\Recept.php";

class ReceptDAO extends Database {

    public function __construct()
    {
        parent::__construct();
    }

    public function findAll(?int $userId) : ?array {

        $filterMoje = true;
        $filterVerejne = true;
        $filterZdielane = true;
        $filterSkupinove = true;

        $values = array();
        if($userId == null) {
            $query = "SELECT * FROM recept WHERE sukromny IS FALSE";
        } else {
            $partQuery = array();
            $query = "SELECT * FROM recept WHERE ";
            if($filterMoje) {
                array_push($partQuery, "uzivatel_id = ?");
                array_push($values, $userId);
            }
            if ($filterVerejne) {
                array_push($partQuery, "sukromny IS FALSE");
            }
            if($filterZdielane) {
                array_push($partQuery, "EXISTS (SELECT uzivatel_id FROM uzivatel_can_see_recept WHERE uzivatel_id = ? AND recept_id = recept.id)");
                array_push($values, $userId);
            }
            if ($filterSkupinove) {
                array_push($partQuery, "EXISTS (SELECT skupina_uzivatelov_id FROM skupina_can_see_recept WHERE skupina_uzivatelov_id IN (SELECT skupina_uzivatelov_id FROM skupina_uzivatelov_has_uzivatel WHERE uzivatel_id = ?) AND recept_id = recept.id)");
                array_push($values, $userId);
            }
            $query .= implode(" OR ", $partQuery);
        }
        $data = $this->select($query, $values);
        
        if (!empty($data)) {
            $recepty = array();
                foreach($data as $receptData) {
                $recept = new Recept();
                $recept->setId($receptData['id']);
                $recept->setName($receptData['name']);
                $recept->setDescription($receptData['description']);
                $recept->setSukromny($receptData['sukromny']);
                $recept->setPoc_zobrazeni($receptData['poc_zobrazeni']);
                $recept->setPoc_likes($receptData['poc_likes']);
                array_push($recepty, $recept);
                }
            return $recepty;
        }
        return null;
    }

    public function find(?int $userId, int $receptId) : ?Recept {
       
        $values = array();
        $query = "SELECT id FROM recept WHERE id = ?";
        array_push($values, $receptId);
        $data = $this->select($query, $values);
        
        if (empty($data)) {
           return null;
        }

        $values = array();
        if ($userId == null) {
            $query = "SELECT * FROM recept WHERE sukromny IS FALSE AND id = ?";
            array_push($values, $receptId);
        } else {
            $partQuery = array();
            $query = "SELECT * FROM recept WHERE (";
            array_push($partQuery, "uzivatel_id = ?");
            array_push($values, $userId);
            array_push($partQuery, "sukromny IS FALSE");
            array_push($partQuery, "EXISTS (SELECT uzivatel_id FROM uzivatel_can_see_recept WHERE uzivatel_id = ? AND recept_id = recept.id)");
            array_push($values, $userId);
            array_push($partQuery, "EXISTS (SELECT skupina_uzivatelov_id FROM skupina_can_see_recept WHERE skupina_uzivatelov_id IN (SELECT skupina_uzivatelov_id FROM skupina_uzivatelov_has_uzivatel WHERE uzivatel_id = ?) AND recept_id = recept.id)");
            array_push($values, $userId);
            $query .= implode(" OR ", $partQuery) . ")";
            $query .= " AND id = ?";
            array_push($values, $receptId);
        }
        $data = $this->select($query, $values);
        if (!empty($data)) {
            $receptData = $data[0];
            $recept = new Recept();
            $recept->setId($receptData['id']);
            $recept->setName($receptData['name']);
            $recept->setDescription($receptData['description']);
            $recept->setSukromny($receptData['sukromny']);
            $recept->setPoc_zobrazeni($receptData['poc_zobrazeni']);
            $recept->setPoc_likes($receptData['poc_likes']);
            return $recept;
        }
        return new Recept();
    }

    public function validate(): bool
    {
       //TODO implementation
       return true;
    }
}

?>