<?php

namespace App\Domain\Subscriptions;

use Library\DataMapper\DataMapperTimestamps;

/**
 * @Entity(name="subscriptions")
 */
class Subscription
{
    use DataMapperTimestamps;

    /** @Id */
    protected $id;

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
}