$(".otp-inputs input").each(function (index) {
    $(this).on("input", function (e) {
        if ($(this).val().length > 1) {
            $(this).val($(this).val().slice(0, 1));
        }
        if ($(this).val().length === 1) {
            if (index < $(".otp-inputs input").length - 1) {
                $(".otp-inputs input")
                    .eq(index + 1)
                    .focus();
            }
        }
    });

    $(this).on("keydown", function (e) {
        if (e.key === "Backspace" && !$(this).val()) {
            if (index > 0) {
                $(".otp-inputs input")
                    .eq(index - 1)
                    .focus();
            }
        }
        if (e.key === "e") {
            e.preventDefault();
        }
    });

    $(this).on("paste", function (e) {
        e.preventDefault(); // Prevent the default paste action
        let pastedData = e.originalEvent.clipboardData.getData("text");

        // Ensure pasted data is a 6-digit number
        if (/^\d{6}$/.test(pastedData)) {
            // Fill all inputs with the pasted 6 digits
            $(".otp-inputs input").each(function (i) {
                $(this).val(pastedData[i]);
                // Move focus to the next input
                if (i < $(".otp-inputs input").length - 1) {
                    $(".otp-inputs input")
                        .eq(i + 1)
                        .focus();
                }
            });
        }
    });
});

$(document).on("submit", "#twoFactorSetupForm", function (e) {
    e.preventDefault();

    let url = $(this).attr("action");
    let btn = $(this).find("button[type='submit']");
    let redirect = $("#redirect").val().trim();
    let secret_key = $(this).find("#secretKey").val();

    let otp = "";
    $(".otp-input").each(function () {
        otp += $(this).val();
    });

    $.ajax({
        type: "POST",
        url: url,
        data: {
            otp: otp,
            secret_key: secret_key,
            redirect: redirect,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        beforeSend: function () {
            btn.prop("disabled", true);
        },
        success: function (response) {
            if (response.success) {
                window.location.href = response.data.redirect;
            }
        },
        error: function (error) {
            console.error(error);
        },
        complete: function () {
            btn.prop("disabled", false);
        },
    });
});
