let tableID = "#table";
let route = $(tableID).attr("data-route");
let cols = [
    {
        data: "id",
        name: "id",
    },
    {
        data: "object_model",
        name: "object_model",
    },
    {
        data: "object_id",
        name: "object_id",
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
        data: "body",
        name: "body",
    },
    {
        data: "version",
        name: "version",
    },
    {
        data: "status",
        name: "status",
    },
    {
        data: "release_date",
        name: "release_date",
    },
    {
        data: "actions",
        name: "actions",
        orderable: false,
        searchable: false,
    },
];

filterTable({ route: route, tableID: tableID, cols: cols });
