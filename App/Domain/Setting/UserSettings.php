<?php

namespace App\Domain\Setting;

use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;

class UserSettings
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    /** @Column(type="boolean", default="true") */
    protected $showTips;

    public function __construct($showTips = true)
    {
        $this->showTips = $showTips;
    }

    public function showTips()
    {
        return $this->showTips;
    }

    public function setShowTips($showTips)
    {
        $this->showTips = $showTips;
    }
}