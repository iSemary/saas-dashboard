// Tab (foucas input)
shortcut.add("Tab", function () {
    $("#barcodeNumber").focus();
});
// Delete Button Cancel Item
shortcut.add("Delete", function () {
    $('#cancelItem').click();
});
// F1 Change Price
shortcut.add("F1", function () {
    if ($(".item-on-active")[0] && !$(".modal").is(":visible") && !$(".swal2-container").is(":visible")) {
        changePrice();
    }
});
// F2 Change Amount
shortcut.add("F2", function () {
    if ($(".item-on-active")[0] && !$(".modal").is(":visible") && !$(".swal2-container").is(":visible")) {
        changeAmount();
    }
});
// Ctrl + Z Pay Order
shortcut.add("Ctrl+Z", function () {
    payOrder();
});
// Ctrl + L Print Last Receipt
shortcut.add("Ctrl+L", function () {
    PrintLastReceipt();
});
// Ctrl + A Call calculator
shortcut.add("Ctrl+A", function () {
    showCalculator();
});
// Ctrl + C Call calculator
shortcut.add("Ctrl+C", function () {
    cancelOrder();
});
// Ctrl + B Categories Foucas
shortcut.add("Ctrl+B", function () {
    $("#CategorySelect").focus();
});
// Ctrl + H Sub Categories Foucas
shortcut.add("Ctrl+H", function () {
    $("#SubCategory").focus();
});
// Ctrl + M Products Foucas
shortcut.add("Ctrl+M", function () {
    $("#ProductSelect").focus();
});
// Enter to submit search by barcode
shortcut.add("Enter", function () {
    if ($("#barcodeNumber").is(":focus")) {
        searchByBarcode();
    }
});
// Ctrl + P Print
shortcut.add("Ctrl+P", function () {
    $("body").removeClass('swal2-shown swal2-height-auto');
    window.print();
});

