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
        data: "country",
        name: "country",
    },
    {
        data: "province",
        name: "province",
    },
    {
        data: "city",
        name: "city",
    },
    {
        data: "town",
        name: "town",
    },
    {
        data: "actions",
        name: "actions",
        orderable: false,
        searchable: false,
    },
];

filterTable({ route: tableRoute, tableID: tableID, cols: cols });
