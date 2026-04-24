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
        data: "metadata",
        name: "metadata",
    },
    {
        data: "created_at",
        name: "created_at",
    },
];

filterTable({ route: tableRoute, tableID: tableID, cols: cols });
