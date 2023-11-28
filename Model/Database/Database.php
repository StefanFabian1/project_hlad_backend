<?php
class Database
{
    
    protected $connection = null;

    public function __construct()
    {
        try {
            require_once PROJECT_ROOT_PATH . "\\Model\\Database\\WhereClause.php";
            $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASENAME);
            if (mysqli_connect_errno()) {
                throw new Exception("Nedá sa pripojiť k databáze.");
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //TODO nebude select ale getAll, GetWithCriteria, zakladne queriny budu tu, rozsirujuce/specificke v extednujucej metode
    protected function select(string $query = "", array $params = []): array
    {
        try {
            $stmt = $this->executeStmt($query, $params);
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            $stmt->close();
            throw new Exception($e->getMessage());
        }
        return false;
    }

    protected function getWithCriteria(string $table, array $cols, array $wheres): void
    {
        $query = "SELECT \? FROM \?";
        $params = array();
        if (count($cols) > 0) {
            foreach ($cols as $col) {
                array_push($params, $col);
            }
        } else {
            array_push($params, "*");
        }
        array_push($params, $table);

        if (count($wheres) > 0 ) {
            for($i = 0; $i < count($wheres); $i++) {
                $where = $wheres[$i];
                if ($i == 0) {
                    $query += "WHERE \?";
                } else {
                    $query += $where->getOperation() . " \?";
                }
                if ($where->isNegated()) {
                    $query += $this->getNegatedVariableSpecificWhereOperator($where->getValue());
                } else {
                    $query += $this->getVariableSpecificWhereOperator($where->getValue());
                }
                array_push($params, $where->getAttribute());
                array_push($params, $where->getValue());
            }
        }
    }

    private function getVariableSpecificWhereOperator($value): string
    {
        if (gettype($value) === "integer" || gettype($value) === "double" || gettype($value) === "float") {
            return " = ?";
        }
        if (gettype($value) === "string") {
            return " LIKE \?";
        }
        if (gettype($value) === "boolean" || gettype($value) === "NULL") {
            return " IS \?";
        }
        if (gettype($value) === "array") {
            $result = " IN (";
            foreach ($value as $val) {
                $result += "?,";
            }
            $result = rtrim($result, ',');
            return $result . ")";
        }
        //TODO handle error
    }

    private function getNegatedVariableSpecificWhereOperator($value): string
    {
        if (gettype($value) === "integer" || gettype($value) === "double" || gettype($value) === "float") {
            return " != ?";
        }
        if (gettype($value) === "string") {
            return " NOT LIKE \?";
        }
        if (gettype($value) === "boolean" || gettype($value) === "NULL") {
            return " IS NOT \?";
        }
        if (gettype($value) === "array") {
            $result = " NOT IN (";
            foreach ($value as $val) {
                $result += "?,";
            }
            $result = rtrim($result, ',');
            return $result . ")";
        }
        //TODO handle error
    }

    private function executeStmt(string $query = "", array $params = []): mysqli_stmt
    {
        try {
            $stmt = $this->connection->prepare($query);
            if ($stmt === false) {
                throw new Exception("Bad query:" . $query);
            }
            if ($params) {
                $stmt->bind_param($params[0], $params[1]);
            }
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            $stmt->close();
            throw new Exception($e->getMessage());
        }
    }
}
