$(document).ready(function() {
    var check = {};

    check['firstName'] = function(id) {
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

    check['lastName'] = function(id) {
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

    $('#edit-modal-firstName').on('input', function() {
        check['firstName']('#edit-modal-firstName');
    });

    $('#edit-modal-lastName').on('input', function() {
        check['lastName']('#edit-modal-lastName');
    });

    $('#edit-modal-save-button').on('click', function(e) {
        var isValid = true;

        $.each(check, function(key, value) {
            isValid = value('#edit-modal-' + key) && isValid;
        });

        if (isValid) {
            editCallBack();
        }
    });
});