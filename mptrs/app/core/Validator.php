<?php
class Validator {
    public static function required($value): bool {
        return !is_null($value) && trim((string)$value) !== '';
    }
    public static function email(string $v): bool {
        return filter_var($v, FILTER_VALIDATE_EMAIL) !== false;
    }
    public static function int($v): bool {
        return filter_var($v, FILTER_VALIDATE_INT) !== false;
    }
    public static function numeric($v): bool {
        return is_numeric($v);
    }
    public static function inArray($v, array $arr): bool {
        return in_array($v, $arr, true);
    }
}