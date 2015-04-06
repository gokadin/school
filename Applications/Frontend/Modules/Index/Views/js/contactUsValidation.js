$(document).ready(function() {
    var check = {};

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

    check['email'] = function(id) {
        return checkRequired(id);
    };

    check['message'] = function(id) {
        var input = $(id);

        if (!input.val()) {
            input.removeClass('valid').addClass('invalid');
            return false;
        }

        input.removeClass('invalid').addClass('valid');
        return true;
    };

    $('#email').on('input', function() {
        check['email']('#email');
    });

    $('#message').on('input', function() {
        check['message']('#message');
    });

    $('#contact-us-form').on('submit', function() {
        var isValid = true;

        $.each(check, function(key, value) {
            isValid = value('#' + key) && isValid;
        });

        return isValid;
    });
});