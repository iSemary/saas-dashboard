let tableID = "#table";
let route = $(tableID).attr("data-route");
let cols = [
    { data: "id", name: "id" },
    { data: "name", name: "name" },
    { data: "slug", name: "slug" },
    { data: "description", name: "description" },
    { data: "parent_name", name: "parent_name" },
    { data: "icon", name: "icon" },
    { data: "type", name: "type" },
    { data: "priority", name: "priority" },
    { data: "status", name: "status" },
    { data: "actions", name: "actions", orderable: false, searchable: false },
];

filterTable(route, tableID, null, null, true, cols, null);
