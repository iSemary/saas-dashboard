$(document).ready(function() {
    $('#profileTabs button').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
});