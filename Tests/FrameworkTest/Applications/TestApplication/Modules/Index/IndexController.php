<?php namespace Tests\FrameworkTest\Applications\TestApplication\Modules\Index;

use Library\BackController;

class IndexController extends BackController
{
    public function index()
    {

    }

    public function testTokenValidation()
    {
        $this->validateToken();
    }
}