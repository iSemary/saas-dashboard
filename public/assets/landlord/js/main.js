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

const loadingSpan = `<span><i class="fa fa-spinner fa-spin"></i> ${translate.please_wait}</span>`;

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
    localStorage.removeItem("order_number");

    $("#" + Form).submit();
});

// Select 2
function fireSelect2() {
    $(".select2").select2({});
}
fireSelect2();

// Tooltip
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

/**
 *
 * ===== Create Modal Section
 *
 */

function OpenCreateModal(url) {
    $("#CreateModal").modal({
        backdrop: true,
        show: true,
    });
    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });

    for (i = 0; i < LargeModalStrings.length; i++) {
        if (url.includes(LargeModalStrings[i])) {
            $("#ModalCreateDialog").addClass("modal-lg");
            break;
        } else {
            $("#ModalCreateDialog").removeClass("modal-lg");
        }
    }

    $("#CreateTargetModal")
        .html(LoadingSpan)
        .load(url, function () {
            $("select.select2").select2({});
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]',
            });
        });
}

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
                    translate.processing +
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
            $(".dataTable").DataTable().ajax.reload();
        },
        error: function (data) {
            form.find(".form-status").html("");
            formBtn.prop("disabled", false);
            // $.each(xhr.responseJSON.errors, function(key, value) {
            form.find(".form-status").append(
                `<h6 class="text-danger"><i class="fas fa-exclamation-triangle"></i> ` +
                    (data.responseJSON ?? "Something went wrong") +
                    `</h6>`
            );
            // });
            formBtn.prop("disabled", false);
        },
    });
});

$("#CreateModal").on("hidden.bs.modal", function () {
    $("#CreateTargetModal").html("");
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
                    translate.creating +
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
            $(".dataTable").DataTable().ajax.reload();
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

function OpenEditModal(url) {
    $("#EditModal").modal({
        backdrop: true,
        show: true,
    });
    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });

    for (i = 0; i < LargeModalStrings.length; i++) {
        if (url.includes(LargeModalStrings[i])) {
            $("#ModalEditDialog").addClass("modal-lg");
            break;
        } else {
            $("#ModalEditDialog").removeClass("modal-lg");
        }
    }
    $("#EditTargetModal")
        .html(LoadingSpan)
        .load(url, function () {
            $("select.select2").select2({});
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]',
            });
        });
}

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
                    translate.updating +
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

            $(".dataTable").DataTable().ajax.reload();
        },
        error: function (xhr) {
            form.find(".form-status").html("");
            formBtn.prop("disabled", false);
            $.each(xhr.responseJSON.errors, function (key, value) {
                $(".form-status").append(
                    `<h6 class="text-danger"><i class="fas fa-exclamation-triangle"></i> ` +
                        value[0] +
                        `</h6>`
                );
            });
        },
    });
});

$("#EditModal").on("hidden.bs.modal", function () {
    $("#EditTargetModal").html("");
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
        $modal.attr("data-modal-link", url);
        $modal.find(".modal-title").html(title);
        $modal.find(".modal-body").html("<p>Loading...</p>");
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
                    .html("<p>Failed to load content. Please try again.</p>");
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
    $modal.find(".modal-body").html("<p>Loading...</p>");

    // Make the AJAX request again to refresh the content
    $.ajax({
        url: url,
        method: "GET",
        beforeSend:function() {
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
                .html("<p>Failed to load content. Please try again.</p>");
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
        title: translate.are_you_sure,
        text: translate.you_want_delete_this + " " + ModelDeleteType + " ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: translate.delete,
        cancelButtonText: translate.cancel,
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
                            text: translate.deleted_successfully,
                            confirmButtonText: translate.ok,
                            type: "success",
                            toast: true,
                            position: "bottom",
                        });
                    } else {
                        Swal.fire({
                            text:
                                data.response ?? translate.something_went_wrong,
                            confirmButtonText: translate.ok,
                            type: "error",
                            toast: true,
                            position: "bottom",
                        });
                    }
                    $(".dataTable").DataTable().ajax.reload();
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
        title: translate.are_you_sure,
        text: translate.you_want_restore_this + " " + ModelDeleteType + " ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#FFD43B",
        cancelButtonColor: "#3085d6",
        confirmButtonText: translate.restore,
        cancelButtonText: translate.cancel,
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
                            text: translate.restored_successfully,
                            confirmButtonText: translate.ok,
                            type: "success",
                            toast: true,
                            position: "bottom",
                        });
                    } else {
                        Swal.fire({
                            text:
                                data.response ?? translate.something_went_wrong,
                            confirmButtonText: translate.ok,
                            type: "error",
                            toast: true,
                            position: "bottom",
                        });
                    }
                    $(".dataTable").DataTable().ajax.reload();
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

function filterTable(
    Route,
    TableID,
    from_date,
    to_date,
    init,
    cols,
    colDefs,
    dataExtra
) {
    // init
    if (init) {
        // check if the table is already exists
        if ($.fn.DataTable.isDataTable(TableID)) {
            $(TableID).DataTable().destroy();
        }
    }

    let data = {
        from_date: from_date ?? null,
        to_date: to_date ?? null,
        table: true,
    };
    if (dataExtra) {
        $.extend(data, dataExtra);
    }

    $(TableID).DataTable({
        order: [[0, "desc"]],
        processing: true,
        serverSide: true,
        columnDefs: colDefs ?? null,
        ajax: {
            url: Route,
            data: data,
        },
        columns: cols,
    });
}

$(document).on("submit", "#filterTable", function (e) {
    e.preventDefault();
    filterTable(
        route,
        tableID,
        $("#from_date").val(),
        $("#to_date").val(),
        true,
        cols
    );
});

function fireDependencies() {
    $(".form-toggle").bootstrapToggle();
    $(".select2").select2();
    fireCKEditor();
    $('[data-toggle="tooltip"]').tooltip();
}

fireDependencies();
