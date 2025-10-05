document.addEventListener("DOMContentLoaded", function () {
    const audio = document.getElementById("bgMusic");
    const audioControl = document.getElementById("audioControl");
    const audioIcon = audioControl.querySelector("i");

    // Try to play audio automatically
    audio.play().catch(function (error) {
        console.log("Audio autoplay failed:", error);
    });

    // Toggle audio play/pause
    audioControl.addEventListener("click", function () {
        if (audio.paused) {
            audio.play();
            audioIcon.className = "fas fa-volume-up";
        } else {
            audio.pause();
            audioIcon.className = "fas fa-volume-mute";
        }
    });
});

$(document).on("submit", "#unlockForm", function (e) {
    e.preventDefault();

    let method = $(this).attr("method");
    let tableRoute = $(this).attr("action");

    let password = $(this).find("#password").val();
    let formStatus = $(".form-status");
    let formIconContainer = $(this).find(".icon-container");

    let btn = $(this).find("submit-btn");

    let loadingIcon = $(".loading-icon");
    let submitIcon = $(".submit-icon");

    $.ajax({
        type: method,
        url: route,
        data: {
            password: password,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        beforeSend: function () {
            formIconContainer.html(loadingIcon);
            btn.prop("disabled", true);
        },
        success: function (response) {
            formIconContainer.html(submitIcon);
            if (response.data.redirect) {
                Swal.fire({
                    icon: 'success',
                    title: 'Session Unlocked',
                    text: 'Welcome back! Redirecting...',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = response.data.redirect;
                });
            }
        },
        error: function (xhr) {
            formIconContainer.html(submitIcon);
            btn.prop("disabled", false);
            
            Swal.fire({
                icon: 'error',
                title: 'Authentication Failed',
                text: xhr.responseJSON?.message || 'Invalid password. Please try again.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        },
    });
});
