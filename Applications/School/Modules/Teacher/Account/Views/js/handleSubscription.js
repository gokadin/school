$(document).ready(function() {
	$('#subscription-credit-card-form').hide();
	$('#subscription-paypal-form').hide();
	
	$('#subscription-form').on('submit', function() {
		var subscriptionType = $('#subscription-form input[name=subscriptionType]:checked').val();
		var paymentMethod = $('#subscription-form select[name=paymentMethod]').val();

		$('#subscription-form .payment-method').hide();
		for (var i = 1; i < subscriptionCount + 1; i++) {
			if (i != subscriptionType) {
				$('#subscription-option-' + i).fadeOut();
			}
		}
		
		$('#subscription-form .subscription-form-submit button').hide();
		
		if (paymentMethod == 1) {
			$('#subscription-credit-card-form').fadeIn();
		} else if (paymentMethod == 2) {
			$('#subscription-paypal-form').fadeIn();
		}
		
		return false;
	});
});