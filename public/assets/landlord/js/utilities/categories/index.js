let tableID = "#table";
let route = $(tableID).attr("data-route");
let cols = [
    { data: "id", name: "id" },
    { data: "name", name: "name" },
    { data: "slug", name: "slug" },
    { data: "description", name: "description" },
    { data: "parent_name", name: "parent_name" },
    { data: "icon", name: "icon", orderable: false, searchable: false },
    { data: "priority", name: "priority" },
    { data: "status", name: "status" },
    { data: "actions", name: "actions", orderable: false, searchable: false },
];

filterTable({ route: route, tableID: tableID, cols: cols });
