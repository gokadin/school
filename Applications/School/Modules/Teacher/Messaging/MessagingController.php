<?php namespace Applications\School\Modules\Teacher\Messaging;

use Library\BackController;
use Library\Facades\Page;

class MessagingController extends BackController
{
	public function index()
	{
		Page::add('students', $this->currentUser->students());
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