<?php
    class WhereClause {

        private string $operation;
        private string $attribute;
        private $value;
        private bool $negated;

        public function __construct(string $operation, string $attribute, $value, bool $negated)
        {
            $this->operation = $operation;
            $this->attribute = $attribute;
            $this->value = $value;
            $this->negated = $negated;
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

        public function isNegated() : bool
        {
            return $this->negated;
        }
    }
?>