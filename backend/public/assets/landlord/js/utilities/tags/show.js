var showTableID = "#showTable";
var showRoute = $(showTableID).attr("data-route");
var showCols = [
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

filterTable({ route: showRoute, tableID: showTableID, cols: showCols });
