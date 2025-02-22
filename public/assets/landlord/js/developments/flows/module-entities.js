$(document).on("click", ".sync-entities", function () {
    let btn = $(this);
    let tableRoute = $(this).attr("data-route");
    let method = $(this).attr("data-method");
    $.ajax({
        url: route,
        type: method,
        beforeSend: function () {
            btn.find(".btn-text").html(t('processing'));
            btn.prop("disabled", true);
        },
        success: function (response) {
            btn.find(".btn-text").html(t('sync_entities'));
            btn.prop("disabled", false);
            Swal.fire({
                text: t('entities_synced'),
                confirmButtonText: t('ok'),
                type: "success",
                toast: true,
                position: "bottom",
            });

            window.location.reload();
        },
        error: function (response) {
            btn.find(".btn-text").html(t('sync_entities'));
            btn.prop("disabled", false);
            Swal.fire({
                text: t('entities_sync_failed'),
                confirmButtonText: t('ok'),
                type: "error",
                toast: true,
                position: "bottom",
            });
        },
    });
});
