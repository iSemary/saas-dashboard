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
        data: "capital_province",
        name: "capital_province",
    },
    {
        data: "code",
        name: "code",
    },
    {
        data: "region",
        name: "region",
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
