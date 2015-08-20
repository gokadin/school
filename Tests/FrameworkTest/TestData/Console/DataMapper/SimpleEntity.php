<?php

namespace Tests\FrameworkTest\TestData\Console\DataMapper;
use Library\Database\DataMapper\DataMapperTimestamps;

/**
 * @Entity(name="simpleEntity")
 * @Other(x="1", y="2", z="3")
 */
class SimpleEntity
{
    use DataMapperTimestamps;

    /** @Id */
    protected $id;

    /** @Column(type="integer", indexed="true") */
    protected $one;

    /** @Column(name="customName", type="integer", size="12") */
    protected $two;

    /** @Column(type="string") */
    protected $str1;

    /** @Column(name="customName2", type="string", size="25") */
    protected $str2;

    /** @Column(type="text") */
    protected $text1;

    /** @Column(type="boolean") */
    protected $bool1;

    /** @Column(type="decimal") */
    protected $decimal1;

    /** @Column(type="decimal", size="2", precision="3") */
    protected $decimal2;
}