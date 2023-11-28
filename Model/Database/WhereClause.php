<?php
    class WhereClause {

        private string $operation;
        private string $attribute;
        private $value;

        public function __construct(string $operation, string $attribute, $value)
        {
            $this->operation = $operation;
            $this->attribute = $attribute;
            $this->value = $value;
        }

        public function getOperation() : string {
            return $this->operation;
        }

        public function getAttribute(): string
        {
            return $this->attribute;
        }

        public function getValue()
        {
            return $this->value;
        }
    }
?>