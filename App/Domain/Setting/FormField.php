<?php

namespace App\Domain\Setting;

use JsonSerializable;

/**
 * Class FormField
 * @package App\Domain\Setting
 */
class FormField implements JsonSerializable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var boolean
     */
    private $active;

    /**
     * @param $name string
     * @param $displayName string
     * @param $active
     */
    public function __construct($name, $displayName, $active)
    {
        $this->name = $name;
        $this->displayName = $displayName;
        $this->active = $active;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function displayName()
    {
        return $this->displayName;
    }

    /**
     * @param $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @param $string
     * @return mixed
     */
    public static function generateName($string)
    {
        return preg_replace('/\s+/', '', $string);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'displayName' => $this->displayName,
            'active' => $this->active
        ];
    }
}