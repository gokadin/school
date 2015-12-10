<?php

namespace App\Domain\Setting;

use JsonSerializable;

class ExtraField implements JsonSerializable
{
    private $name;

    private $displayName;

    public function __construct($name, $displayName)
    {
        $this->name = $name;
        $this->displayName = $displayName;
    }

    public function name()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function displayName()
    {
        return $this->displayName;
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    public static function generateName($string)
    {
        return preg_replace('/\s+/', '', $string);
    }

    public function jsonSerialize()
    {
        return [
            'displayName' => $this->displayName,
        ];
    }
}