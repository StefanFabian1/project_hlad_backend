<?php
interface ModelInterface
{
    public function getTableName(): string;

    public function validate(): bool;
}
?>