<?php
    require_once PROJECT_ROOT_PATH . "\\Model\\Database\\Database.php";

    class TestModel extends Database {
        public function getTestData() : array {
            return $this->select("SELECT * FROM test ORDER BY id ASC");
        }
    }
?>