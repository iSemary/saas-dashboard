var translate = {};
$.getJSON(language.languageFile, function(data) {
    translate = data;
}).fail(function(xhr, textStatus, error) {
    console.error('Error loading translation file:', error);
});

$(document).on("change", "#LangSelect", function (e) {
    let Locale = $(this).val();
    let FullLocale = $("#LangSelect option:selected").text();

    let SelectedLangForm = `
        <div class="form-group">
            <label class="text-capitalize">${FullLocale}</label>
            <span class="text-danger">&nbsp;&nbsp;*</span>
            <span class="mx-3 text-danger cursor-pointer remove-locale" data-locale="${Locale}"><i class="fas fa-times-circle"></i></span>
            <textarea type="text" class="form-control text-capitalize" name="locales[${Locale}]" minlength="1" maxlength="500" placeholder="${FullLocale}" required></textarea>
        </div>
`;

    $("#Translations").append(SelectedLangForm);
    $("#LangSelect option:selected").attr("disabled", true);
});

$(document).on("click", ".remove-locale", function (e) {
    let RemovedLocale = $(this).attr("data-locale");
    $("#LangSelect option[value='" + RemovedLocale + "']").attr(
        "disabled",
        false
    );
    $(this).parent().remove();
});

$(document).on("input", ".slug-input", function () {
    var inputValue = $(this).val();
    inputValue = inputValue.replace(/\s+/g, "-");
    inputValue = inputValue.replace(/[٠-٩]/g, function (digit) {
        return String.fromCharCode(digit.charCodeAt(0) - 1632 + 48);
    });
    inputValue = inputValue.replace(/[^a-zA-Z0-9\-]/g, "");
    $(this).val(inputValue);
});

$(document).on("input", ".snake-input", function () {
    let inputValue = $(this).val();

    // Replace spaces with underscores
    inputValue = inputValue.replace(/\s+/g, "_");

    // Convert Arabic numerals to English numerals
    inputValue = inputValue.replace(/[\u0660-\u0669]/g, function (digit) {
        return String.fromCharCode(digit.charCodeAt(0) - 0x0660 + 48);
    });

    // Remove any characters that are not alphanumeric, hyphens, or underscores
    inputValue = inputValue.replace(/[^a-zA-Z0-9\-_.]/g, "");

    // Update the input value
    $(this).val(inputValue);
});

$(document).on("input", ".decimal-input", function (e) {
    // Remove any non-numeric characters except decimal point
    let value = $(this)
        .val()
        .replace(/[^0-9.]/g, "");

    // Ensure only one decimal point
    let decimalCount = (value.match(/\./g) || []).length;
    if (decimalCount > 1) {
        value = value.replace(/\.(?=.*\.)/g, "");
    }

    // Limit to 5 decimal places
    if (value.includes(".")) {
        let parts = value.split(".");
        if (parts[1].length > 5) {
            parts[1] = parts[1].substring(0, 5);
            value = parts.join(".");
        }
    }

    // Update input value
    $(this).val(value);
});

$(document).on("blur", ".decimal-input", function (e) {
    let value = $(this).val();
    if (value !== "") {
        $(this).val(parseFloat(value).toFixed(5));
    }
});

/**
 * Initializes the CKEditor on the element with the ID "ckInput" if it exists.
 * Configures the toolbar with various groups and items.
 * Logs a warning to the console if the element is not found.
 */
function fireCKEditor() {
    // Check if the element exists
    if (document.getElementById("ckInput")) {
        CKEDITOR.replace("ckInput", {
            toolbar: [
                {
                    name: "document",
                    groups: ["mode", "document", "doctools"],
                    items: [
                        "Source",
                        "-",
                        "Save",
                        "NewPage",
                        "Preview",
                        "-",
                        "Templates",
                    ],
                },
                {
                    name: "clipboard",
                    groups: ["undo"],
                    items: ["Cut", "Copy", "Paste", "-", "Undo", "Redo"],
                },
                {
                    name: "editing",
                    groups: ["find", "selection"],
                    items: ["Find", "-", "SelectAll", "-", "Scayt"],
                },
                { name: "forms", items: [] },
                "/",
                {
                    name: "basicstyles",
                    groups: ["basicstyles"],
                    items: [
                        "Bold",
                        "Italic",
                        "Underline",
                        "Strike",
                        "Subscript",
                        "Superscript",
                        "-",
                    ],
                },
                {
                    name: "paragraph",
                    groups: ["list", "indent", "blocks", "align", "bidi"],
                    items: [
                        "NumberedList",
                        "BulletedList",
                        "-",
                        "Outdent",
                        "Indent",
                        "-",
                        "Blockquote",
                        "CreateDiv",
                        "-",
                        "JustifyLeft",
                        "JustifyCenter",
                        "JustifyRight",
                        "JustifyBlock",
                        "-",
                        "BidiLtr",
                        "BidiRtl",
                        "Language",
                    ],
                },
                { name: "links", items: [] },
                {
                    name: "insert",
                    items: ["Table", "HorizontalRule", "SpecialChar"],
                },
                "/",
                {
                    name: "styles",
                    items: ["Styles", "Format", "Font", "FontSize"],
                },
                { name: "colors", items: ["TextColor", "BGColor"] },
                { name: "tools", items: ["Maximize"] },
                { name: "others", items: ["-"] },
                { name: "about", items: [] },
            ],
        });
    } else {
        console.warn("CKEditor element not found");
    }
}

/**
 * Image Modal Previewer
 */
$(document).on("click", ".view-image", function () {
    const imgSrc = $(this).attr("src");
    $("#modalImage").attr("src", imgSrc);
    $("#imageModal").modal("show");
});

$(document).on("click", "#modalImage", function () {
    $(this).toggleClass("zoomed");
});

// Password generator
$(document).on("input", ".generate-password-input", function () {
    const password = $(this);
    const progressBar = $(".progress-bar");
    const requirements = {
        length: /.{8,}/,
        lowercase: /[a-z]/,
        uppercase: /[A-Z]/,
        number: /[0-9]/,
        special: /[^A-Za-z0-9]/,
    };

    const value = password.val();
    let strength = 0;

    // Check each requirement
    Object.keys(requirements).forEach((req) => {
        const li = $(`.requirement-list .${req}`);
        const isValid = requirements[req].test(value);

        // Update requirement status
        li.find("i")
            .removeClass("fa-hourglass-end fa-check fa-times")
            .addClass(isValid ? "fa-check valid" : "fa-times invalid");

        if (isValid) {
            strength += 20;
            li.find("i").removeClass("invalid");
        }
    });

    // Update progress bar
    progressBar
        .css("width", `${strength}%`)
        .removeClass("bg-danger bg-warning bg-success")
        .addClass(
            strength <= 40
                ? "bg-danger"
                : strength <= 80
                ? "bg-warning"
                : "bg-success"
        );
});

// Email availability checker
// Debounce function to limit API calls
function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

$(document).on("input", ".email-checker", function () {
    let input = $(this);
    let formGroup = input.closest(".form-group");
    let loaderImage = formGroup.data("loader-image");
    let loadingHtml =
        '<div class="loading-spinner position-absolute" style="right: 10px; top: 38px;"><img src="' +
        loaderImage +
        '" width="20" height="20"></div>';

    // Remove previous feedback and add loading spinner
    formGroup.find(".invalid-feedback").remove();
    input.removeClass("is-invalid is-valid");
    formGroup.find(".loading-spinner").remove();
    formGroup.append(loadingHtml);

    // Call the debounced email checker
    debouncedEmailCheck(input, formGroup);
});

const debouncedEmailCheck = debounce(function (input, formGroup) {
    let email = input.val();
    let userId = formGroup.data("id");
    let checkRoute = formGroup.data("email-check-route");
    let invalidEmailFormatMessage = formGroup.data(
        "invalid-email-format-message"
    );

    // If email is empty, remove loading and return
    if (!email) {
        formGroup.find(".loading-spinner").remove();
        return;
    }

    // Check if email has valid format
    if (isValidEmail(email)) {
        $.ajax({
            url: checkRoute,
            type: "POST",
            data: {
                email: email,
                user_id: userId,
                _token: $('meta[name="_token"]').attr("content"),
            },
            success: function (response) {
                formGroup.find(".loading-spinner").remove();
                if (!response.success) {
                    input.addClass("is-invalid");
                    formGroup.append(
                        '<div class="invalid-feedback">' +
                            response.message +
                            "</div>"
                    );
                } else {
                    input.addClass("is-valid");
                }
            },
            error: function (xhr) {
                formGroup.find(".loading-spinner").remove();
                let response = xhr.responseJSON;
                if (!response.success) {
                    input.addClass("is-invalid");
                    formGroup.append(
                        '<div class="invalid-feedback">' +
                            response.message +
                            "</div>"
                    );
                } else {
                    input.addClass("is-valid");
                }
            },
        });
    } else {
        formGroup.find(".loading-spinner").remove();
        input.addClass("is-invalid");
        formGroup.append(
            '<div class="invalid-feedback">' +
                invalidEmailFormatMessage +
                "</div>"
        );
    }
}, 2000); // 2 seconds delay

// Email validation helper function
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Image previewer
$(document).on("change", ".upload-image", function (e) {
    var input = $(this);
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            input
                .parents(".form-group")
                .find(".preview-image")
                .attr("src", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    }
});

// Notifications Drop Down
document.addEventListener("DOMContentLoaded", function () {
    const notificationsList = document.querySelector(".notifications-list");
    if (notificationsList) {
        OverlayScrollbars(notificationsList, {
            className: "os-theme-dark",
            scrollbars: {
                visibility: "auto",
                autoHide: "leave",
                autoHideDelay: 800,
            },
            overflowBehavior: {
                x: "hidden",
                y: "scroll",
            },
        });
    }
});

// Prevent dropdown from closing when clicking inside
$(".notifications-menu").on("click", function (e) {
    e.stopPropagation();
});

// Alternative approach using data attribute
$(document).on("click", '[data-stopPropagation="true"]', function (e) {
    e.stopPropagation();
});

// Prevent parent dropdown from closing when clicking inside
$(".notifications-menu").on("click", function (e) {
    e.stopPropagation();
});

// Handle options dropdown toggle
$(".notification-menu-btn").on("click", function (e) {
    e.stopPropagation(); // Prevent click from propagating
    // Close other option dropdowns first
    $(".options-dropdown .dropdown-menu")
        .not($(this).siblings(".dropdown-menu"))
        .hide();
});

// Close options dropdown when clicking outside of it
$(document).on("click", function (e) {
    if (!$(e.target).closest(".options-dropdown").length) {
        $(".options-dropdown .dropdown-menu").hide();
    }
});

// Prevent dropdown menu items from closing parent dropdown
$(".notification-options-menu .dropdown-item").on("click", function (e) {
    e.stopPropagation();
});


/**
 * 
 * Notifications Loader
 * 
 */

$("#notificationsDropdown").on("click", function () {
    let notificationsList = $(".notifications-list");
    if (notificationsList.find('.notification-item').length == 0) {
        let notificationsListRoute = $(this).data("notifications-list-route");
        let notificationsRoute = $(this).data("notifications-route");
        let noNotificationsMessage = $(this).data("no-notifications-message");
        let notificationsViewAllMessage = $(this).data("notifications-view-all-message");
        loadNotifications(notificationsListRoute, notificationsList, noNotificationsMessage, notificationsRoute, notificationsViewAllMessage);
    }
});

function loadNotifications(route, notificationListElement, noNotificationsMessage, notificationsRoute, notificationsViewAllMessage) {
    $.ajax({
        url: route,
        type: "GET",
        beforeSend: function () {
            // Loader in the panel
            notificationListElement.html("<div class='text-center'><i class='fas fa-spinner fa-spin'></i></div>");
        },
        success: function (response) {
            renderNotifications(notificationListElement,noNotificationsMessage, notificationsRoute, notificationsViewAllMessage, response);
        },
        error: function (xhr) {
            console.log(xhr);
        },
    });
}


function renderNotifications(
    notificationListElement, 
    noNotificationsMessage, 
    notificationsRoute, 
    notificationsViewAllMessage, 
    response
) {
    let notifications = response.data.data.data;
    let currentPage = response.data.data.current_page;
    let lastPage = response.data.data.last_page;

    // Clear the notification list if it's the first page
    if (currentPage === 1) {
        notificationListElement.empty();
    }

    // If there are no notifications and it's the first page
    if (notifications.length === 0 && currentPage === 1) {
        notificationListElement.html(`
            <div class="dropdown-item text-center text-muted" data-stopPropagation="true">
                ${noNotificationsMessage}
            </div>
        `);
    } else {
        // Loop through and append each notification
        notifications.forEach(notification => {
            const notificationHtml = `
                <div class="notification-item dropdown-item ${notification.seen_at ? 'notification-seen' : 'notification-unseen'}"
                     data-seen-at="${notification.seen_at}" data-stopPropagation="true">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <img src="${notification.icon}" class="notification-icon" alt="notifications icon" />
                        </div>
                        <div class="w-100">
                            <a href="${notification.route}" class="notification-link text-decoration-none text-dark">
                                <span class="font-weight-bold">${notification.name}</span>
                                <p class="my-0 break-spaces text-muted f-13">${notification.description}</p>
                            </a>
                            <div class="row">
                                <div class="col-10">
                                    <small class="text-muted text-right">${notification.created_at}</small>
                                </div>
                                <div class="col-2">
                                    <div class="btn-group dropdown options-dropdown">
                                        <button type="button" class="btn btn-xs btn-transparent notification-menu-btn"
                                                data-toggle="dropdown" data-boundary="window">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right notification-options-menu">
                                            <button class="dropdown-item" type="button"
                                                    onclick="markAsRead(${notification.id})">
                                                <i class="fas fa-check mr-2"></i> Mark as read
                                            </button>
                                            <button class="dropdown-item text-danger delete-notification" type="button"
                                                    onclick="deleteNotification(${notification.id})">
                                                <i class="fas fa-trash-alt mr-2"></i> Delete this notification
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
            `;
            notificationListElement.append(notificationHtml);
        });

        // Add "Load More" button if more pages are available
        if (currentPage < lastPage) {
            notificationListElement.parent('div').find('.load-more-btn').remove(); // Remove existing button if any
            notificationListElement.parent('div').append(`
                <button class="dropdown-item text-center text-primary load-more-btn" 
                        data-stopPropagation="true" onclick="loadMoreNotifications(${currentPage + 1})">
                    Load More
                </button>
            `);
        } else {
            // Remove "Load More" button if no more pages are available
            notificationListElement.parent('div').find('.load-more-btn').remove();
        }

        // Add "View All" link
        notificationListElement.parent('div').find('.view-all-link').remove(); // Remove existing link if any
        notificationListElement.parent('div').append(`
            <a href="${notificationsRoute}" class="dropdown-item text-center text-primary view-all-link"
               data-stopPropagation="true">
                ${notificationsViewAllMessage}
            </a>
        `);
    }
}

