$(document).ready(function() {
    // Auto-generate slug from name
    $('#name').on('input', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        $('#slug').val(slug);
    });

    // Image preview
    $('#logo').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('.preview-image').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Initialize select2 if present
    if ($.fn.select2) {
        $('.select2').select2({
            width: '100%'
        });
    }

    // Initialize emoji input if present
    if ($.fn.emojioneArea) {
        $('.emoji-input').emojioneArea({
            pickerPosition: 'bottom',
            tonesStyle: 'bullet'
        });
    }
});
