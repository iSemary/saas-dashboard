let tableID = "#table";
let route = $(tableID).attr("data-route");
let cols = [
    {
        data: "id",
        name: "id",
    },
    {
        data: "folder",
        name: "folder",
        orderable: false,
    },
    {
        data: "hash_name",
        name: "hash_name",
    },
    {
        data: "checksum",
        name: "checksum",
    },
    {
        data: "original_name",
        name: "original_name",
    },
    {
        data: "mime_type",
        name: "mime_type",
    },
    {
        data: "host",
        name: "host",
    },
    {
        data: "status",
        name: "status",
    },
    {
        data: "access_level",
        name: "access_level",
    },
    {
        data: "size",
        name: "size",
    },
    {
        data: "metadata",
        name: "metadata",
    },
    {
        data: "is_encrypted",
        name: "is_encrypted",
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

filterTable({ route: route, tableID: tableID, cols: cols });
