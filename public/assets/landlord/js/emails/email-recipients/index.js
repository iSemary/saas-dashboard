let tableID = "#table";
let tableRoute = $(tableID).attr("data-route");
let cols = [
    {
        data: "id",
        name: "id",
    },
    {
        data: "email",
        name: "email",
    },
    {
        data: "name",
        name: "name",
    },
    {
        data: "total_metadata",
        name: "total_metadata",
    },
    {
        data: "groups",
        name: "groups",
    },
    {
        data: "status",
        name: "status",
    },
    {
        data: "actions",
        name: "actions",
        orderable: false,
        searchable: false,
    },
];

filterTable({ route: tableRoute, tableID: tableID, cols: cols });
