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

    $('#signup-form').on('submit', function() {
        var isValid = true;

        $.each(check, function(key, value) {
            isValid = value('#' + key) && isValid;
        });

        return isValid;
    });
});