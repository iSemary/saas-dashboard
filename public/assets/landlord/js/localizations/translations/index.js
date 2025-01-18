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
            btn.html(translate.generating + "...").prop("disabled", true);
        },
        success: function (response) {
            btn.html(translate.generate_translations_json).prop(
                "disabled",
                false
            );
            Swal.fire({
                text: translate.translations_generated,
                confirmButtonText: translate.ok,
                type: "success",
                toast: true,
                position: "bottom",
            });
        },
        error: function (response) {
            btn.html(translate.generate_translations_json).prop(
                "disabled",
                false
            );
            Swal.fire({
                text: translate.translations_generate_failed,
                confirmButtonText: translate.ok,
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
            btn.html(translate.processing + "...").prop("disabled", true);
        },
        success: function (response) {
            btn.html(translate.sync_missing_translations).prop(
                "disabled",
                false
            );
            Swal.fire({
                text: translate.translations_synced,
                confirmButtonText: translate.ok,
                type: "success",
                toast: true,
                position: "bottom",
            });
        },
        error: function (response) {
            btn.html(translate.sync_missing_translations).prop(
                "disabled",
                false
            );
            Swal.fire({
                text: translate.translations_sync_failed,
                confirmButtonText: translate.ok,
                type: "error",
                toast: true,
                position: "bottom",
            });
        },
    });
});
