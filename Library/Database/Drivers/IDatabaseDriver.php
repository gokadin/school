<?php

namespace Library\Database\Drivers;

interface IDatabaseDriver
{
    function persist($object);
}