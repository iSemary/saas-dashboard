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
        data: "slug",
        name: "slug",
    },
    {
        data: "description",
        name: "description",
    },
    {
        data: "status",
        name: "status",
    },
    {
        data: "icon",
        name: "icon",
        orderable: false,
        searchable: false,
    },
    {
        data: "actions",
        name: "actions",
        orderable: false,
        searchable: false,
    },
];

filterTable({ route: tableRoute, tableID: tableID, cols: cols });
