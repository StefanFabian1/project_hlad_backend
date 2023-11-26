<?php
    class Database {

        protected $connection = null;

        public function __construct() {
            try {
                $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASENAME);
                if (mysqli_connect_errno()) {
                    throw new Exception("Nedá sa pripojiť k databáze.");
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        //TODO nebude select ale getAll, GetWithCriteria, zakladne queriny budu tu, rozsirujuce/specificke v extednujucej metode
        protected function select(string $query = "", array $params = []) : array{
            try{
                $stmt = $this->executeStmt($query, $params);
                $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                return $result;
            }
            catch ( Exception $e ) {
                $stmt->close();
                throw new Exception( $e->getMessage());
            }
            return false;
        }

        private function executeStmt(string $query = "", array $params = []) : mysqli_stmt {
            try {
                $stmt = $this->connection->prepare( $query );
                if ($stmt === false) {
                    throw new Exception("Na picu query" . $query);
                }
                if ( $params ) {
                    $stmt->bind_param($params[0], $params[1]);
                }
                $stmt->execute();
                return $stmt;
            } catch ( Exception $e ) {
                $stmt->close();
                throw new Exception( $e->getMessage());
            }
        }
    }
?>