$(document).ready(function() {
    var check = {};

    function checkRequired(id) {
        var input = $(id);
        var icon = input.parent();
        var messages = icon.nextAll('span');

        if (!input.val()) {
            input.removeClass('valid').addClass('invalid');
            icon.addClass('show');
            messages.eq(0).addClass('show');
            for (var i = 1; i < messages.length; i++) {
                messages.eq(i).removeClass('show');
            }
            
            return false;
        }

        input.removeClass('invalid').addClass('valid');
        icon.removeClass('show');
        for (var i = 0; i < messages.length; i++) {
            messages.eq(i).removeClass('show');
        }
        
        return true;
    }
    
    check['expirationMonth'] = function(id) {
        var input = $(id);
        
        if (input.val() == 0) {
            input.addClass('invalid');
            return false;
        }
        
        input.removeClass('invalid');
        return true;
    };
    
    check['expirationYear'] = function(id) {
        var input = $(id);
        
        if (input.val() == 0) {
            input.addClass('invalid');
            return false;
        }
        
        input.removeClass('invalid');
        return true;
    };
    
    check['cardNumber'] = function(id) {
        if (!checkRequired(id)) {
            return false;
        }
        
        var input = $(id);
        var icon = input.parent();
        var messages = icon.nextAll('span');
        
        if (!$.isNumeric(input.val())) {
            input.removeClass('valid').addClass('invalid');
            icon.addClass('show');
            messages.eq(0).removeClass('show');
            messages.eq(1).addClass('show');
            return false;
        }
        
        input.removeClass('invalid').addClass('valid');
        icon.removeClass('show');
        messages.eq(0).removeClass('show');
        messages.eq(1).removeClass('show');
        return true;
    };

    check['cardName'] = function(id) {
        return checkRequired(id);
    };
    
    check['cardCode'] = function(id) {
        if (!checkRequired(id)) {
            return false;
        }
        
        var input = $(id);
        var icon = input.parent();
        var messages = icon.nextAll('span');
        
        if (!$.isNumeric(input.val())) {
            input.removeClass('valid').addClass('invalid');
            icon.addClass('show');
            messages.eq(0).removeClass('show');
            messages.eq(1).addClass('show');
            messages.eq(2).removeClass('show');
            return false;
        }
        
        if (input.val().length != 3) {
            input.removeClass('valid').addClass('invalid');
            icon.addClass('show');
            messages.eq(0).removeClass('show');
            messages.eq(1).removeClass('show');
            messages.eq(2).addClass('show');
            return false;
        }
        
        input.removeClass('invalid').addClass('valid');
        icon.removeClass('show');
        messages.eq(0).removeClass('show');
        messages.eq(1).removeClass('show');
        messages.eq(2).removeClass('show');
        return true;
    };
    
    $('#cardNumber').on('input', function() {
        check['cardNumber']('#cardNumber');
    });

    $('#cardName').on('input', function() {
        check['cardName']('#cardName');
    });
    
    $('#cardCode').on('input', function() {
        check['cardCode']('#cardCode');
    });

    $('#subscription-credit-card-form').on('submit', function(e) {
        var isValid = true;

        $.each(check, function(key, value) {
            isValid = value('#' + key) && isValid;
        });

        return isValid;
    });
});