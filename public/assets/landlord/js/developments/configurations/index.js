let tableID = "#table";
let route = $(tableID).attr("data-route");
let cols = [
    {
        data: "id",
        name: "id",
    },
    {
        data: "configuration_key",
        name: "configuration_key",
    },
    {
        data: "configuration_value",
        name: "configuration_value",
    },
    {
        data: "description",
        name: "description",
    },
    {
        data: "type",
        name: "type",
    },
    {
        data: "is_encrypted",
        name: "is_encrypted",
    },
    {
        data: "is_system",
        name: "is_system",
    },
    {
        data: "is_visible",
        name: "is_visible",
    },
    {
        data: "actions",
        name: "actions",
        orderable: false,
        searchable: false,
    },
];

filterTable({ route: route, tableID: tableID, cols: cols });
