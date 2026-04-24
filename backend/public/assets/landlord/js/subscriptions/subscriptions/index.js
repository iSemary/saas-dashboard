let tableID = "#table";
let tableRoute = $(tableID).attr("data-route");
let cols = [
    { data: "id", name: "id" },
    { data: "subscription_id", name: "subscription_id" },
    { data: "brand_name", name: "brand_name" },
    { data: "user_name", name: "user_name" },
    { data: "plan_name", name: "plan_name" },
    { data: "formatted_price", name: "formatted_price" },
    { data: "billing_cycle", name: "billing_cycle" },
    { data: "next_billing", name: "next_billing" },
    { data: "days_remaining", name: "days_remaining" },
    { data: "status", name: "status" },
    { data: "actions", name: "actions", orderable: false, searchable: false },
];

filterTable({ route: tableRoute, tableID: tableID, cols: cols });
