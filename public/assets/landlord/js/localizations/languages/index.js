var tableID = "#table";
var route = $(tableID).attr("data-route");
var cols = [
    {
        data: "id",
        name: "id",
    },
    {
        data: "name",
        name: "name",
    },
    {
        data: "locale",
        name: "locale",
    },
    {
        data: "direction",
        name: "direction",
    },
    {
        data: "total_translations",
        name: "total_translations",
        searchable: false,
    },
    {
        data: "actions",
        name: "actions",
        orderable: false,
        searchable: false,
    },
];

filterTable({
    route: tableRoute,
    tableID: tableID,
    cols: cols,
    orderColumnIndex: 1,
    orderColumnType: "desc",
});
