$(document).on("change", "#LangSelect", function (e) {
    let Locale = $(this).val();
    let FullLocale = $("#LangSelect option:selected").text();

    let SelectedLangForm = `
        <div class="form-group">
            <label class="text-capitalize">${FullLocale}</label>
            <span class="text-danger">&nbsp;&nbsp;*</span>
            <span class="mx-3 text-danger cursor-pointer remove-locale" data-locale="${Locale}"><i class="fas fa-times-circle"></i></span>
            <textarea type="text" class="form-control text-capitalize" name="locales[${Locale}]" minlength="1" maxlength="500" placeholder="${FullLocale}" required></textarea>
        </div>
`;

    $("#Translations").append(SelectedLangForm);
    $("#LangSelect option:selected").attr("disabled", true);
});
$(document).on("click", ".remove-locale", function (e) {
    let RemovedLocale = $(this).attr("data-locale");
    $("#LangSelect option[value='" + RemovedLocale + "']").attr(
        "disabled",
        false
    );
    $(this).parent().remove();
});

$(document).on("input", ".slug-input", function () {
    var inputValue = $(this).val();
    inputValue = inputValue.replace(/\s+/g, "-");
    inputValue = inputValue.replace(/[٠-٩]/g, function (digit) {
        return String.fromCharCode(digit.charCodeAt(0) - 1632 + 48);
    });
    inputValue = inputValue.replace(/[^a-zA-Z0-9\-]/g, "");
    $(this).val(inputValue);
});
$(document).on("input", ".snake-input", function () {
    let inputValue = $(this).val();

    // Replace spaces with underscores
    inputValue = inputValue.replace(/\s+/g, "_");

    // Convert Arabic numerals to English numerals
    inputValue = inputValue.replace(/[\u0660-\u0669]/g, function (digit) {
        return String.fromCharCode(digit.charCodeAt(0) - 0x0660 + 48);
    });

    // Remove any characters that are not alphanumeric, hyphens, or underscores
    inputValue = inputValue.replace(/[^a-zA-Z0-9\-_]/g, "");

    // Update the input value
    $(this).val(inputValue);
});

$(".decimal-input").on("input", function (e) {
    // Remove any non-numeric characters except decimal point
    let value = $(this)
        .val()
        .replace(/[^0-9.]/g, "");

    // Ensure only one decimal point
    let decimalCount = (value.match(/\./g) || []).length;
    if (decimalCount > 1) {
        value = value.replace(/\.(?=.*\.)/g, "");
    }

    // Limit to 5 decimal places
    if (value.includes(".")) {
        let parts = value.split(".");
        if (parts[1].length > 5) {
            parts[1] = parts[1].substring(0, 5);
            value = parts.join(".");
        }
    }

    // Update input value
    $(this).val(value);
});

$(".decimal-input").on("blur", function () {
    let value = $(this).val();
    if (value !== "") {
        $(this).val(parseFloat(value).toFixed(5));
    }
});
