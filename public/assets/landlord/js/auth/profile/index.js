$(document).ready(function () {
    $("#profileTabs button").on("click", function (e) {
        e.preventDefault();
        $(this).tab("show");
    });

    // Handle avatar removal
    $("#removeAvatar").on("click", function (e) {
        e.preventDefault();

        let route = $(this).parent("form").attr("action");

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
                    // window.location.reload();
                }
            },
        });
    });
});
