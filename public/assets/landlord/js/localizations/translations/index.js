let tableID = "#table";
let route = $(tableID).attr("data-route");
let cols = [
    {
        data: "id",
        name: "id",
    },
    {
        data: "language",
        name: "language",
    },
    {
        data: "translation_key",
        name: "translation_key",
    },
    {
        data: "translation_value",
        name: "translation_value",
    },
    {
        data: "translation_context",
        name: "translation_context",
    },
    {
        data: "is_shareable",
        name: "is_shareable",
    },
    {
        data: "actions",
        name: "actions",
        orderable: false,
        searchable: false,
    },
];

filterTable(route, tableID, null, null, true, cols, null);

// Generate json files from the translations
$(document).on("click", ".generate-translations-json", function () {
    let btn = $(this);
    let route = $(this).attr("data-route");
    let method = $(this).attr("data-method");

    $.ajax({
        url: route,
        type: method,
        beforeSend: function () {
            btn.find(".btn-text").html(t('generating'));
            btn.prop("disabled", true);
        },
        success: function (response) {
            btn.find(".btn-text").html(t('generate_translations_json'));
            btn.prop("disabled", false);
            Swal.fire({
                text: t('translations_generated'),
                confirmButtonText: t('ok'),
                type: "success",
                toast: true,
                position: "bottom",
            });
        },
        error: function (response) {
            btn.find(".btn-text").html(t('generate_translations_json'));
            btn.prop("disabled", false);
            Swal.fire({
                text: t('translations_generate_failed'),
                confirmButtonText: t('ok'),
                type: "error",
                toast: true,
                position: "bottom",
            });
        },
    });
});

// Generate json files from the translations
$(document).on("click", ".sync-missing-translations", function () {
    let btn = $(this);
    let route = $(this).attr("data-route");
    let method = $(this).attr("data-method");
    $.ajax({
        url: route,
        type: method,
        beforeSend: function () {
            btn.find(".btn-text").html(t('processing'));
            btn.prop("disabled", true);
        },
        success: function (response) {
            btn.find(".btn-text").html(t('sync_missing_translations'));
            btn.prop("disabled", false);
            Swal.fire({
                text: t('translations_synced'),
                confirmButtonText: t('ok'),
                type: "success",
                toast: true,
                position: "bottom",
            });
        },
        error: function (response) {
            btn.find(".btn-text").html(t('sync_missing_translations'));
            btn.prop("disabled", false);
            Swal.fire({
                text: t('translations_sync_failed'),
                confirmButtonText: t('ok'),
                type: "error",
                toast: true,
                position: "bottom",
            });
        },
    });
});
