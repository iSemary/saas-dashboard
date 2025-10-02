let tableID = "#table";
let tableRoute = $(tableID).attr("data-route");
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
        data: "domain",
        name: "domain",
    },
    {
        data: "database",
        name: "database",
    },
    {
        data: "table_count",
        name: "table_count",
        orderable: false,
        searchable: false,
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

filterTable({ route: tableRoute, tableID: tableID, cols: cols });

// Handle tenant database operations
$(document).on('click', '.tenant-remigrate', function() {
    let tenantId = $(this).data('tenant-id');
    
    if (confirm('Are you sure you want to re-migrate this tenant database? This will reset all data!')) {
        $.ajax({
            url: `/landlord/tenants/${tenantId}/remigrate`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $(tableID).DataTable().ajax.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || 'An error occurred';
                toastr.error(message);
            },
            complete: function() {
                $('.tenant-remigrate[data-tenant-id="' + tenantId + '"]')
                    .prop('disabled', false)
                    .html('<i class="fas fa-database"></i>');
            }
        });
    }
});

$(document).on('click', '.tenant-seed', function() {
    let tenantId = $(this).data('tenant-id');
    
    if (confirm('Are you sure you want to seed this tenant database?')) {
        $.ajax({
            url: `/landlord/tenants/${tenantId}/seed`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $(tableID).DataTable().ajax.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || 'An error occurred';
                toastr.error(message);
            },
            complete: function() {
                $('.tenant-seed[data-tenant-id="' + tenantId + '"]')
                    .prop('disabled', false)
                    .html('<i class="fas fa-seedling"></i>');
            }
        });
    }
});

$(document).on('click', '.tenant-reseed', function() {
    let tenantId = $(this).data('tenant-id');
    
    if (confirm('Are you sure you want to re-seed this tenant database? This will reset all seeded data!')) {
        $.ajax({
            url: `/landlord/tenants/${tenantId}/reseed`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $(tableID).DataTable().ajax.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || 'An error occurred';
                toastr.error(message);
            },
            complete: function() {
                $('.tenant-reseed[data-tenant-id="' + tenantId + '"]')
                    .prop('disabled', false)
                    .html('<i class="fas fa-redo"></i>');
            }
        });
    }
});

// Auto-refresh table every 30 seconds to update table counts
setInterval(function() {
    if ($(tableID).length && $(tableID).DataTable()) {
        $(tableID).DataTable().ajax.reload(null, false);
    }
}, 30000);
