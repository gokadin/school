<?php

namespace App\Domain\Subscriptions;

use Library\DataMapper\DataMapperTimestamps;
use Library\DataMapper\DataMapperPrimaryKey;

/**
 * @Entity(name="subscriptions")
 */
class Subscription
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    /** @Column(type="integer", size="3") */
    protected $type;

    /** @Column(type="decimal", size="6", precision="2", default="-1") */
    protected $customRate;

    /** @Column(type="integer", size="5", default="1") */
    protected $period;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function type()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function customRate()
    {
        return $this->customRate;
    }

    public function setCustomRate($customRate)
    {
        $this->customRate = $customRate;
    }

    public function period()
    {
        return $this->period;
    }

    public function setPeriod($period)
    {
        $this->period = $period;
    }
}