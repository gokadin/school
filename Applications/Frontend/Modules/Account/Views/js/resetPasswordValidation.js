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

    $('#email').on('input', function() {
        check['email']('#email');
    });

    $('#reset-password-form').on('submit', function() {
        var isValid = true;

        $.each(check, function(key, value) {
            isValid = value('#' + key) && isValid;
        });

        if (isValid) {
            $('#reset-validation-message').show();
        }

        return false;
    });
});