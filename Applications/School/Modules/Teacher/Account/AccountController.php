<?php namespace Applications\School\Modules\Teacher\Account;

use Library\BackController;
use Library\Facades\Page;
use Library\Facades\Request;
use Library\Facades\Response;
use Library\Facades\Session;
use Models\Subscription;

class AccountController extends BackController
{
	public function index()
	{
		
	}
	
	public function editPersonalInfo()
	{	
		$this->currentUser->first_name = Request::postData('firstName');
		$this->currentUser->last_name = Request::postData('lastName');
		$this->currentUser->email = Request::postData('email');
		
		if ($this->currentUser->save())
			Session::setFlash('Personal info updated successfully.');
		else
			Session::setFlash('There was an error processing your request.');
			
		Response::toAction('School#Teacher/Account#index');
	}
	
	public function changePassword()
	{
		$currentPassword = Request::postData('currentPassword');
		$password = Request::postData('password');
		$confirmPassword = Request::postData('confirmPassword');
		
		if (empty($currentPassword) || empty($password) || empty($confirmPassword))
			Session::setFlash('One or more fields are empty.');	
		
		if ($password != $confirmPassword)
			Session::setFlash('Confirmation password does not match.');
			
		if (md5($currentPassword) != $this->currentUser->password)
			Session::setFlash('Invalid password. Please try again.');
			
		$this->currentUser->password = md5($password);
		if ($this->currentUser->save())
			Session::setFlash('Password changed successfully.');
		else
			Session::setFlash('An error occured while processing your request.');	
		
		Response::toAction('School#Teacher/Account#index');
	}
	
	public function subscription()
	{
		Page::add('subscription', $this->currentUser->subscription());
	}
	
	public function subscriptionPayment()
	{
		$subscriptionType = Request::postData('subscriptionType');
		$paymentMethod = Request::postData('paymentMethod');
		
		switch ($paymentMethod)
		{
			case 1:
				Response::toAction('School#Teacher/Account#creditCardPayment', $subscriptionType);
				break;
			case 2:
				Response::toAction('School#Teacher/Account#paypalPayment', $subscriptionType);
				break;
			default:
				Session::setFlash('There was an error processing your request.');
				Response::toAction('School#Teacher/Account#subscription');
				break;	
		}
	}
	
	public function creditCardPayment()
	{
		$subscriptionType = Request::getData('subscriptionType');
		
		if ($subscriptionType < 1 || $subscriptionType > Subscription::SUBSCRIPTION_COUNT)
		{
			Session::setFlash('There was an error processing your request.');
			Response::toAction('School#Teacher/Account#subscription');
		}
		
		$subscription = new Subscription();
		$subscription->type = $subscriptionType;
		Page::add('subscription', $subscription);
	}
	
	public function paypalPayment()
	{
		$subscriptionType = Request::getData('subscriptionType');
		
		if ($subscriptionType < 1 || $subscriptionType > Subscription::SUBSCRIPTION_COUNT)
		{
			Session::setFlash('There was an error processing your request.');
			Response::toAction('School#Teacher/Account#subscription');
		}
	}
	
	public function processCreditCardPayment()
	{
		\Stripe\Stripe::setApiKey("sk_test_dVsKknKBGTxmMO87Aah9MBzn");

		$token = Request::postData('stripeToken');
		$subscriptionType = Request::postData('subscriptionType');
		if ($subscriptionType < 1 || $subscriptionType > 4)
		{
			Session::setFlash('An error has occured. Your card was not charged.');
			Response::back();
		}

		$customer = \Stripe\Customer::create(array(
		  "source" => $token,
		  "plan" => $subscriptionType,
		  "email" => $this->currentUser->email));
		  
	  	Session::setFlash('Thank you for subscribing. blablabla.');
		Response::toAction('School#Teacher/Index#index');
	}
}