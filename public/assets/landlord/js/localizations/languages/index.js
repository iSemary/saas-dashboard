let tableID = "#table";
let route = $(tableID).attr("data-route");
let cols = [
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
        data: "name",
        name: "name",
    },
    {
        data: "locale",
        name: "locale",
    },
    {
        data: "direction",
        name: "direction",
    },
    {
        data: "total_translations",
        name: "total_translations",
        searchable: false,
    },
    {
        data: "actions",
        name: "actions",
        orderable: false,
        searchable: false,
    },
];

filterTable(
    route,
    tableID,
    null,
    null,
    true,
    cols,
    null,
    null,
    1,
    "desc",
    true
);

$(document).on("click", ".check-selected", function (e) {
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

    // Log the result
    console.log("Rows IDs selected is: ", selectedIdsString);
});
