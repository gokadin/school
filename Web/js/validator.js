(function() {
    $.fn.validate = function(options) {
        $.each(options, function(key, value) {
            validatateSingle($('#' + key), value);
        });
    };
// has-errors is in the way...
    // dont add options for error display
    // add options for checking on input or on out of focus
    // add submit check
    function validatateSingle(input, rule) {
        switch (rule) {
            case 'required':
                return required(input);
        }
    }

    function required(input) {
        input.removeClass('valid').addClass('invalid');
        return false;
    }
}());