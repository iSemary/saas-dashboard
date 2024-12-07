let lang = null;
$.ajax({
    dataType: "json",
    url: document.currentScript.getAttribute('locale'),
    cache: true,
    async: false,
    success: function(data) {
        lang = data;
    }
});
