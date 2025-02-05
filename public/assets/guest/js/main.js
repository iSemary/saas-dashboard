// Logout Handler
$(".logout-btn").on("click", function (e) {
    e.preventDefault();

    let Form = $(this).data("form");
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user_id");
    localStorage.removeItem("order_number");

    $("#" + Form).submit();
});
