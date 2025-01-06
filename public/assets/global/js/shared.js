$(document).on("change", "#LangSelect", function(e) {
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
    $("#LangSelect option:selected").attr("disabled",true);

});
$(document).on("click", ".remove-locale", function(e) {
    let RemovedLocale = $(this).attr('data-locale');
    $("#LangSelect option[value='"+RemovedLocale+"']").attr("disabled",false);
    $(this).parent().remove();
});


$(document).on('input', '.slug-input', function() {
    var inputValue = $(this).val();
    inputValue = inputValue.replace(/\s+/g, '-');
    inputValue = inputValue.replace(/[٠-٩]/g, function(digit) {
        return String.fromCharCode(digit.charCodeAt(0) - 1632 + 48);
    });
    inputValue = inputValue.replace(/[^a-zA-Z0-9\-]/g, '');
    $(this).val(inputValue);
});
