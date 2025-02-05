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
    let route = $(this).attr("action");

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
                window.location.href = response.data.redirect;
            }
        },
        error: function (xhr) {
            formIconContainer.html(submitIcon);
            formStatus.html(
                `<span class="text-danger"><i class="fas fa-exclamation-circle"></i> ${xhr.responseJSON.message}</span>`
            );
            btn.prop("disabled", false);
        },
    });
});
