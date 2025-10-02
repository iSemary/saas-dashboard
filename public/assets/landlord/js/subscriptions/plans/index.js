let tableID = "#table";
let tableRoute = $(tableID).attr("data-route");
let cols = [
    { data: "id", name: "id" },
    { data: "name", name: "name" },
    { data: "slug", name: "slug" },
    { data: "description", name: "description" },
    { data: "sort_order", name: "sort_order" },
    { data: "is_popular_badge", name: "is_popular_badge", orderable: false, searchable: false },
    { data: "subscriptions_count", name: "subscriptions_count" },
    { data: "active_subscriptions_count", name: "active_subscriptions_count" },
    { data: "status", name: "status" },
    { data: "actions", name: "actions", orderable: false, searchable: false },
];

filterTable({ route: tableRoute, tableID: tableID, cols: cols });
