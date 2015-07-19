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
        $userInfo = $this->currentUser->userInfo();
        $userInfo->first_name = Request::data('firstName');
        $userInfo->last_name = Request::data('lastName');
        $userInfo->email = Request::data('email');
		
		if ($userInfo->save())
			Session::setFlash('Personal info updated successfully.', 'success', 5);
		else
			Session::setFlash('There was an error processing your request.', 'error');
			
		Response::toAction('School#Teacher/Account#index');
	}
	
	public function changePassword()
	{
		$currentPassword = Request::data('currentPassword');
		$password = Request::data('password');
		$confirmPassword = Request::data('confirmPassword');
		
		if (empty($currentPassword) || empty($password) || empty($confirmPassword))
			Session::setFlash('One or more fields are empty.');	
		
		if ($password != $confirmPassword)
			Session::setFlash('Confirmation password does not match.');
			
		if (md5($currentPassword) != $this->currentUser->password)
			Session::setFlash('Invalid password. Please try again.');

        $userInfo = $this->currentUser->userInfo();
        $userInfo->password = md5($password);
		if ($userInfo->save())
			Session::setFlash('Password changed successfully.', 'success', 5);
		else
			Session::setFlash('An error occured while processing your request.', 'error');	
		
		Response::toAction('School#Teacher/Account#index');
	}
	
	public function editProfilePicture()
	{
		$target_dir = $_SERVER["DOCUMENT_ROOT"].'/Web/Uploads/';
		$extension = pathinfo($_FILES["profilePicture"]["name"], PATHINFO_EXTENSION);
		$targetFile = $target_dir.'profilePicture_'.$this->currentUser->id.'.'.$extension;
		$databaseLink = '/Web/Uploads/profilePicture_'.$this->currentUser->id.'.'.$extension;
		
	    if(!getimagesize($_FILES["profilePicture"]["tmp_name"])) 
		{
			Session::setFlash('File is not an image.', 'error');
			Response::toAction('School#Teacher/Account#index');
	    }

		if ($_FILES["profilePicture"]["size"] > 200000) 
		{
			Session::setFlash('File size cannot be greater than 2MB.', 'error');
			Response::toAction('School#Teacher/Account#index');
		}

		if($extension != "jpg" && $extension != "png" && $extension != "jpeg"
			&& $extension != "gif" ) 
		{
		    $uploadOk = false;
			Session::setFlash('Only JPG, JPEG, PNG and GIF files are allowed.', 'error');
			Response::toAction('School#Teacher/Account#index');
		}
		
		if (file_exists($targetFile))
		{
			rename($targetFile, $targetFile.'.temp');
		}
		
	    if (!move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $targetFile)) 
		{
			rename($targetFile.'.temp', $targetFile);
			
			Session::setFlash('There was an error while uploading your picture.', 'error');
			Response::toAction('School#Teacher/Account#index');
	    }

        $userInfo = $this->currentUser->userInfo();
        $userInfo->profile_picture = $databaseLink;

		if (!$userInfo->save())
		{
			if (file_exists($targetFile.'.temp'))
				rename($targetFile.'.temp', $targetFile);
			
			Session::setFlash('There was an error while uploading your picture.', 'error');
			Response::toAction('School#Teacher/Account#index');
		}
		
		if (file_exists($targetFile.'.temp'))
			unlink($targetFile.'.temp');
		
		Session::setFlash('Profile picture uploaded successfully.', 'success', 5);
        Response::toAction('School#Teacher/Account#index');
	}
	
	public function subscription()
	{
		Page::add('subscription', $this->currentUser->subscription());
	}
	
	public function subscriptionPayment()
	{
		$subscriptionType = Request::data('subscriptionType');
		$paymentMethod = Request::data('paymentMethod');
		
		switch ($paymentMethod)
		{
			case 1:
				Response::toAction('School#Teacher/Account#creditCardPayment', $subscriptionType);
				break;
			case 2:
				Response::toAction('School#Teacher/Account#paypalPayment', $subscriptionType);
				break;
			default:
				Session::setFlash('There was an error processing your request.', 'error');
				Response::toAction('School#Teacher/Account#subscription');
				break;	
		}
	}
	
	public function creditCardPayment()
	{
		$subscriptionType = Request::data('subscriptionType');
		
		if ($subscriptionType < 1 || $subscriptionType > Subscription::SUBSCRIPTION_COUNT)
		{
			Session::setFlash('There was an error processing your request.', 'error');
			Response::toAction('School#Teacher/Account#subscription');
		}
		
		$subscription = new Subscription();
		$subscription->type = $subscriptionType;
		Page::add('subscription', $subscription);
	}
	
	public function paypalPayment()
	{
		$subscriptionType = Request::data('subscriptionType');
		
		if ($subscriptionType < 1 || $subscriptionType > Subscription::SUBSCRIPTION_COUNT)
		{
			Session::setFlash('There was an error processing your request.', 'error');
			Response::toAction('School#Teacher/Account#subscription');
		}
	}
	
	public function processCreditCardPayment()
	{
		\Stripe\Stripe::setApiKey("sk_test_dVsKknKBGTxmMO87Aah9MBzn");

		$token = Request::data('stripeToken');
		$subscriptionType = Request::data('subscriptionType');
		if ($subscriptionType < 1 || $subscriptionType > 4)
		{
			Session::setFlash('An error has occured. Your card was not charged.', 'error');
			Response::toAction('School#Teacher/Account#subscription');
		}

		$customer = \Stripe\Customer::create(array(
		  "source" => $token,
		  "plan" => $subscriptionType,
		  "email" => $this->currentUser->userInfo()->email));
		  
	  	Session::setFlash('Thank you for subscribing. blablabla.', 'success', 5);
		Response::toAction('School#Teacher/Index#index');
	}
}