// Unbind events
$(document).off("click", ".remove-meta");
$(document).off("click", ".add-meta");

// Function to generate HTML for a meta field row
function createMetaRow(meta) {
    // Clone the template
    const $template = $(".meta-row-template .meta-row").clone();

    // Set the values if meta is provided
    if (meta) {
        $template.find('input[name="meta_keys[]"]').val(meta.meta_key);
        $template.find('textarea[name="meta_values[]"]').val(meta.meta_value);
    }

    $template.find(".remove-meta").off('click').on("click", function () {
        $(this).closest(".meta-row").remove();
    });

    return $template;
}

// Function to render meta fields
function renderMetas(metas) {
    // Clear the current meta fields
    $(".meta-container").html("");

    // If metas is provided, render them
    if (Array.isArray(metas)) {
        metas.forEach((meta) => {
            const $metaRow = createMetaRow(meta);
            $(".meta-container").append($metaRow);
        });
    }
}

// Add new meta field
$(document).on("click", ".add-meta", function () {
    const $newMetaRow = createMetaRow();
    $(".meta-container").append($newMetaRow);
});

// Remove meta field
$(document).on("click", ".remove-meta", function () {
    $(this).closest(".meta-row").remove();
});

