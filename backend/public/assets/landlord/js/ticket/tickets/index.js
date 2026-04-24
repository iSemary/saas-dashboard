/**
 * Ticket Index JavaScript
 * Handles DataTables, filtering, and bulk actions
 */

$(document).ready(function() {
    const tableID = "#table";
    const routeURL = $(tableID).data("route");
    
    // Initialize DataTable
    let table = $(tableID).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: routeURL,
            data: function(d) {
                d.status = $('#statusFilter').val();
                d.priority = $('#priorityFilter').val();
                d.assigned_to = $('#assigneeFilter').val();
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
            }
        },
        columns: [
            {
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<input type="checkbox" class="form-check-input ticket-checkbox" value="${data}">`;
                }
            },
            { data: "id", name: "id" },
            { data: "ticket_number", name: "ticket_number" },
            { 
                data: "title", 
                name: "title",
                render: function(data, type, row) {
                    return `<a href="/landlord/tickets/${row.id}" class="text-decoration-none">${data}</a>`;
                }
            },
            { 
                data: "status_badge", 
                name: "status",
                orderable: false,
                searchable: false
            },
            { 
                data: "priority_badge", 
                name: "priority",
                orderable: false,
                searchable: false
            },
            { data: "creator_name", name: "creator.name" },
            { data: "assignee_name", name: "assignee.name" },
            { data: "brand_name", name: "brand.name" },
            { 
                data: "comments_count", 
                name: "comments_count",
                render: function(data, type, row) {
                    return data > 0 ? `<span class="badge bg-info">${data}</span>` : '0';
                }
            },
            { 
                data: "created_at", 
                name: "created_at",
                render: function(data, type, row) {
                    return moment(data).format('MMM DD, YYYY HH:mm');
                }
            },
            {
                data: "actions",
                name: "actions",
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let actions = data;
                    
                    // Add overdue indicator
                    if (row.overdue_indicator) {
                        actions = row.overdue_indicator + ' ' + actions;
                    }
                    
                    return actions;
                }
            }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-info btn-sm'
            }
        ],
        createdRow: function(row, data, dataIndex) {
            // Add priority class to row
            $(row).addClass(`ticket-priority-${data.priority}`);
            $(row).addClass('ticket-row');
            
            // Add overdue class if applicable
            if (data.overdue_indicator) {
                $(row).addClass('table-warning');
            }
        }
    });

    // Filter event handlers
    $('#statusFilter, #priorityFilter, #assigneeFilter').on('change', function() {
        table.draw();
    });

    // Date filter handlers
    $('#from_date, #to_date').on('change', function() {
        table.draw();
    });

    // Select all checkbox handler
    $('#selectAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.ticket-checkbox').prop('checked', isChecked);
        updateBulkActionsPanel();
    });

    // Individual checkbox handler
    $(document).on('change', '.ticket-checkbox', function() {
        updateBulkActionsPanel();
        
        // Update select all checkbox
        const totalCheckboxes = $('.ticket-checkbox').length;
        const checkedCheckboxes = $('.ticket-checkbox:checked').length;
        
        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes);
    });

    // Auto-refresh every 2 minutes
    setInterval(function() {
        if ($(tableID).length && $(tableID).DataTable()) {
            $(tableID).DataTable().ajax.reload(null, false);
        }
    }, 120000);
});

function updateBulkActionsPanel() {
    const selectedCount = $('.ticket-checkbox:checked').length;
    
    if (selectedCount > 0) {
        $('#bulkActionsPanel').show();
        $('#selectedCount').text(selectedCount);
    } else {
        $('#bulkActionsPanel').hide();
    }
}

function getSelectedTicketIds() {
    return $('.ticket-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
}

function bulkAssign() {
    const selectedIds = getSelectedTicketIds();
    if (selectedIds.length === 0) {
        toastr.warning(translate('no_tickets_selected'));
        return;
    }
    
    // Show bulk assign modal
    $('#bulkAssignModal').modal('show');
    $('#bulkAssignTicketIds').val(JSON.stringify(selectedIds));
}

function bulkUpdateStatus() {
    const selectedIds = getSelectedTicketIds();
    if (selectedIds.length === 0) {
        toastr.warning(translate('no_tickets_selected'));
        return;
    }
    
    // Show bulk status modal
    $('#bulkStatusModal').modal('show');
    $('#bulkStatusTicketIds').val(JSON.stringify(selectedIds));
}

function bulkUpdatePriority() {
    const selectedIds = getSelectedTicketIds();
    if (selectedIds.length === 0) {
        toastr.warning(translate('no_tickets_selected'));
        return;
    }
    
    // Show bulk priority modal
    $('#bulkPriorityModal').modal('show');
    $('#bulkPriorityTicketIds').val(JSON.stringify(selectedIds));
}

function bulkDelete() {
    const selectedIds = getSelectedTicketIds();
    if (selectedIds.length === 0) {
        toastr.warning(translate('no_tickets_selected'));
        return;
    }
    
    if (confirm(translate('confirm_bulk_delete'))) {
        performBulkAction('delete', null, selectedIds);
    }
}

async function performBulkAction(action, value, ticketIds, comment = null) {
    try {
        const response = await fetch('/landlord/tickets/bulk-update', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ticket_ids: ticketIds,
                action: action,
                value: value,
                comment: comment
            })
        });

        if (response.ok) {
            const data = await response.json();
            toastr.success(`${translate('bulk_action_completed')}: ${data.data.updated_count} ${translate('tickets_updated')}`);
            
            // Refresh table and hide bulk actions panel
            $('#table').DataTable().ajax.reload();
            $('#bulkActionsPanel').hide();
            $('.ticket-checkbox').prop('checked', false);
            $('#selectAll').prop('checked', false);
            
            // Close any open modals
            $('.modal').modal('hide');
        } else {
            throw new Error('Failed to perform bulk action');
        }
    } catch (error) {
        console.error('Error performing bulk action:', error);
        toastr.error(translate('error_bulk_action'));
    }
}

// Bulk assign form handler
$(document).on('submit', '#bulkAssignForm', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const ticketIds = JSON.parse(formData.get('ticket_ids'));
    const assignedTo = formData.get('assigned_to');
    const comment = formData.get('comment');
    
    performBulkAction('assign', assignedTo, ticketIds, comment);
});

// Bulk status form handler
$(document).on('submit', '#bulkStatusForm', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const ticketIds = JSON.parse(formData.get('ticket_ids'));
    const status = formData.get('status');
    const comment = formData.get('comment');
    
    performBulkAction('status', status, ticketIds, comment);
});

// Bulk priority form handler
$(document).on('submit', '#bulkPriorityForm', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const ticketIds = JSON.parse(formData.get('ticket_ids'));
    const priority = formData.get('priority');
    const comment = formData.get('comment');
    
    performBulkAction('priority', priority, ticketIds, comment);
});

// Translation helper
function translate(key) {
    const translations = {
        'no_tickets_selected': 'No tickets selected',
        'confirm_bulk_delete': 'Are you sure you want to delete the selected tickets?',
        'bulk_action_completed': 'Bulk action completed',
        'tickets_updated': 'tickets updated',
        'error_bulk_action': 'Error performing bulk action'
    };
    
    return translations[key] || key;
}
