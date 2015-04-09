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

        //return isValid;

        // In reality the email would be processed and sent via PHP
        // or other server side language.
        // Therefore, we will simply print a message to the user for now.

        if (isValid) {
            alert('Thank you, your message was successfully sent!');
        }

        return false; // preventing form submission in all cases
    });
});