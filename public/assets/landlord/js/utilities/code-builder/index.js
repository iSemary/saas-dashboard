$(document).ready(function () {
    // Restrict inputs to English letters, underscores only, and no spaces
    function sanitizeInput(value) {
        return value.replace(/[^a-zA-Z_-]/g, ''); // Removes non-English letters, spaces, and disallowed characters except underscores
    }

    // Event listener for MODEL_NAME
    $('#MODEL_NAME').on('input', function () {
        let modelName = sanitizeInput($(this).val());
        $(this).val(modelName); // Update the field with sanitized input
        if (modelName) {
            const lowerCaseModelName = modelName.toLowerCase();
            $('#PLURAL_TITLE').val(lowerCaseModelName + 's'); // Set PLURAL_TITLE
            $('#SINGLE_TITLE').val(lowerCaseModelName); // Set SINGLE_TITLE
        } else {
            $('#PLURAL_TITLE').val('');
            $('#SINGLE_TITLE').val('');
        }
    });

    // Event listener for MODULE_NAME
    $('#MODULE_NAME').on('input', function () {
        let moduleName = sanitizeInput($(this).val());
        $(this).val(moduleName); // Update the field with sanitized input
        if (moduleName) {
            const lowerCaseModuleName = moduleName.toLowerCase();
            $('#MODULE_PLURAL_TITLE').val(lowerCaseModuleName + 's'); // Set MODULE_PLURAL_TITLE
        } else {
            $('#MODULE_PLURAL_TITLE').val('');
        }
    });

    // Event listener for all other inputs
    $('input').not('#MODEL_NAME, #MODULE_NAME').on('input', function () {
        let sanitizedValue = sanitizeInput($(this).val());
        $(this).val(sanitizedValue); // Update the field with sanitized input
    });
});
