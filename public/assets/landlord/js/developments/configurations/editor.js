$("#inputType").on("change", function () {
    handleInputType($(this).val(), "");
});

// Function to handle dynamic input fields based on input type
function handleInputType(inputType, configValue) {
    var inputTypeField = $("#inputTypeFields");
    inputTypeField.empty(); // Clear previous input fields

    // Check the selected input type and display the relevant input fields
    switch (inputType) {
        case "string":
            inputTypeField.html(
                `<div class="w-100">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroupPrependString"><i class="fa fa-text-width"></i></span>
                        </div>
                        <input type="text" name="configuration_value" id="configuration_value" class="form-control" required value="${configValue}" />
                    </div>
                </div>`
            );
            break;
        case "integer":
            inputTypeField.html(
                `<div class="w-100">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroupPrependInteger"><i class="fa fa-hashtag"></i></span>
                        </div>
                        <input type="number" name="configuration_value" id="configuration_value" class="form-control" required value="${configValue}" />
                    </div>
                </div>`
            );
            break;

        case "boolean":
            inputTypeField.html(
                `<label class="checkbox-inline">
                        <input type="checkbox" name="configuration_value" id="configuration_value" class="form-toggle" ${
                            configValue === "1" ? "checked" : ""
                        } data-toggle="toggle"> ${translate.active}
                    </label>`
            );
            break;

        case "html":
            inputTypeField.html(
                `<textarea name="configuration_value" id="ckInput" class="form-control" required>${configValue}</textarea>`
            );
            break;
        case "file":
            inputTypeField.html(
                `<input type="file" name="configuration_value" id="configuration_value" required />`
            );
            break;
        case "array":
            inputTypeField.html(
                `<textarea name="configuration_value" id="configuration_value" class="form-control" required>${configValue}</textarea>`
            );
            break;
        case "object":
            inputTypeField.html(
                `<textarea name="configuration_value" id="configuration_value" class="form-control" required>${configValue}</textarea>`
            );
            break;
        case "email":
            inputTypeField.html(
                `<div class="w-100">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrependEmail"><i class="fa fa-envelope"></i></span>
                            </div>
                            <input type="email" name="configuration_value" id="configuration_value" class="form-control" value="${configValue}" required />
                        </div>
                    </div>`
            );
            break;
        case "url":
            inputTypeField.html(
                `<div class="w-100">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrependUrl"><i class="fa fa-link"></i></span>
                            </div>
                            <input type="url" name="configuration_value" id="configuration_value" class="form-control" value="${configValue}" required />
                        </div>
                    </div>`
            );
            break;
        case "password":
            inputTypeField.html(
                `<div class="w-100">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrependPassword"><i class="fa fa-lock"></i></span>
                            </div>
                            <input type="password" name="configuration_value" id="configuration_value" class="form-control" value="" ${configValue && configValue !== ""?"": "required"} />
                        </div>
                    </div>`
            );
            break;
        case "phone":
            inputTypeField.html(
                `<div class="w-100">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrependPhone"><i class="fa fa-phone"></i></span>
                            </div>
                            <input type="tel" name="configuration_value" id="configuration_value" class="form-control" value="${configValue}" required />
                        </div>
                    </div>`
            );
            break;
        case "date":
            inputTypeField.html(
                `<div class="w-100">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrependDate"><i class="fa fa-calendar"></i></span>
                            </div>
                            <input type="date" name="configuration_value" id="configuration_value" class="form-control" value="${configValue}" required />
                        </div>
                    </div>`
            );
            break;
        case "time":
            inputTypeField.html(
                `<div class="w-100">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrependTime"><i class="fa fa-clock"></i></span>
                            </div>
                            <input type="time" name="configuration_value" id="configuration_value" class="form-control" value="${configValue}" required />
                        </div>
                    </div>`
            );
            break;
        case "datetime":
            inputTypeField.html(
                `<div class="w-100">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrependDatetime"><i class="fa fa-calendar-alt"></i></span>
                            </div>
                            <input type="datetime-local" name="configuration_value" id="configuration_value" class="form-control" value="${configValue}" required />
                        </div>
                    </div>`
            );
            break;
        case "color":
            inputTypeField.html(
                `<input type="color" name="configuration_value" id="configuration_value" class="form-control" value="${configValue}" required />`
            );
            break;
        case "range":
            inputTypeField.html(
                `<input type="range" name="configuration_value" id="configuration_value" class="form-control" value="${configValue}" required />`
            );
            break;
        default:
            inputTypeField.html(
                `<input type="text" name="configuration_value" id="configuration_value" class="form-control default-input" value="${configValue}" required/>`
            );
            break;
    }

    fireDependencies();
}
