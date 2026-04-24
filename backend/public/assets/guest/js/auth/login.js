// Ensure SweetAlert2 is loaded and ready
function checkSweetAlertAndExecute(callback) {
    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
        callback();
    } else {
        // Wait a bit and try again
        setTimeout(function() {
            checkSweetAlertAndExecute(callback);
        }, 50);
    }
}

function showToastNotification(icon, title, text, duration = 3000) {
    checkSweetAlertAndExecute(function() {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: duration,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    });
}

function showSuccessToastWithRedirect(title, text, redirectUrl) {
    checkSweetAlertAndExecute(function() {
        Swal.fire({
            icon: 'success',
            title: title,
            text: text,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        }).then(() => {
            window.location.href = redirectUrl;
        });
    });
}

$("#organizationForm").on("submit", function (e) {
    e.preventDefault();

    const organizationName = $("#organizationName").val();
    let url = $(this).attr("action");
    if (!organizationName) {
        showToastNotification('warning', 'Organization Required', 'Please enter your organization name');
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
                    // Hide organization form and show login form
                    $("#organizationForm").hide();
                    $("#loginOrganizationName").val(organizationName);
                    $("#loginForm").show();
                    // Don't redirect here - let the login form handle the actual login
                } else {
                    showToastNotification('error', 'Organization Error', response.message || "Invalid organization name.");
                }
        },
        error: function (xhr, status, error) {
            let errorMessage = 'Organization check failed. Please try again.';
            
            try {
                const response = JSON.parse(xhr.responseText);
                if (response && response.message) {
                    errorMessage = response.message;
                } else if (response && response.error) {
                    errorMessage = response.error;
                }
            } catch (e) {
                // If JSON parsing fails, use the status text or default message
                errorMessage = xhr.statusText || 'Organization check failed';
            }
            
            // Show specific error message from the server
            showToastNotification('error', 'Network Error', errorMessage);
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
            showToastNotification('warning', 'Required Field', 'Organization name is required');
            return;
        }
        if (!username) {
            showToastNotification('warning', 'Required Field', 'Username or email is required');
            return;
        }
        if (!password) {
            showToastNotification('warning', 'Required Field', 'Password is required');
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
                    // Show success message with redirect
                    showSuccessToastWithRedirect('Login Successful', 'Welcome back! Redirecting...', response.data.redirect);
            } else {
                showToastNotification('error', 'Login Failed', response.message || 'Invalid credentials. Please try again.');
            }
        },
         error: function (xhr, status, error) {
            let errorMessage = 'Invalid credentials';
            
            // Log for debugging
            console.log('AJAX Error:', {xhr, status, error, responseText: xhr.responseText});
            
            try {
                const response = JSON.parse(xhr.responseText);
                console.log('Parsed response:', response);
                
                if (response && response.message) {
                    errorMessage = response.message;
                } else if (response && response.error) {
                    errorMessage = response.error;
                }
            } catch (e) {
                console.log('JSON Parse Error:', e);
                console.log('Raw response:', xhr.responseText);
                // If JSON parsing fails, use the status text or default message
                errorMessage = xhr.statusText || 'Invalid credentials';
            }
            
            console.log('Final error message:', errorMessage);
            
            // Check if SweetAlert2 is available
            showToastNotification('error', 'Login Failed', errorMessage, 4000);
        },
    });
});
