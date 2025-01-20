var showTableID = "#showTable";
var showRoute = $(showTableID).attr("data-route");
var showCols = [
    {
        data: "id",
        name: "id",
    },
    {
        data: "agent",
        name: "agent",
    },
    {
        data: "ip",
        name: "ip",
    },
    {
        data: "created_at",
        name: "created_at",
    },
];

filterTable(showRoute, showTableID, null, null, true, showCols, null);
