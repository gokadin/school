<?php

namespace App\Http\Translators;

use Library\Http\Request;
use Library\Transformer\Transformer;

abstract class Translator
{
    /**
     * @var Transformer
     */
    protected $transformer;

    public function __construct(Transformer $transformer)
    {
        $this->transformer = $transformer;
    }

    abstract function translateRequest(Request $request);
}