$("#resetPasswordForm").on("submit", function (e) {
    e.preventDefault();

    let url = $(this).attr("action");

    let token = $("#token").val().trim();
    let password = $("#password").val().trim();
    let passwordConfirmation = $("#passwordConfirmation").val().trim();

    // Validation
        if (!token) {
            Swal.fire({
                icon: 'warning',
                title: 'Token Required',
                text: 'Token is required.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            return;
        }

        if (!password) {
            Swal.fire({
                icon: 'warning',
                title: 'Password Required',
                text: 'Password is required.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            return;
        }

        if (password.length < 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Password Too Short',
                text: 'Password must be at least 6 characters.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            return;
        }

        if (password !== passwordConfirmation) {
            Swal.fire({
                icon: 'warning',
                title: 'Passwords Mismatch',
                text: 'Passwords do not match.',
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
            token: token,
            password: password,
            password_confirmation: passwordConfirmation,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Password Reset',
                    text: 'Your password has been reset successfully.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = response.data.redirect;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Reset Failed',
                    text: response.message || 'Unable to reset password. Please try again.',
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
