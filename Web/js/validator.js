(function() {
    $.fn.validate = function(options) {
        $.each(options, function(key, value) {
            var input = $('#' + key);
            var rule = null;
            var message = null;
            var triggers = ['input'];

            if ($.isArray(value)) {
                rule = value['rule'];
                if ('message' in value) {
                    message = value['message'];
                }

                if ('triggers' in value) {alert('in triggers');
                    if ($.isArray(value['triggers'])) {
                        triggers = value['triggers'];
                    } else {
                        triggers = [];alert('here');
                        triggers.push(value['triggers']);
                    }
                }
            } else {
                rule = value;
            }

alert(triggers.join(' '));
            input.on(triggers.join(' '), function() {
                if (!validateSingle(input, rule)) {
                    makeInvalid(input, rule, message);
                } else {
                    makeValid(input);
                }
            });
        });
    };
    // add options for checking on input or on out of focus
    // add submit check
    function validateSingle(input, rule) {
        switch (rule) {
            case 'required':
                return required(input);
        }
    }

    function makeInvalid(input, rule, message) {
        input.removeClass('valid').addClass('invalid');
    }

    function makeValid(input) {
        input.removeClass('invalid').addClass('valid');
    }

    function required(input) {
        return false;
    }
}());