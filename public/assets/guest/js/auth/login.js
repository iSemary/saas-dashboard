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
                $("#loginOrganizationName").val(organizationName);
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

    let username = $("#username").val().trim();
    let password = $("#password").val().trim();
    let redirect = $("#redirect").val().trim();
    let rememberMe = $("#rememberMe").is(":checked");
    let organizationName = $("#loginOrganizationName").val().trim();

    // Validate fields
    if (!organizationName) {
        alert("Organization name is required.");
        return;
    }
    if (!username) {
        alert("Username is required.");
        return;
    }
    if (!password) {
        alert("Password is required.");
        return;
    }

    $.ajax({
        url: url,
        method: "POST",
        data: {
            subdomain: organizationName,
            username: username,
            password: password,
            remember_me: !!rememberMe,
            redirect: redirect,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                localStorage.setItem(
                    "access_token",
                    response.data.access_token
                );
                window.location.href = response.data.redirect;
            }
        },
        error: function () {
            alert("An error occurred. Please try again.");
        },
    });
});
