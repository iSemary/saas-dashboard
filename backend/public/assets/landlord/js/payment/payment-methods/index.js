let tableID = "#table";
let tableRoute = $(tableID).attr("data-route");
let cols = [
    { data: "id", name: "id" },
    { data: "name", name: "name" },
    { data: "processor_type", name: "processor_type" },
    { data: "gateway_name", name: "gateway_name" },
    { data: "supported_currencies", name: "supported_currencies", orderable: false, searchable: false },
    { data: "is_global", name: "is_global" },
    { data: "status", name: "status" },
    { data: "actions", name: "actions", orderable: false, searchable: false },
];

filterTable({ route: tableRoute, tableID: tableID, cols: cols });
