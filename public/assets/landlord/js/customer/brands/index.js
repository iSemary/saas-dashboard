let tableID = "#table";
let tableRoute = $(tableID).attr("data-route");
let cols = [
    { data: "id", name: "id" },
    { data: "logo", name: "logo", orderable: false, searchable: false },
    { data: "name", name: "name" },
    { data: "slug", name: "slug" },
    { data: "description", name: "description" },
    { data: "tenant_name", name: "tenant_name" },
    { data: "status", name: "status" },
    { data: "created_at", name: "created_at" },
    { data: "actions", name: "actions", orderable: false, searchable: false },
];

filterTable({ route: tableRoute, tableID: tableID, cols: cols });
