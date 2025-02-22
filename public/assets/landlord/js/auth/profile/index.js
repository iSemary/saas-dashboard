$(document).ready(function () {
    $("#profileTabs button").on("click", function (e) {
        e.preventDefault();
        $(this).tab("show");
    });
});

// Handle avatar removal
$("#removeAvatar").on("click", function (e) {
    e.preventDefault();

    // Add SweetAlert confirmation
    Swal.fire({
        title: t("are_you_sure"),
        text: t("you_want_delete_this_avatar"),
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: t("delete"),
        cancelButtonText: t("cancel"),
    }).then((result) => {
        if (result.value) {
            let tableRoute = $(this).parent("form").attr("action");

            $.ajax({
                url: route,
                type: "POST",
                data: {
                    _token: csrfToken,
                    _method: "PUT",
                    type: "general",
                    remove_avatar: 1,
                },
                success: function (response) {
                    if (response.status === 200) {
                        window.location.reload();
                    }
                },
            });
        }
    });
});

$(document).on("click", ".reset-factor-authentication", function (e) {
    let url = $(this).data("route");
    let btn = $(this);

    btn.prop("disable", true);
    
    $.ajax({
        type: "POST",
        url: url,
        dataType: "json",
        success: function (response) {
            location.reload();
        },
        error: function (xhr) {
            btn.prop("disable", false);
        },
    });
});
