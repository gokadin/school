$(document).ready(function() {
	$('#subscription-form').on('submit', function() {
		var subscriptionType = $('#subscription-form input[name=subscriptionType]:checked').val();
		var paymentMethod = $('#subscription-form select[name=paymentMethod]').val();

		$('#subscription-form-div').fadeOut(function() {
			if (paymentMethod == 1) {
				$('#subscription-credit-card-form-div').fadeIn();
			} else if (paymentMethod == 2) {
				$('#subscription-paypal-form-div').fadeIn();
			}
		});
		
		return false;
	});
});