$("#resetPasswordForm").on("submit", function (e) {
    e.preventDefault();

    let url = $(this).attr("action");

    let token = $("#token").val().trim();
    let password = $("#password").val().trim();
    let passwordConfirmation = $("#passwordConfirmation").val().trim();

    // Validation
    if (!token) {
        alert("Token is required.");
        return;
    }

    if (!password) {
        alert("Password is required.");
        return;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters.");
        return;
    }

    if (password !== passwordConfirmation) {
        alert("Passwords do not match.");
        return;
    }

    $.ajax({
        url: url,
        method: "POST",
        data: {
            token: token,
            password: password,
            password_confirmation: passwordConfirmation,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                window.location.href = response.data.redirect;
            }
        },
        error: function () {
            alert("An error occurred. Please try again.");
        },
    });
});
