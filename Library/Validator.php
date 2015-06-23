<?php namespace Library;

class Validator
{
    public function required($value)
    {
        if ($value == null || trim($value) === '')
            return false;

        return true;
    }

    public function number($value)
    {
        return is_numeric($value);
    }

    public function min($value, $min)
    {
        // ...
    }
}