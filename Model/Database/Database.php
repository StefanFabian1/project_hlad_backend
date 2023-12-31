<?php
class Database
{
    protected $connection = null;

    public function __construct()
    {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASENAME);
            if (mysqli_connect_errno()) {
                throw new Exception("Nedá sa pripojiť k databáze.");
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    protected function insert(string $query, array $values = []) {
        $types = "";
        foreach($values as $value) {
            $types .= $this->getDataTypeSpecifier(gettype($value));
        }
        var_dump($query);
        //var_dump($values);
        //var_dump($types);
        $this->executeStmt($query, $values, $types);
        return $this->connection->insert_id;
    }

    protected function select(string $query, array $values = []) :array
    {
        $types = "";
        foreach ($values as $value) {
            $types .= $this->getDataTypeSpecifier(gettype($value));
        }
        var_dump($query);
        //var_dump($values);
        //var_dump($types);
        return $this->executeStmt($query, $values, $types);
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

        var_dump($query);
        //var_dump($values);
        //var_dump($types);

        $this->executeStmt($query, $values, $types);
        return $this->connection->insert_id;
    }

    protected function updateWithQuery(string $query, array $values = []) {
        $types = "";
        foreach ($values as $value) {
            $types .= $this->getDataTypeSpecifier(gettype($value));
        }
        var_dump($query);
        //var_dump($values);
        //var_dump($types);
        return $this->executeStmt($query, $values, $types);
    }

    protected function update(object $object)
    {
        $tableName = $object->getTableName();

        $reflector = new ReflectionClass($object);
        $properties = $reflector->getProperties(ReflectionProperty::IS_PRIVATE);

        $setStatements = [];
        $values = [];
        $types = '';

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            if ($propertyName !== 'tableName') {
                if ($property->getValue($object) == null) {
                    $setStatements[] = "$propertyName = NULL";
                } else {
                    $setStatements[] = "$propertyName = ?";
                    $values[] = $property->getValue($object);
                    $types .= $this->getDataTypeSpecifier($property->getType()->getName());
                }
            }
        }

        $setStatementsStr = implode(', ', $setStatements);

        $query = "UPDATE $tableName SET $setStatementsStr WHERE id = ?";

        $values[] = $object->getId();
        $types .= "i";

        var_dump($query);
        //var_dump($values);
        //var_dump($types);

        return $this->executeStmt($query, $values, $types);
    }

    protected function delete(string $query, array $values = []) {
        $types = "";
        foreach ($values as $value) {
            $types .= $this->getDataTypeSpecifier(gettype($value));
        }
        var_dump($query);
        //var_dump($values);
        //var_dump($types);
        return $this->executeStmt($query, $values, $types);
    }

    private function executeStmt(string $query = "", array $values = [], string $types = '')
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
                return mysqli_stmt_affected_rows($stmt);
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
            case 'integer':
            case 'int':
                return 'i'; // Integer
            case 'float':
                return 'd'; // Double
            case 'boolean':
                return '';
                // Add more cases for other data types as needed
            default:
                return 's'; // Default to string if type is not recognized
        }
    }
}
