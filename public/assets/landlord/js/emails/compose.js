$(document).on("change", "#recipients_type", function () {
    const recipientsType = $(this).val();

    showRecipientsContainer(recipientsType);
});

function showRecipientsContainer(recipientsType) {
    $(".email-to-container").html("");

    switch (recipientsType) {
        case "all":
            showAllRecipientsContainer();
            break;
        case "recipients_only":
            showRecipientsOnlyContainer();
            break;
        case "multiple":
            showMultipleRecipientsContainer();
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

function showAllRecipientsContainer() {
    $(".email-to-container").html("");
}

function showRecipientsOnlyContainer() {
    $(".email-to-container").html("");
}

function showMultipleRecipientsContainer() {
    $(".email-to-container").html("");
}

function showSingleRecipientsContainer() {
    $(".email-to-container").html("");
}

function showUploadExcelRecipientsContainer() {
    $(".email-to-container").html("");
}

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
