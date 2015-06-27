(function() {
    $.fn.validate = function(options) {
        var check = {};

        $.each(options, function(key, value) {
            var input = $('#' + key);
            var parsedLines = [];

            $.each(value, function(index, line) {
                var rule = line['rule'];
                var message = null;
                var triggers = ['input'];

                if ('message' in line) {
                    message = line['message'];
                }

                if ('triggers' in line) {
                    if ($.isArray(line['triggers'])) {
                        triggers = line['triggers'];
                    } else {
                        triggers = [];
                        triggers.push(line['triggers']);
                    }
                }

                var tempArray = rule.split(':');
                var functionName = tempArray[0];
                var args = null;
                if (tempArray.length > 1) {
                    args = tempArray[1].split(',');
                }

                parsedLines.push({
                    input: input,
                    rule: {
                        functionName: functionName,
                        args: args
                    },
                    message: message,
                    triggers: triggers.join(' ')
                });
            });

            check[key] = parsedLines;
        });

        $.each(check, function(key, lines) {
            lines[0]['input'].on('input', function() {
                $.each(lines, function(index, line) {
                    if (!validateSingleWithError(key, line['input'], line['rule']['functionName'], line['rule']['args'], line['message'])) {
                        return false;
                    }
                });
            });
        });
    };

    function validateSingle(input, functionName, args) {
        switch (functionName) {
            case 'required':
                return required(input.val());
            case 'email':
                return email(input.val());
            case 'unique':
                return unique(input.val(), args[0], args[1]);
            case 'equalsField':
                return equalsField(input.val(), args[0]);
        }
    }

    function validateSingleWithError(key, input, functionName, args, message) {
        if (!validateSingle(input, functionName, args)) {
            makeInvalid(key, input, functionName, args, message);
            return false;
        } else {
            makeValid(input);
            return true;
        }
    }

    function makeInvalid(key, input, functionName, args, message) {
        input.removeClass('valid').addClass('invalid');
        var errorDiv = input.next('div.form-error');

        if (message != null) {
            errorDiv.html(message);
            return;
        }

        errorDiv.html(getErrorMessage(key, functionName, args));
    }

    function makeValid(input) {
        input.removeClass('invalid').addClass('valid');
        var errorDiv = input.next('div.form-error');
        errorDiv.html('');
    }

    function getErrorMessage(key, functionName, args) {
        switch (functionName) {
            case 'required':
                return key + ' is required';
            case 'email':
                return 'email format is invalid';
            case 'unique':
                return key + ' is already taken';
            case 'equalsField':
                return key + ' does not equal to ' + args[0];
        }
    }

    /* VALIDATIONS */

    function required(value) {
        return value != null && $.trim(value).length > 0;
    }

    function email(value) {
        var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
        return pattern.test(value);
    }

    function unique(value, modelName, columnName) {
        if (value == null || $.trim(value).length == 0) {
            return false;
        }

        $ajaxResult = null;
        $.ajax({
            async: false,
            type: 'POST',
            url: '/ajax/exists',
            datatype: 'html',
            data: {modelName: modelName, columnName: columnName, value: value},
            success: function(data) {
                ajaxResult = data;
            }
        });

        return ajaxResult != null && ajaxResult > 0;
    }

    function equalsField(value, fieldName) {
        return value == $('#' + fieldName).val();
    }
}());