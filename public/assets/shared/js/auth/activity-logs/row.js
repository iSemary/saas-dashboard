var showTableID = "#showTable";
var showRoute = $(showTableID).attr("data-route");
var showCols = [
    {
        data: "id",
        name: "id",
    },
    {
        data: "event",
        name: "event",
    },
    {
        data: "type",
        name: "type",
    },
    {
        data: "type_id",
        name: "type_id",
    },
    {
        data: "old_values",
        name: "old_values",
    },
    {
        data: "new_values",
        name: "new_values",
    },
    {
        data: "ip_address",
        name: "ip_address",
    },
    {
        data: "user_agent",
        name: "user_agent",
    },
];

filterTable({ route: showRoute, tableID: showTableID, cols: showCols });
