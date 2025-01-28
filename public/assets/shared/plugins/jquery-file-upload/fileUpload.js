(function ($) {
    var fileUploadCount = 0;

    $.fn.fileUpload = function () {
        return this.each(function () {
            var fileUploadDiv = $(this);
            var fileUploadId = `fileUpload-${++fileUploadCount}`;

            // Get configuration from data attributes
            var config = {
                multiple: fileUploadDiv.data("multiple") || false,
                required: fileUploadDiv.data("required") || false,
                maxFileSize: fileUploadDiv.data("max-file-size") || Infinity, // in KB
                allowedFiles: (fileUploadDiv.data("allowed-files") || "")
                    .split(",")
                    .filter(Boolean),
                label: fileUploadDiv.data("label") || "Drag & Drop Files Here",
                buttonLabel:
                    fileUploadDiv.data("button-label") || "Browse Files",
            };

            // Create the accept attribute value
            var acceptValue = config.allowedFiles
                .map((ext) => `.${ext}`) // Add a dot before each extension
                .join(","); // Join into a comma-separated string

            // Creates HTML content for the file upload area
            var fileDivContent = `
                <label for="${fileUploadId}" class="file-upload">
                    <div>
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>${config.label}</p>
                        <span>${t('or')}</span>
                        <div>${config.buttonLabel}</div>
                    </div>
                    <input type="file" id="${fileUploadId}" name="files[]" 
                           ${config.multiple ? "multiple" : ""} 
                           ${config.required ? "required" : ""} 
                           ${acceptValue ? `accept="${acceptValue}"` : ""} 
                           hidden />
                </label>
            `;

            fileUploadDiv.html(fileDivContent).addClass("file-container");

            var table = null;
            var tableBody = null;

            // Creates a table containing file information
            function createTable() {
                table = $(`
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th style="width: 30%;">${t('file_name')}</th>
                                <th>${t('preview')}</th>
                                <th style="width: 20%;">Size</th>
                                <th>${t('type')}</th>
                                <th>${t('action')}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                `);

                tableBody = table.find("tbody");
                fileUploadDiv.append(table);
            }

            // Validate file
            function validateFile(file) {
                // Check file size
                if (file.size > config.maxFileSize * 1024) {
                    return `File ${file.name} exceeds maximum size of ${config.maxFileSize}KB`;
                }

                // Check file type if specified
                if (config.allowedFiles.length > 0) {
                    const extension = file.name.split(".").pop().toLowerCase();
                    if (!config.allowedFiles.includes(extension)) {
                        return `File type .${extension} is not allowed. Allowed types: ${config.allowedFiles.join(
                            ", "
                        )}`;
                    }
                }

                return null; // null means valid
            }

            // Adds the information of uploaded files to table
            function handleFiles(files) {
                if (!table) {
                    createTable();
                }

                tableBody.empty();
                if (files.length > 0) {
                    let hasErrors = false;

                    $.each(files, function (index, file) {
                        const validationError = validateFile(file);
                        if (validationError) {
                            hasErrors = true;
                            tableBody.append(`
                                <tr class="error">
                                    <td colspan="6">${validationError}</td>
                                </tr>
                            `);
                            return;
                        }

                        var fileName = file.name;
                        var fileSize = (file.size / 1024).toFixed(2) + " KB";
                        var fileType = file.type;
                        var preview = fileType.startsWith("image")
                            ? `<img src="${URL.createObjectURL(
                                  file
                              )}" alt="${fileName}" class="view-image" height="30">`
                            : `<i class="far fa-eye-slash"></i>`;

                        tableBody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${fileName}</td>
                                <td>${preview}</td>
                                <td>${fileSize}</td>
                                <td>${fileType}</td>
                                <td><button type="button" class="deleteBtn"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        `);
                    });

                    if (hasErrors) {
                        // Clear the file input if there are validation errors
                        fileUploadDiv.find(`#${fileUploadId}`).val("");
                    }

                    tableBody.find(".deleteBtn").click(function () {
                        $(this).closest("tr").remove();

                        if (tableBody.find("tr").length === 0) {
                            tableBody.append(
                                '<tr><td colspan="6" class="no-file">No files selected!</td></tr>'
                            );
                        }
                    });
                }
            }

            // Events triggered after dragging files
            fileUploadDiv.on({
                dragover: function (e) {
                    e.preventDefault();
                    fileUploadDiv.toggleClass(
                        "dragover",
                        e.type === "dragover"
                    );
                },
                drop: function (e) {
                    e.preventDefault();
                    fileUploadDiv.removeClass("dragover");
                    handleFiles(e.originalEvent.dataTransfer.files);
                },
            });

            // Event triggered when file is selected
            fileUploadDiv.find(`#${fileUploadId}`).change(function () {
                handleFiles(this.files);
            });
        });
    };
})(jQuery);
