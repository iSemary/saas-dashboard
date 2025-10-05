$("#organizationForm").on("submit", function (e) {
    e.preventDefault();

    const organizationName = $("#organizationName").val();
    let url = $(this).attr("action");
        if (!organizationName) {
            Swal.fire({
                icon: 'warning',
                title: 'Organization Required',
                text: 'Organization name is required',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Organization Error',
                        text: response.message || "Invalid organization name.",
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'An error occurred. Please try again.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
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
        Swal.fire({
            icon: 'warning',
            title: 'Validation Error',
            text: 'Organization name is required.',
            toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
        });
        return;
    }
    if (!email) {
        Swal.fire({
            icon: 'warning',
            title: 'Validation Error',
            text: 'Email is required.',
            toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
        });
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
                Swal.fire({
                    icon: 'success',
                    title: 'Password Reset Email Sent',
                    text: 'Please check your email for password reset instructions.',
                    showConfirmButton: true,
                    toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
                }).then(() => {
                    if (response.data.redirect) {
                        window.location.href = response.data.redirect;
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Send Failed',
                    text: response.message || 'Unable to send reset email. Please try again.',
                    toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'An error occurred. Please try again.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        },
    });
});
