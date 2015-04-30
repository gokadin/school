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

    check['password'] = function(id) {
        return checkRequired(id);
    };

    check['confirmPassword'] = function(id) {
        var input = $(id);
        var icon = input.parent();
        var messages = icon.nextAll('span');

        if (!input.val()) {
            input.removeClass('valid').addClass('invalid');
            icon.addClass('show');
            messages.eq(0).addClass('show');
            messages.eq(1).removeClass('show');
            return false;
        } else if (input.val() == $('#password').val()) {
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

    $('#password').on('input', function() {
        check['password']('#password');
    });

    $('#confirmPassword').on('input', function() {
        check['confirmPassword']('#confirmPassword');
    });

    $('#email-confirmation-form').on('submit', function() {
        var isValid = true;

        $.each(check, function(key, value) {
            isValid = value('#' + key) && isValid;
        });

        return isValid;
    });
});