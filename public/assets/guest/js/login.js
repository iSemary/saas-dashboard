$("#organizationForm").on("submit", function (e) {
    e.preventDefault();

    const organizationName = $("#organization_name").val();
    let url = $(this).attr("action");
    if (!organizationName) {
        alert("Organization name is required");
        return;
    }
    $.ajax({
        url: url,
        method: "POST",
        data: {
            organization_name: organizationName,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response.success) {
                $("#organizationForm").hide();
                $("#loginForm").show();
            } else {
                alert(response.message || "Invalid organization name.");
            }
        },
        error: function () {
            alert("An error occurred. Please try again.");
        },
    });
});

$("#loginForm").on("submit", function (e) {
    e.preventDefault();

    let url = $(this).attr("action");

    $.ajax({
        url: url,
        method: "POST",
        data: {
            organization_name: organizationName,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response.success) {
                $("#organizationForm").hide();
                $("#loginForm").show();
            } else {
                alert(response.message || "Invalid organization name.");
            }
        },
        error: function () {
            alert("An error occurred. Please try again.");
        },
    });
});
