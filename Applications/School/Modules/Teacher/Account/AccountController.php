<?php namespace Applications\School\Modules\Teacher\Account;

use Library\BackController;
use Library\Facades\Page;

class AccountController extends BackController
{
	public function index()
	{
		
	}
	
	public function subscription()
	{
		Page::add('subscription', $this->currentUser->subscription());
	}
}