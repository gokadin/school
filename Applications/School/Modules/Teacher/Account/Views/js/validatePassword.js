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
    
    check['currentPassword'] = function(id) {
        return checkRequired(id);
    };
    
    check['password'] = function(id) {
        return checkRequired(id);
    };
    
    check['confirmPassword'] = function(id) {
        if (!checkRequired(id)) {
            return false;
        }
        
        var input = $(id);
        var icon = input.parent();
        var messages = icon.nextAll('span');
        
        if (input.val() != $('#password').val()) {
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
    
    $('#currentPassword').on('input', function() {
        check['currentPassword']('#currentPassword');
    });
    
    $('#password').on('input', function() {
        check['password']('#password');
    });

    $('#confirmPassword').on('input', function() {
        check['confirmPassword']('#confirmPassword');
    });
   
    $('#change-password-form').on('submit', function(e) {
        var isValid = true;

        $.each(check, function(key, value) {
            isValid = value('#' + key) && isValid;
        });
        
        return isValid;
    });
});