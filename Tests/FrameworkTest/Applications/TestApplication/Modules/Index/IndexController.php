<?php namespace Tests\FrameworkTest\Applications\TestApplication\Modules\Index;

use Library\BackController;
use Library\Facades\Request;

class IndexController extends BackController
{
    public function index()
    {

    }

    public function testTokenValidation()
    {
        $this->validateToken();
    }

    public function testRequestValidation()
    {
        $this->validateRequest(['one' => 'required']);
    }

    public function testMultipleRequestValidation()
    {
        $this->validateRequest([
            'one' => ['required', 'numeric'],
            'two' => ['required', 'numeric']
        ]);
    }
}