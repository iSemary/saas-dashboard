let tableID = "#table";
let tableRoute = $(tableID).attr("data-route");
let cols = [
    {
        data: "id",
        name: "id",
    },
    {
        data: "name",
        name: "name",
    },
    {
        data: "code",
        name: "code",
    },
    {
        data: "type",
        name: "type",
    },
    {
        data: "base_conversion",
        name: "base_conversion",
    },
    {
        data: "description",
        name: "description",
    },
    {
        data: "is_base_unit",
        name: "is_base_unit",
    },
    {
        data: "actions",
        name: "actions",
        orderable: false,
        searchable: false,
    },
];

filterTable({ route: tableRoute, tableID: tableID, cols: cols });
