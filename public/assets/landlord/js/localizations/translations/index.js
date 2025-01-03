let tableID = "#table";
let route = $(tableID).attr("data-route");
let cols = [
    {
        data: "id",
        name: "id",
    },
    {
        data: "language",
        name: "language",
    },
    {
        data: "translation_key",
        name: "translation_key",
    },
    {
        data: "translation_value",
        name: "translation_value",
    },
    {
        data: "translation_context",
        name: "translation_context",
    },
    {
        data: "actions",
        name: "actions",
        orderable: false,
        searchable: false,
    },
];

filterTable(route, tableID, null, null, true, cols, null);
