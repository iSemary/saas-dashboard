let tableID = "#table";
let route = $(tableID).attr("data-route");
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
        data: "module_key",
        name: "module_key",
    },
    {
        data: "description",
        name: "description",
    },
    {
        data: "route",
        name: "route",
    },
    {
        data: "icon",
        name: "icon",
    },
    {
        data: "slogan",
        name: "slogan",
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

filterTable({ route: route, tableID: tableID, cols: cols });
