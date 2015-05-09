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
		$this->currentUser->save();
		
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
		$cardNumber = Request::postData('cardNumber');
		$cardName = Request::postData('cardName');
		$cardCode = Request::postData('cardCode');
		$expirationMonth = Request::postData('expirationMonth');
		$expirationYear = Request::postData('expirationYear');
		echo $cardNumber;
		if (!ctype_digit($cardNumber) || $cardNumber > 99999999999999999999 || $cardNumber < 1111111)
		{
			Session::setFlash('Card number is not valid.');
			Response::back();
		}
		
		
	}
}