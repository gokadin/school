$(document).ready(function() {
    var check = {};

    check['name'] = function(id) {
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
    };

    check['defaultRate'] = function(id) {
        var input = $(id);
        var icon = input.parent();
        var messages = icon.nextAll('span');

        if (!input.val()) {
            input.removeClass('valid').addClass('invalid');
            icon.addClass('show');
            messages.eq(1).removeClass('show');
            messages.eq(0).addClass('show');
            return false;
        }

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

    $('#name').on('input', function() {
        check['name']('#name');
    });

    $('#defaultRate').on('input', function() {
        check['defaultRate']('#defaultRate');
    });

    $('#create-activity-form').on('submit', function(e) {
        var isValid = true;

        $.each(check, function(key, value) {
            isValid = value('#' + key) && isValid;
        });

        return isValid;
    });
});