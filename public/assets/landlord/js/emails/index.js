var tableID = "#table";
var route = $(tableID).attr("data-route");
var selectable = $(tableID).attr("data-selectable") == "true" ? true : false;
var cols = [
    {
        data: null,
        render: function (data, type, row) {
            return '<div class="text-center"><input type="checkbox" class="select-row" name="select_row" /></div>';
        },
        orderable: false,
        searchable: false,
    },
    {
        data: "id",
        name: "id",
    },
    {
        data: "template_name",
        name: "template_name",
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

filterTable({
    route: route,
    tableID: tableID,
    cols: cols,
    selectable: selectable,
    orderColumnIndex: 1,
    orderColumnType: "desc",
});
