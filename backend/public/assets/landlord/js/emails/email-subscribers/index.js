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
