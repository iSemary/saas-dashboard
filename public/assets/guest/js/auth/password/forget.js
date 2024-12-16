$("#organizationForm").on("submit", function (e) {
    e.preventDefault();

    const organizationName = $("#organizationName").val();
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
                $("#forgetPasswordOrganizationName").val(organizationName);
                $("#forgetPasswordForm").show();
            } else {
                alert(response.message || "Invalid organization name.");
            }
        },
        error: function () {
            alert("An error occurred. Please try again.");
        },
    });
});

$("#forgetPasswordForm").on("submit", function (e) {
    e.preventDefault();

    let url = $(this).attr("action");

    let email = $("#email").val().trim();
    let organizationName = $("#forgetPasswordOrganizationName").val().trim();

    // Validate fields
    if (!organizationName) {
        alert("Organization name is required.");
        return;
    }
    if (!email) {
        alert("email is required.");
        return;
    }

    $.ajax({
        url: url,
        method: "POST",
        data: {
            email: email,
            subdomain: organizationName,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                // window.location.href = response.data.redirect;
            }
        },
        error: function () {
            alert("An error occurred. Please try again.");
        },
    });
});
