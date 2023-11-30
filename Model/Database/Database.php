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

    protected function save(object $object)
    {

        $tableName = $object->getTableName();

        $reflector = new ReflectionClass($object);
        $properties = $reflector->getProperties(ReflectionProperty::IS_PRIVATE);

        $columns = [];
        $values = [];
        $types = '';

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            if ($propertyName !== 'tableName' && $propertyName !== 'id') {
                $columns[] = $propertyName;
                $values[] = $property->getValue($object);
                $types .= $this->getDataTypeSpecifier($property->getType()->getName());
            }
        }

        $columnsStr = implode(', ', $columns);

        $valuesStr = implode(', ', array_fill(0, count($values), '?'));

        $query = "INSERT INTO $tableName ($columnsStr) VALUES ($valuesStr)";

        $this->executeStmt($query, $values, $types);

        return $this->connection->insert_id;
    }

    protected function select(string $table, ?array $cols, array $wheres): array
    {
        $columns = $cols ? implode(", ", $cols) : "*";
        $query = "SELECT $columns FROM $table";
        $params = [];
        $types = '';

        if (!empty($wheres)) {
            $query .= " WHERE ";
            foreach ($wheres as $i => $where) {
                if ($i > 0) {
                    $query .= " " . $where->getOperation() . " ";
                }

                $query .= $where->getAttribute();

                if ($where->isNegated()) {
                    $query .= $this->getNegatedVariableSpecificWhereOperator($where->getValue());
                } else {
                    $query .= $this->getVariableSpecificWhereOperator($where->getValue());
                }

                $params[] = $where->getValue();
                $types .= $this->getDataTypeSpecifier(gettype($where->getValue()));
            }
        }
        //var_dump($query);
        //var_dump($params);
        //var_dump($types);
       return $this->executeStmt($query, $params, $types);
    }


    private function getVariableSpecificWhereOperator($value): string
    {
        if (gettype($value) === "integer" || gettype($value) === "double" || gettype($value) === "float") {
            return " = ?";
        }
        if (gettype($value) === "string") {
            return " LIKE ?";
        }
        if (gettype($value) === "boolean" || gettype($value) === "NULL") {
            return " IS ?";
        }
        if (gettype($value) === "array") {
            $result = " IN (";
            foreach ($value as $val) {
                $result .= "?,";
            }
            $result = rtrim($result, ',');
            return $result . ")";
        }
        // TODO handle error
    }

    private function getNegatedVariableSpecificWhereOperator($value): string
    {
        if (gettype($value) === "integer" || gettype($value) === "double" || gettype($value) === "float") {
            return " != ?";
        }
        if (gettype($value) === "string") {
            return " NOT LIKE ?";
        }
        if (gettype($value) === "boolean" || gettype($value) === "NULL") {
            return " IS NOT ?";
        }
        if (gettype($value) === "array") {
            $result = " NOT IN (";
            foreach ($value as $val) {
                $result .= "?,";
            }
            $result = rtrim($result, ',');
            return $result . ")";
        }
        // TODO handle error
    }

    private function executeStmt(string $query = "", array $values = [], string $types = ''): ?array
    {
        $stmt = null;

        try {
            $stmt = $this->connection->prepare($query);

            if ($stmt === false) {
                throw new Exception("Failed to prepare query: $query");
            }

            if (!empty($values) && !empty($types)) {
                $stmt->bind_param($types, ...$values);
            }

            $stmt->execute();

            if (strcasecmp(substr(trim($query), 0, 6), 'SELECT') === 0) {
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);
                return $data;
            } else {
                return null;
            }

        } catch (Exception $e) {
            throw new Exception("Query execution failed: " . $e->getMessage(), 0, $e);
        } finally {
            if ($stmt !== null) {
                $stmt->close();
            }
        }
    }

    private function getDataTypeSpecifier(string $typeName)
    {
        switch ($typeName) {
            case 'int':
                return 'i'; // Integer
            case 'float':
                return 'd'; // Double
                // Add more cases for other data types as needed
            default:
                return 's'; // Default to string if type is not recognized
        }
    }
}
