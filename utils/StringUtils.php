<?php
    class StringUtils{

        public static function isEmpty(?string $str) : bool {
            return ($str === null || trim($str) === '');
        }
    }
?>