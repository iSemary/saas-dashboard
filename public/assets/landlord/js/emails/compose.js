
$(document).on("change", "#recipientsType", function () {
    const recipientsType = $(this).val();
    const recipientsRoute = $(this).data("recipients-route");
    const allUsersRoute = $(this).data("all-users-route");
    showRecipientsContainer(recipientsType, recipientsRoute, allUsersRoute);
});

function showRecipientsContainer(
    recipientsType,
    recipientsRoute,
    allUsersRoute
) {
    $(".email-to-container").html("");

    switch (recipientsType) {
        case "all_users":
            showAllUsersContainer(allUsersRoute);
            break;
        case "recipients_only":
            showRecipientsOnlyContainer(recipientsRoute);
            break;
        case "multiple":
            showMultipleRecipientsContainer(recipientsRoute);
            break;
        case "single":
            showSingleRecipientsContainer();
            break;
        case "upload_excel":
            showUploadExcelRecipientsContainer();
            break;
        default:
            break;
    }
}

function showAllUsersContainer(allUsersRoute) {
    var totalUsers = `<i class='fas fa-spinner fa-spin'></i>`;

    $.ajax({
        type: "GET",
        url: allUsersRoute,
        dataType: "json",
        beforeSend: function () {
            $(".email-to-container").html(
                `<p class="mb-0">${t('all_users_will_receive_the_email')}</p><p>${t('total_users')}: <b>${totalUsers}</b></p>`
            );
        },
        success: function (response) {
            totalUsers = response.data.count;
            $(".email-to-container").html(
                `<p class="mb-0">${t('all_users_will_receive_the_email')}</p><p>${t('total_users')}: <b>${totalUsers}</b></p>`
            );
        },
    });
}

function showRecipientsOnlyContainer(recipientsRoute) {
    var totalRecipients = `<i class='fas fa-spinner fa-spin'></i>`;

    $.ajax({
        type: "GET",
        url: recipientsRoute,
        dataType: "json",
        beforeSend: function () {
            $(".email-to-container").html(
                `<p class="mb-0">${t('all_recipients_will_receive_the_email')}</p><p>${t('total_recipients')}: <b>${totalRecipients}</b></p>`
            );
        },
        success: function (response) {
            totalRecipients = response.data.data.total;
            $(".email-to-container").html(
                `<p class="mb-0">${t('all_recipients_will_receive_the_email')}</p><p>${t('total_recipients')}: <b>${totalRecipients}</b></p>`
            );
        },
    });
}

function showMultipleRecipientsContainer(recipientsRoute) {
    $(".email-to-container").html(
        `<select name="emails[]" class="form-control emails-selector" required multiple></select>`
    );

    $(".emails-selector").select2({
        allowClear: true,
        ajax: {
            url: recipientsRoute,
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    term: params.term || "", // Search term
                    page: params.page || 1, // Current page for pagination
                };
            },
            processResults: function (response, params) {
                // Extract the data from the nested structure
                const items = response.data.data.data;

                // Map the data to the format Select2 expects
                const results = items.map(function (item) {
                    return {
                        id: item.id, // Use 'id' as the value
                        text: item.email, // Use 'email' as the displayed text
                    };
                });

                // Check if there are more pages
                const pagination = response.data.data;
                const hasMore = pagination.current_page < pagination.last_page;

                return {
                    results: results,
                    pagination: {
                        more: hasMore,
                    },
                };
            },
            cache: true,
        },
        placeholder: `${t('search_for_recipients')}...`,
        language: {
            searching: function () {
                return `${t('searching')}...`;
            },
        },
        minimumInputLength: 1,
    });
}

function showSingleRecipientsContainer() {
    $(".email-to-container").html(
        `<input class="form-control" placeholder="${t('to')}:" type="email" name="email" id="email" required/>`
    );
}

function showUploadExcelRecipientsContainer() {
    var samplePath = $("#recipientsType").attr("data-excel-sample");
    $(".email-to-container").html(`
        <div class="upload-excel-container mb-2">
            <label for="uploadExcel" class="upload-label">${t('upload_excel_file')} <a href="${samplePath}" target="_blank">${t('view_sample')}</a></label><br/>
            <input type="file" id="uploadExcel" name="excel_file" accept=".xlsx, .xls" class="upload-input" />
        </div>
        <div class="excel-table">
            <table class="excel-datatable table table-bordered table-striped table-hover" data-disable-reload="true">
                <thead>
                    <tr>
                        <th>${t('email')}<span class="text-danger">*</span></th>
                        <th>${t('name')}</th>
                        <th>${t('action')}</th>
                    </tr>
                </thead>
                <tbody id="excelDataBody">
                    <tr>
                        <td>example@test.com</td>
                        <td>Example (Optional)</td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash-alt"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    `);

    initializeDataTable();
}

function initializeDataTable() {
    $(".excel-datatable").DataTable({
        destroy: true,
        responsive: true,
        ordering: false,
        order: false,
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],
    });
}

$(document).on("change", "#uploadExcel", function (e) {
    const file = e.target.files[0];
    const reader = new FileReader();

    reader.onload = function (e) {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: "array" });

        // Assume first sheet
        const firstSheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[firstSheetName];

        // Convert to JSON
        const excelData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

        // Clear existing rows
        const tbody = $("#excelDataBody");
        tbody.empty();

        // Populate table (assuming email in first column, name in second)
        excelData.slice(1).forEach((row) => {
            // Ensure email exists
            if (row[0]) {
                tbody.append(`
                    <tr>
                        <td><input type="hidden" name="excel_emails[]" value="${row[0]}" />${row[0]}</td>
                        <td><input type="hidden" name="excel_names[]" value="${row[1]}" />${row[1] || ""}</td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash-alt"></i></button></td>
                    </tr>
                `);
            }
        });
    };

    // Reinitialize DataTable
    initializeDataTable();

    reader.readAsArrayBuffer(file);
});

$(document).on("click", ".remove-row", function (e) {
    $(this).closest("tr").remove();
});

$(document).on("change", ".select-email-template", function () {
    const route = $(this).find("option:selected").data("route");

    $.ajax({
        url: route,
        type: "GET",
        success: function (response) {
            const { subject, body } = response.data.data;
            $("#subject").val(subject);
            CKEDITOR.instances.ckInput.setData(body);
        },
        error: function (error) {
            console.log(error);
        },
    });
});
