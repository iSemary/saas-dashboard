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
        data: "domain",
        name: "domain",
    },
    {
        data: "database",
        name: "database",
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

filterTable({ route: tableRoute, tableID: tableID, cols: cols });
