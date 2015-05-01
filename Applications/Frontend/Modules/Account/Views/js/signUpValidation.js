$(document).ready(function() {
    var check = {};
    var checkExists = {};

    function checkRequired(id) {
        var input = $(id);
        var icon = input.parent();
        var message = icon.next('span');

        if (!input.val()) {
            input.removeClass('valid').addClass('invalid');
            icon.addClass('show');
            message.addClass('show');
            return false;
        }

        input.removeClass('invalid').addClass('valid');
        icon.removeClass('show');
        message.removeClass('show');
        return true;
    }
    
    checkExists['email'] = function(id) {
        if (!check['email']('#email')) {
            return false;
        }

        var input = $(id);
        var icon = input.parent();
        var messages = icon.nextAll('span');

        var data = input.val();
        var ajaxResult = null;
        $.ajax({
            async: false,
            type: 'POST',
            url: '/School/ajax/email-exists',
            datatype: 'html',
            data: {email: data},
            success: function(data) {
                ajaxResult = data;
            }
        });

        if (ajaxResult >= 1) {
            input.removeClass('valid').addClass('invalid');
            icon.addClass('show');
            messages.eq(0).removeClass('show');
            messages.eq(1).addClass('show');
            return false;
        } else {
            input.removeClass('invalid').addClass('valid');
            icon.removeClass('show');
            messages.eq(0).removeClass('show');
            messages.eq(1).removeClass('show');
            return true;
        }
    };

    check['firstName'] = function(id) {
        return checkRequired(id);
    };

    check['lastName'] = function(id) {
        return checkRequired(id);
    };

    check['email'] = function(id) {
        return checkRequired(id);
    };

    $('#firstName').on('input', function() {
        check['firstName']('#firstName');
    });

    $('#lastName').on('input', function() {
        check['lastName']('#lastName');
    });

    $('#email').on('input', function() {
        check['email']('#email');
    });
    
    $('#email').on('focusout', function() {
        checkExists['email']('#email');
    });

    $('#signup-form').on('submit', function() {
        var isValid = true;

        $.each(check, function(key, value) {
            isValid = value('#' + key) && isValid;
        });
        
        $.each(checkExists, function(key, value) {
            isValid = value('#' + key) && isValid;
        });

        return isValid;
    });
});