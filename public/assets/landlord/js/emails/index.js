var tableID = "#table";
var route = $(tableID).attr("data-route");
var selectable = $(tableID).attr("data-selectable") == "true" ? true : false;
var cols = [
    {
        data: null,
        render: function (data, type, row) {
            return '<div class="text-center"><input type="checkbox" class="select-row" name="select_row" /></div>';
        },
        orderable: false,
        searchable: false,
    },
    {
        data: "id",
        name: "id",
    },
    {
        data: "template_name",
        name: "template_name",
    },
    {
        data: "email",
        name: "email",
    },
    {
        data: "status",
        name: "status",
    },
    {
        data: "created_at",
        name: "created_at",
    },
    {
        data: "actions",
        name: "actions",
        orderable: false,
        searchable: false,
    },
];

filterTable({
    route: route,
    tableID: tableID,
    cols: cols,
    selectable: selectable,
    orderColumnIndex: 1,
    orderColumnType: "desc",
});


$(document).on("click", ".resend-email-btn", function (e) {
    e.preventDefault();
    let url = $(this).data("route");
    let btn = $(this);

    btn.prop("disabled", true);

    $.ajax({
        type: "POST",
        url: url,
        dataType: "json",
        success: function (response) {
            btn.prop("disabled", false);
            Swal.fire({
                text: t("email_resent_successfully"),
                confirmButtonText: t("ok"),
                type: "success",
                toast: true,
                position: "bottom",
            });
        },
        error: function (xhr) {
            btn.prop("disabled", false);
            Swal.fire({
                text: xhr.responseJSON?.message ?? t("something_went_wrong"),
                confirmButtonText: t("ok"),
                type: "error",
                toast: true,
                position: "bottom",
            });
        },
    });
});

$(document).on("click", ".resend-multiple-emails", function (e) {
    let url = $(this).data("route");
    let btn = $(this);

    // Initialize an array to store selected IDs
    let selectedIds = [];

    // Loop through all checked checkboxes with the class .select-row
    $(".select-row:checked").each(function () {
        // Get the row ID from the closest row (assuming the ID is stored in a data-id attribute)
        let rowId = $(this).closest("tr").attr("data-id");
        if (rowId) {
            selectedIds.push(rowId); // Add the ID to the array
        }
    });

    // Join the IDs into a comma-separated string
    let selectedIdsString = selectedIds.join(",");

    btn.prop("disabled", true);

    $.ajax({
        type: "POST",
        url: url,
        data: {
            ids: selectedIdsString,
        },
        dataType: "json",
        success: function (response) {
            btn.prop("disabled", false);
            Swal.fire({
                text: t("email_resent_successfully"),
                confirmButtonText: t("ok"),
                type: "success",
                toast: true,
                position: "bottom",
            });
        },
        error: function (xhr) {
            btn.prop("disabled", false);
            Swal.fire({
                text: xhr.responseJSON?.message ?? t("something_went_wrong"),
                confirmButtonText: t("ok"),
                type: "error",
                toast: true,
                position: "bottom",
            });
        },
    });
});
