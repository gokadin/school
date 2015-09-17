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

    /** @Column(type="text", nullable="true") */
    protected $text1;

    /** @Column(type="boolean", nullable="true") */
    protected $bool1;

    /** @Column(type="decimal", nullable="true") */
    protected $decimal1;

    /** @Column(type="decimal", size="3", precision="2", nullable="true") */
    protected $decimal2;

    public function __construct($one, $two, $str1, $str2)
    {
        $this->one = $one;
        $this->two = $two;
        $this->str1 = $str1;
        $this->str2 = $str2;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setOne($one)
    {
        $this->one = $one;
    }

    public function getOne()
    {
        return $this->one;
    }

    public function setTwo($two)
    {
        $this->two = $two;
    }

    public function getTwo()
    {
        return $this->two;
    }

    public function setStr1($str1)
    {
        $this->str1 = $str1;
    }

    public function getStr1()
    {
        return $this->str1;
    }

    public function setStr2($str2)
    {
        $this->str2 = $str2;
    }

    public function getStr2()
    {
        return $this->str2;
    }

    public function setText1($text1)
    {
        $this->text1 = $text1;
    }
}