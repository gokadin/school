(function() {
    $.fn.validate = function(options) {
        $.each(options, function(key, value) {
            var input = $('#' + key);
            var rule = null;
            var message = null;
            var triggers = ['input'];

            if (typeof value === 'object') {
                rule = value['rule'];
                if ('message' in value) {
                    message = value['message'];
                }

                if ('triggers' in value) {
                    if (typeof value['triggers'] === 'object') {
                        triggers = value['triggers'];
                    } else {
                        triggers = [];
                        triggers.push(value['triggers']);
                    }
                }
            } else {
                rule = value;
            }

            input.on(triggers.join(' '), function() {
                if (!validateSingle(input, rule)) {
                    makeInvalid(input, rule, message);
                } else {
                    makeValid(input);
                }
            });
        });
    };
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
        return input.val() != null && $.trim(input.val()).length > 0;
    }
}());