headerParams = {
    "Content-Type": "application/x-www-form-urlencoded",
    Authorization: "Bearer " + localStorage.getItem("auth_token"),
};

const csrfToken = $('meta[name="_token"]').attr("content");
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": csrfToken,
    },
});

const loadingSpan = `<span><i class="fa fa-spinner fa-spin"></i> ${t(
    "please_wait"
)}</span>`;

// DataTables Config
$.extend(true, $.fn.dataTable.defaults, {
    language: {
        url: language.dataTableLanguageFile,
    },
    lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "∞"],
    ],
    dom: "Blfrtip",
    // dom: "lpftrip",
    buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5", "print"],
});

// Get URL parameter name
function getURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split("&");
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split("=");
        if (sParameterName[0] == sParam) {
            return decodeURIComponent(sParameterName[1]);
        }
    }
}

// Full Screen Trigger
$(document).keydown(function (event) {
    if (event.which == "122") {
        fullScreen();
    }
});

// Logout Handler
$(".logout-btn").on("click", function (e) {
    e.preventDefault();

    let Form = $(this).data("form");
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user_id");

    $("#" + Form).submit();
});

// Lock Handler
$(".lock-btn").on("click", function (e) {
    e.preventDefault();

    let Form = $(this).data("form");

    $("#" + Form).submit();
});

/**
 *
 * ===== Create Modal Section
 *
 */

$(document).on("submit", ".create-form", function (e) {
    e.preventDefault();
    let formBtn = $(this).find(":submit");
    let formData = new FormData(this);
    let formID = "#" + $(this).attr("id");
    let formUrl = $(this).attr("action");
    let form = $(this);

    $.ajax({
        type: "POST",
        dataType: "json",
        url: formUrl,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            form.find(".form-status").html(
                `<h6 class="text-muted"><i class="fas fa-circle-notch fa-spin"></i> ` +
                    t("processing") +
                    ` ...` +
                    `</h6>`
            );
            formBtn.prop("disabled", true);
        },
        success: function (data) {
            form.find(".form-status").html(
                `<h6 class="text-success"><i class="fas fa-check-circle"></i> ` +
                    data.message +
                    `</h6>`
            );
            formBtn.prop("disabled", false);

            // reload the page if came from the back
            if (data.data.reload) location.reload();

            $("input, textarea", formID)
                .not(
                    ":input[type=button], :input[type=submit], :input[type=hidden], :input[type=reset]"
                )
                .val("");

                refreshDataTable();
        },
        error: function (data) {
            form.find(".form-status").html("");
            formBtn.prop("disabled", false);
            // $.each(xhr.responseJSON.errors, function(key, value) {
            form.find(".form-status").append(
                `<h6 class="text-danger"><i class="fas fa-exclamation-triangle"></i> ` +
                    (data("responseJSON") ?? t("something_went_wrong")) +
                    `</h6>`
            );
            // });
            formBtn.prop("disabled", false);
        },
    });
});

/**
 *
 * ===== Import Excel Script
 *
 */
$(document).on("submit", ".import-excel-form", function (e) {
    e.preventDefault();
    let formBtn = $(this).find(":submit");
    let formData = new FormData(this);
    let formID = "#" + $(this).attr("id");
    let formUrl = $(this).attr("action");
    $.ajax({
        type: "POST",
        dataType: "json",
        url: formUrl,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $(".import-status").html(
                `<h6 class="text-muted"><i class="fas fa-circle-notch fa-spin"></i> ` +
                    t("creating") +
                    `...` +
                    `</h6>`
            );
            formBtn.prop("disabled", true);
        },
        success: function (data) {
            $(".import-status").html(
                `<h6 class="text-success"><i class="fas fa-check-circle"></i> ` +
                    data.message +
                    `</h6>`
            );
            formBtn.prop("disabled", false);

            // reload the page if came from the back
            if (data.data.reload) location.reload();

            $("input, textarea", formID)
                .not(
                    ":input[type=button], :input[type=submit], :input[type=hidden], :input[type=reset]"
                )
                .val("");
            
                refreshDataTable()
        },
        error: function (xhr) {
            $(".import-status").html("");
            formBtn.prop("disabled", false);
            $.each(xhr.responseJSON.errors, function (key, value) {
                $(".import-status").append(
                    `<h6 class="text-danger"><i class="fas fa-exclamation-triangle"></i> ` +
                        value[0] +
                        `</h6>`
                );
            });
            formBtn.prop("disabled", false);
        },
    });
});

/**
 *
 * ===== Edit Modal Section
 *
 */

$(document).on("submit", ".edit-form", function (e) {
    e.preventDefault();
    let formBtn = $(this).find(":submit");
    let formData = new FormData(this);
    let formID = "#" + $(this).attr("id");
    let formUrl = $(this).attr("action");
    let form = $(this);

    $.ajax({
        type: "POST",
        dataType: "json",
        url: formUrl,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            form.find(".form-status").html(
                `<h6 class="text-muted"><i class="fas fa-circle-notch fa-spin"></i> ` +
                    t("updating") +
                    ` ...` +
                    `</h6>`
            );
            formBtn.prop("disabled", true);
        },
        success: function (data) {
            form.find(".form-status").html(
                `<h6 class="text-success"><i class="fas fa-check-circle"></i> ` +
                    data.message +
                    `</h6>`
            );
            formBtn.prop("disabled", false);

            // reload the page if came from the back
            if (data.data.reload) location.reload();

            refreshDataTable();
        },
        error: function (xhr) {
            form.find(".form-status").html("");
            formBtn.prop("disabled", false);
            $.each(xhr.responseJSON.errors, function (key, value) {
                form.find(".form-status").append(
                    `<h6 class="text-danger"><i class="fas fa-exclamation-triangle"></i> ` +
                        (value[0] ?? t("something_went_wrong")) +
                        `</h6>`
                );
            });
        },
    });
});

// Unbind all events for elements within .modal-body
$(document).on("hidden.bs.modal", "#editModal, #createModal", function () {
    $(this).find(".modal-body *").unbind();
    $(this).find(".modal-body *").off();

    // Clear the HTML content
    $(this).find(".modal-body").html("");
});

$(document).on(
    "click",
    ".open-create-modal, .open-edit-modal, .open-details-btn",
    function (e) {
        e.preventDefault();

        // Determine the modal ID based on the button's class
        let modalId;
        if ($(this).hasClass("open-create-modal")) {
            modalId = "createModal";
        } else if ($(this).hasClass("open-edit-modal")) {
            modalId = "editModal";
        } else if ($(this).hasClass("open-details-btn")) {
            modalId = "showModal";
        }

        // Get the data attributes from the button
        const url = $(this).data("modal-link");
        const title = $(this).data("modal-title");

        // Show the modal and set a loading message
        const $modal = $(`#${modalId}`);

        if (language.direction == "rtl") {
            $modal.find(".modal-content").resizable({
                handles: {
                    e: ".resizable-handle",
                },
                start: function (event, ui) {},
                resize: function (event, ui) {
                    if (language.direction == "rtl") {
                        // Maintain the right position while resizing
                        const parentWidth = $(this).parent().width();
                        const newRight =
                            parentWidth - (ui.position.left + ui.size.width);
                        $(this).css({
                            left: "auto",
                            right: newRight,
                        });
                    }
                },
            });
        } else {
            $modal.find(".modal-content").resizable({
                handles: "w",
            });
        }

        $modal.attr("data-modal-link", url);
        $modal.find(".modal-title").html(title);
        $modal.find(".modal-body").html(`<p>${t("loading")}...</p>`);
        $modal.modal("show");

        // Make the AJAX request
        $.ajax({
            url: url,
            method: "GET",
            success: function (response) {
                $modal.find(".modal-body").html(response);
                fireDependencies();
            },
            error: function () {
                $modal
                    .find(".modal-body")
                    .html(
                        `<p>${t(
                            "failed_to_load_content._please_try_again."
                        )}</p>`
                    );
            },
        });
    }
);

$(document).on("click", ".refresh-modal", function (e) {
    e.preventDefault();
    var btn = $(this);
    // Get the modal ID from the parent modal element
    const $modal = $(this).closest(".modal");
    const url = $modal.attr("data-modal-link");

    // Show loading message while fetching the content again
    $modal.find(".modal-body").html(`<p>${t("loading")}...</p>`);

    // Make the AJAX request again to refresh the content
    $.ajax({
        url: url,
        method: "GET",
        beforeSend: function () {
            btn.prop("disabled", true);
        },
        success: function (response) {
            btn.prop("disabled", false);
            $modal.find(".modal-body").html(response);
            fireDependencies();
        },
        error: function () {
            btn.prop("disabled", false);
            $modal
                .find(".modal-body")
                .html(
                    `<p>${t("failed_to_load_content._please_try_again.")}</p>`
                );
        },
    });
});

/**
 *
 * ===== Delete Function Section
 *
 */

function deleteRow(link, ModelDeleteType) {
    Swal.fire({
        title: t("are_you_sure"),
        text: t("you_want_delete_this") + " " + ModelDeleteType + " ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: t("delete"),
        cancelButtonText: t("cancel"),
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: link,
                type: "DELETE",
                data: {
                    _token: csrfToken,
                },
            })
                .done(function (data) {
                    if (data.status == 200) {
                        Swal.fire({
                            text: t("deleted_successfully"),
                            confirmButtonText: t("ok"),
                            type: "success",
                            toast: true,
                            position: "bottom",
                        });
                    } else {
                        Swal.fire({
                            text: data.response ?? t("something_went_wrong"),
                            confirmButtonText: t("ok"),
                            type: "error",
                            toast: true,
                            position: "bottom",
                        });
                    }
                    
                    refreshDataTable();
                })
                .fail(function (data) {
                    Swal.fire({
                        text: data.response,
                        type: "error",
                        toast: true,
                        position: "bottom",
                    });
                });
        }
    });
}
/**
 *
 * ===== Restore Function Section
 *
 */

function restoreRow(link, ModelDeleteType) {
    Swal.fire({
        title: t("are_you_sure"),
        text: t("you_want_restore_this") + " " + ModelDeleteType + " ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#FFD43B",
        cancelButtonColor: "#3085d6",
        confirmButtonText: t("restore"),
        cancelButtonText: t("cancel"),
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: link,
                type: "POST",
                data: {
                    _token: csrfToken,
                },
            })
                .done(function (data) {
                    if (data.status == 200) {
                        Swal.fire({
                            text: t("restored_successfully"),
                            confirmButtonText: t("ok"),
                            type: "success",
                            toast: true,
                            position: "bottom",
                        });
                    } else {
                        Swal.fire({
                            text: data.response ?? t("something_went_wrong"),
                            confirmButtonText: t("ok"),
                            type: "error",
                            toast: true,
                            position: "bottom",
                        });
                    }
                    
                    refreshDataTable();
                })
                .fail(function (data) {
                    Swal.fire({
                        text: data.response,
                        type: "error",
                        toast: true,
                        position: "bottom",
                    });
                });
        }
    });
}

$(document).on("click", ".delete-btn", function (event) {
    event.preventDefault();
    deleteRow($(this).attr("data-url"), $(this).attr("data-delete-type"));
});

$(document).on("click", ".restore-btn", function (event) {
    event.preventDefault();
    restoreRow($(this).attr("data-url"), $(this).attr("data-restore-type"));
});

function filterTable({
    route,
    tableID = "#table",
    fromDate = null,
    toDate = null,
    init = true,
    cols = [],
    colDefs = null,
    dataExtra = null,
    orderColumnIndex = 0,
    orderColumnType = "desc",
    selectable = false,
}) {
    // init
    if (init) {
        // check if the table already exists
        if ($.fn.DataTable.isDataTable(tableID)) {
            $(tableID).DataTable().destroy();
        }
    }
    
    let urlParams = new URLSearchParams(window.location.search);
    let data = {
        from_date: urlParams.get('from_date') ?? fromDate ?? null,
        to_date: urlParams.get('to_date') ?? toDate ?? null,
        table: true,
    };
    
    // Convert all URL parameters into the data object
    urlParams.forEach((value, key) => {
        if (!data.hasOwnProperty(key)) {
            data[key] = value;
        }
    });
    
    console.log(data);
    

    if (dataExtra) {
        $.extend(data, dataExtra);
    }

    let dataTableConfig = {
        order: [[orderColumnIndex, orderColumnType]],
        processing: true,
        serverSide: true,
        autoWidth: false,
        columnDefs: colDefs ?? [],
        ajax: {
            url: route,
            data: data,
        },
        columns: cols,
        createdRow: function (row, data, dataIndex) {
            // Set the data-id attribute for the row
            if (data.id) {
                $(row).attr("data-id", data.id);
            }
        },
    };

    // Add select configuration if selectable is true
    if (selectable) {
        dataTableConfig.select = {
            style: "multi",
            selector: "td:not(:last-child)", // Exclude the last column (e.g., actions column)
        };
    }

    // Initialize the DataTable
    var dataTableCoreTable = $(tableID).DataTable(dataTableConfig);

    // Select all rows when the header checkbox is clicked
    $("#selectAllRows").on("click", function () {
        var rows = dataTableCoreTable.rows({ search: "applied" }).nodes();
        $('input[type="checkbox"]', rows).prop("checked", this.checked);
        updateButtonState(); // Update button state when "Select All" is clicked
    });

    // Handle row selection
    $(tableID).on("change", ".select-row", function () {
        if (!this.checked) {
            var el = $("#selectAllRows").get(0);
            if (el && el.checked && "indeterminate" in el) {
                el.indeterminate = true;
            }
        }
        updateButtonState(); // Update button state when a row is selected/deselected
    });

    // Make the entire row selectable, except the last <td>
    $(tableID).on("click", "tbody tr", function (event) {
        // Check if the click was on the checkbox itself to avoid double toggling
        if ($(event.target).is('input[type="checkbox"]')) {
            return;
        }

        // Check if the click was on the last <td> in the row
        if ($(event.target).closest("td").is(":last-child")) {
            return; // Skip toggling if the last <td> was clicked
        }

        // Toggle the checkbox
        var checkbox = $(this).find(".select-row");
        checkbox.prop("checked", !checkbox.prop("checked")).trigger("change");
    });

    // Function to update the button state
    function updateButtonState() {
        // Count the number of selected rows
        let selectedCount = $(".select-row:checked").length;

        // Enable/disable buttons with data-button-listen="select-row"
        $('button[data-button-listen="select-row"]').prop(
            "disabled",
            selectedCount < 2
        );
    }

    // Initial button state update
    updateButtonState();
}

$(document).on("submit", ".filter-table", function (e) {
    e.preventDefault();

    var tableID = "#" + $(this).closest(".card").find("table").attr("id");
    var route = $(tableID).attr("data-route");
    var fromDate = $(this).find(".filter-table-from-date").val();
    var toDate = $(this).find(".filter-table-to-date").val();
    var cols = $(tableID).DataTable().settings()[0].aoColumns;

    var dt = $(tableID).DataTable();
    var dtSettings = dt.settings()[0];

    // Get column definitions
    var colDefs = dtSettings.aoColumnDefs;

    // Get order configuration
    var order = dtSettings.aaSorting[0] || [0, "desc"];
    var orderColumnIndex = order[0];
    var orderColumnType = order[1];

    // Get selectable status
    var selectable =
        $(tableID).attr("data-selectable") == "true" ? true : false;

    filterTable({
        route: route,
        tableID: tableID,
        fromDate: fromDate,
        toDate: toDate,
        cols: cols,
        colDefs: colDefs,
        orderColumnIndex: orderColumnIndex,
        orderColumnType: orderColumnType,
        selectable: selectable,
    });
});

// fire important dependencies
function fireDependencies() {
    setTimeout(() => {
        document.querySelectorAll(".select2").forEach(function (element) {
            $(element).select2();
        });
        document.querySelectorAll(".form-toggle").forEach(function (element) {
            $(element).bootstrapToggle();
        });
        document.querySelectorAll(".file-uploader").forEach(function (element) {
            $(element).fileUpload();
        });
        document.querySelectorAll(".emoji-input").forEach(function (element) {
            $(element).emojioneArea({
                pickerPosition: "bottom",
                searchPosition: "bottom",
            });
        });
        document
            .querySelectorAll('[data-toggle="tooltip"]')
            .forEach(function (element) {
                $(element).tooltip({
                    trigger: "hover",
                });
            });
        fireCKEditor();
        $('[data-toggle="tooltip"]').tooltip();
        // Force select2 search to work inside modals
        $.fn.modal.Constructor.prototype._enforceFocus = function () {};
    }, 0);
}

fireDependencies();

$(document).on("click", ".copy-to-clipboard-btn", function (e) {
    e.preventDefault();
    var content = $(this).data("content");

    // Create temporary textarea to copy from
    var tempTextArea = document.createElement("textarea");
    tempTextArea.value = content;
    document.body.appendChild(tempTextArea);

    // Select and copy the text
    tempTextArea.select();
    document.execCommand("copy");

    // Remove the temporary textarea
    document.body.removeChild(tempTextArea);

    // Show success message using SweetAlert
    Swal.fire({
        type: "success",
        title: t("copied_to_clipboard"),
        position: "bottom",
        toast: true,
        showConfirmButton: false,
        timer: 1500,
    });
});


function refreshDataTable()
{
    if ($(".dataTable").length > 0) {
        $(".dataTable").each(function () {
            if (!$(this).attr("data-disable-refresh")) {
                var dataTable = $(this).DataTable();
                if (dataTable?.settings()[0]?.ajax) {
                    dataTable.ajax.reload();
                }
            }
        });
    }
}