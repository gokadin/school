<?php namespace Applications\School\Modules\Teacher\Messaging;

use Library\BackController;

class MessagingController extends BackController
{
	public function index()
	{
		
	}

    public function test()
    {
        $this->validateToken();
        $this->validateRequest([
            'firstName' => 'required',
            'lastName' => 'required'
        ]);
    }
}