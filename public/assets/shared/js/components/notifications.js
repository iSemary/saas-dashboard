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
    if (notificationsList.find(".notification-item").length == 0) {
        loadNotifications(notificationsList);
    }
});

function loadNotifications(notificationListElement) {
    $.ajax({
        url: route("notifications.list"),
        type: "GET",
        beforeSend: function () {
            // Loader in the panel
            notificationListElement.html(
                "<div class='text-center'><i class='fas fa-spinner fa-spin'></i></div>"
            );
        },
        success: function (response) {
            renderNotifications(notificationListElement, response);
        },
        error: function (xhr) {
            console.log(xhr);
        },
    });
}

function renderNotifications(notificationListElement, response) {
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
                ${translate("no_notifications_yet")}
            </div>
        `);
    } else {
        // Loop through and append each notification
        notifications.forEach((notification) => {
            const notificationHtml = `
                <div class="notification-item dropdown-item ${
                    notification.seen_at
                        ? "notification-seen"
                        : "notification-unseen"
                }"
                     data-seen-at="${
                         notification.seen_at
                     }" data-stopPropagation="true">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <img src="${
                                notification.icon
                            }" class="notification-icon" alt="notifications icon" />
                        </div>
                        <div class="w-100">
                            <a href="${
                                notification.route
                            }" class="notification-link text-decoration-none text-dark">
                                <span class="font-weight-bold">${
                                    notification.name
                                }</span>
                                <p class="my-0 break-spaces text-muted f-13">${
                                    notification.description
                                }</p>
                            </a>
                            <div class="row">
                                <div class="col-10">
                                    <small class="text-muted text-right" title="${
                                        notification.created_at
                                    }">${notification.created_at_diff}</small>
                                </div>
                                <div class="col-2">
                                    <div class="btn-group dropdown options-dropdown">
                                        <button type="button" class="btn btn-xs btn-transparent notification-menu-btn"
                                                data-toggle="dropdown" data-boundary="window">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right notification-options-menu">
                                            <button class="dropdown-item" type="button"
                                                    onclick="markAsRead(${
                                                        notification.id
                                                    })">
                                                <i class="fas fa-check mr-2"></i> Mark as read
                                            </button>
                                            <button class="dropdown-item text-danger delete-notification" type="button"
                                                    onclick="deleteNotification(${
                                                        notification.id
                                                    })">
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
            notificationListElement
                .parent("div")
                .find(".load-more-btn")
                .remove(); // Remove existing button if any
            notificationListElement.parent("div").append(`
                <button class="dropdown-item text-center text-primary load-more-btn" 
                        data-stopPropagation="true" onclick="loadMoreNotifications(${
                            currentPage + 1
                        })">
                    Load More
                </button>
            `);
        } else {
            // Remove "Load More" button if no more pages are available
            notificationListElement
                .parent("div")
                .find(".load-more-btn")
                .remove();
        }

        // Add "View All" link
        notificationListElement.parent("div").find(".view-all-link").remove(); // Remove existing link if any
        notificationListElement.parent("div").append(`
            <a href="${route(
                "notifications.index"
            )}" class="dropdown-item text-center text-primary view-all-link"
               data-stopPropagation="true">
                ${translate("view_all")}
            </a>
        `);
    }
}

function loadMoreNotifications(page) {
    const notificationsList = $(".notifications-list");

    $.ajax({
        url: route("notifications.list"),
        type: "GET",
        data: {
            page: page,
        },
        beforeSend: function () {
            // Add loading indicator to the "Load More" button
            $(".load-more-btn")
                .html('<i class="fas fa-spinner fa-spin mr-2"></i>Loading...')
                .prop("disabled", true);
        },
        success: function (response) {
            // Remove loading state from button
            $(".load-more-btn").remove();

            // Render the new notifications
            renderNotifications(notificationsList, response);
        },
        error: function (xhr) {
            console.log(xhr);
            // Reset button state on error
            $(".load-more-btn").html("Load More").prop("disabled", false);
        },
    });
}
