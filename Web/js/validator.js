(function() {
    $.fn.validate = function(options) {
        $.each(options, function(key, value) {
            if ($.isArray(value)) {
                var input = $('#' + key);
                if (!validateSingle(input, value['rule'])) {
                    makeInvalid(input, value['rule'], value['message']);
                }
            } else{
                validateSingle($('#' + key), value, null);
            }
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