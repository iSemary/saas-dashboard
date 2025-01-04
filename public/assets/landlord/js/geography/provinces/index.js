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
        data: "country",
        name: "country",
    },
    {
        data: "is_capital",
        name: "is_capital",
    },
    {
        data: "flag",
        name: "flag",
    },
    {
        data: "phone_code",
        name: "phone_code",
    },
    {
        data: "actions",
        name: "actions",
        orderable: false,
        searchable: false,
    },
];

filterTable(route, tableID, null, null, true, cols, null);
