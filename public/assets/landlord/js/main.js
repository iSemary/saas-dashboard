headerParams = {
    "Content-Type": "application/x-www-form-urlencoded",
    Authorization: "Bearer " + localStorage.getItem("auth_token"),
};
// const currencyObject = JSON.parse($('meta[name="store_currency"]').attr("content"));
// const currency = currencyObject.value;
const CsrfToken = $('meta[name="_token"]').attr("content");
// const LoadingSpan = `<span><i class="fa fa-spinner fa-spin"></i> ${lang.please_wait}</span>`;

/**
 *
 * Initial Functions
 *
 */

function clearStatus() {
    $(".barcode-status").html(``);
}

function barcodeFocus() {
    $("#barcodeNumber").focus();
}

function barcodeSearchingStatus() {
    $(".barcode-status")
        .html(
            `<i class="fas fa-circle-notch fa-spin"></i> ` +
                lang.barcode_searching
        )
        .css("color", "#343a40");
}

function barcodeFoundStatus() {
    $(".barcode-status")
        .html(`<i class="fas fa-check-circle"></i> ` + lang.success_data_found)
        .css("color", "#28a745");
}

function barcodeNotFoundStatus() {
    $(".barcode-status")
        .html(`<i class="fas fa-exclamation-circle"></i> ` + lang.no_data_found)
        .css("color", "#dc3545");
}

function disableProductSelect() {
    $("#ProductSelect").html("").prop("disabled", true);
}

function searchBarcodeGetProduct() {
    var id = $("#barcodeNumber").val();
    $.ajax({
        type: "get",
        dataType: "json",
        url: `${url_search_barcode_get}/${id}`,
        headers: headerParams,
        beforeSend: function () {
            barcodeSearchingStatus();
        },
        success: function (data) {
            if (data.length == 0 || data == "empty") {
                barcodeNotFoundStatus();
                $(".product-name").html("");
            } else {
                barcodeFoundStatus();
                disableProductSelect();
                $("#barcodeNumber").attr("data-product-id", data.id);
                $(".product-name").html(
                    data.name +
                        " - " +
                        lang.price +
                        ` :` +
                        data.sale_price +
                        " " +
                        data.currency
                );
                $("#OriginalPrice, #OfferPrice, #ReturnAmount").val(
                    data.sale_price
                );
            }
        },
        error: function () {
            $(".product-name").html("");
        },
    });
}

// Delete Form
$(document).on("click", "#DeleteForm button", function (e) {
    e.preventDefault();
    let DeleteForm = $(this).parent("form");
    let ModelDeleteType = DeleteForm.attr("data-delete-type");
    Swal.fire({
        title: lang.are_you_sure,
        text: lang.you_want_delete_this + ModelDeleteType + "?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: lang.delete,
        cancelButtonText: lang.cancel,
    }).then((result) => {
        if (result.value) {
            DeleteForm.submit();
        }
    });
});
// Remove the below text line if there's no data inside it
$("#navbarVerticalNav")
    .children(".nav-item")
    .each(function (i, obj) {
        if ($(obj).children().length == 1) {
            obj.remove();
        }
    });

// DataTables Config
$.extend(true, $.fn.dataTable.defaults, {
    // language: {
    //     url: "LanguageJson",
    // },
    lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "All"],
    ],
    dom: "Blfrtip",
    // dom: "lpftrip",
    buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5", "print"],
});

// Dark Theme [0->dark | 1->light]
document.currentScript.getAttribute("theme") == 1
    ? $(".main-header").removeClass("navbar-white").addClass("navbar-dark")
    : "";

// Image Modal [On Click on any images pops up]

$("img:not(.most-product img, .circle-logo-content img)").on(
    "click",
    function () {
        $("#imagepreviewApp").attr("src", $(this).attr("src"));
        $("#imagemodalApp").modal("show");
    }
);

/**
 *
 * ========================================
 *          System Configuration
 * ========================================
 */

if (document.currentScript.getAttribute("env") == "production") {
    function online() {
        return;
    }

    function offline() {
        Swal.fire({
            title: lang.no_internet_connection,
            imageUrl: assets.no_wifi,
            imageSize: "512x512",
            confirmButton: true,
        }).then(function (isConfirm) {
            if (isConfirm) {
                if (window.navigator.onLine) {
                    online();
                } else {
                    offline();
                }
            }
        });
    }

    if (window.navigator.onLine) {
        online();
    } else {
        offline();
    }
    window.addEventListener("online", online);
    window.addEventListener("offline", offline);
}
// Disable Inspect Element [0->local/1->production]
if (document.currentScript.getAttribute("env") == "production") {
    $(function () {
        $(this).bind("contextmenu", function (e) {
            e.preventDefault();
        });
    });
    $(document).keydown(function (event) {
        if (event.keyCode == 123) {
            // Prevent F12
            return false;
        } else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) {
            // Prevent Ctrl+Shift+I
            return false;
        }
    });

    var element = new Image();
    var devtoolsOpen = false;
    element.__defineGetter__("id", function () {
        devtoolsOpen = true;
    });
    setInterval(function () {
        devtoolsOpen = false;
        console.log(element);
        if (devtoolsOpen) {
            $("body").html(
                `<div style="text-align:center;color:#fff;font-weight:bold;margin-top:50px;">@lang('inspect_element_on')</div>`
            );
            $("body").css("background-color", "red");
        }
    }, 1000);
}

// Get URL parameter name
function GetURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split("&");
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split("=");
        if (sParameterName[0] == sParam) {
            return decodeURIComponent(sParameterName[1]);
        }
    }
}

// Full Screen Trigger
$(document).keydown(function (event) {
    if (event.which == "122") {
        fullScreen();
    }
});

// if user opened with CashiercExe
let userAgent = navigator.userAgent;
if (userAgent.indexOf("QtWebEngine") >= 0) {
    $(document).on("click", "#FullScreen", function () {
        fullScreen();
    });

    function fullScreen() {
        let btn = $(this);
        let BestHeight = $(window).height() - 235 + 150;
        if (window.innerHeight != screen.height) {
            // browser is fullscreen
            $(".main-sidebar, .main-header").show(500);
            $(".anotherFullScreen").remove();
            btn.children("i").removeClass("fa-compress").addClass("fa-expand");
            $("div.content-wrapper").attr("style", "margin:auto");
            $(".order-content .top-quarter").attr("style", "height:400px");
            $("#TouchTopQurater").attr("style", "height:400px");
        } else {
            $("body").prepend(
                `<a class="nav-link anotherFullScreen" id="FullScreen" href="#"><i class="fas fa-expand"></i></a>`
            );
            $(".main-sidebar, .main-header").hide(500);
            $("div.content-wrapper").attr("style", "margin:0 !important");
            btn.children("i").removeClass("fa-expand").addClass("fa-compress");
            $(".order-content .top-quarter,.table-barcode-section").attr(
                "style",
                "height:545px"
            );
            $(".table-touch-section").attr("style", "height:400px");
            $(".content").attr("style", "padding: 0;");
            $("#TouchTopQurater").attr("style", "height:505px");
        }
    }

    // if user opened with another browser
} else {
    $(document).on("click", "#FullScreen", function () {
        fullScreen();
        (document.fullScreenElement && null !== document.fullScreenElement) ||
        (!document.mozFullScreen && !document.webkitIsFullScreen)
            ? document.documentElement.requestFullScreen
                ? document.documentElement.requestFullScreen()
                : document.documentElement.mozRequestFullScreen
                ? document.documentElement.mozRequestFullScreen()
                : document.documentElement.webkitRequestFullScreen &&
                  document.documentElement.webkitRequestFullScreen(
                      Element.ALLOW_KEYBOARD_INPUT
                  )
            : document.cancelFullScreen
            ? document.cancelFullScreen()
            : document.mozCancelFullScreen
            ? document.mozCancelFullScreen()
            : document.webkitCancelFullScreen &&
              document.webkitCancelFullScreen();
    });

    function fullScreen() {
        let btn = $(this);
        let BestHeight = $(window).height() - 235 + 150;
        if (window.innerHeight == screen.height) {
            // browser is fullscreen
            $(".main-sidebar, .main-header").show(500);
            $(".anotherFullScreen").remove();
            btn.children("i").removeClass("fa-compress").addClass("fa-expand");
            $("div.content-wrapper").attr("style", "margin:auto");
            $(".order-content .top-quarter").attr("style", "height:400px");
            $("#TouchTopQurater").attr("style", "height:400px");
        } else {
            $("body").prepend(
                `<a class="nav-link anotherFullScreen" id="FullScreen" href="#"><i class="fas fa-expand"></i></a>`
            );
            $(".main-sidebar, .main-header").hide(500);
            $("div.content-wrapper").attr("style", "margin:0 !important");
            btn.children("i").removeClass("fa-expand").addClass("fa-compress");
            $(".order-content .top-quarter,.table-barcode-section").attr(
                "style",
                "height:545px"
            );
            $(".table-touch-section").attr("style", "height:400px");
            $(".content").attr("style", "padding: 0;");
            $("#TouchTopQurater").attr("style", "height:505px");
        }
    }
}
// Logout Handler
$(".logout-btn").on("click", function (e) {
    e.preventDefault();

    let Form = $(this).data("form");
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user_id");
    localStorage.removeItem("order_number");

    $("#" + Form).submit();
});

// Is Offer Handler
$("#IsOffer").on("change", function () {
    if ($(this).is(":checked")) {
        let OfferPercentage = $("#OfferPercentage").val();
        $("#OfferPercentage").prop("disabled", false);
        $("#IsOffer").val("on");
    } else {
        $("#IsOffer").val("");
        $("#OfferPercentage").prop("disabled", true);
    }
});

// Offer Percentage Changer
$("#OfferPercentage, #SalePrice").on("input", function () {
    if ($("#IsOffer").is(":checked")) {
        $("#SalePriceOffer").val(
            parseFloat(
                $("#SalePrice").val().replace(",", "") -
                    ($("#SalePrice").val().replace(",", "") *
                        $("#OfferPercentage").val()) /
                        100
            ).toFixed(2)
        );
    }
});

// Currency Separator
$(document).ready(function () {
    var commaCounter = 10;

    function numberSeparator(Number) {
        Number += "";
        for (var i = 0; i < commaCounter; i++) {
            Number = Number.replace(",", "");
        }
        x = Number.split(".");
        y = x[0];
        z = x.length > 1 ? "." + x[1] : "";
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(y)) {
            y = y.replace(rgx, "$1" + "," + "$2");
        }
        commaCounter++;
        return y + z;
    }

    // Set Currency Separator to input fields
    $(document).on("keypress , paste", ".number-separator", function (e) {
        if (/^-?\d*[,.]?(\d{0,3},)*(\d{3},)?\d{0,3}$/.test(e.key)) {
            $(".number-separator").on("input", function () {
                e.target.value = numberSeparator(e.target.value);
            });
        } else {
            e.preventDefault();
            return false;
        }
    });
});

// Select 2
function fireSelect2() {
    $(".select2").select2({});
}
fireSelect2();

// Tooltip
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

function clearCacheCookies() {
    // Clear all website cache
    window.location.reload(true);

    // Clear all cookies
    var cookies = document.cookie.split(";");
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf("=");
        var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    }
}

$(document).on("change", "#uploadImg", function (e) {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#previewImg").attr("src", e.target.result);
        };
        reader.readAsDataURL(this.files[0]); // convert to base64 string
    }
});

const LargeModalStrings = ["products", "purchases", "branches"];

/**
 *
 * ===== Create Modal Section
 *
 */

function OpenCreateModal(url) {
    // $("#CreateModal").modal("show");
    $("#CreateModal").modal({
        backdrop: true,
        show: true,
    });
    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });

    for (i = 0; i < LargeModalStrings.length; i++) {
        if (url.includes(LargeModalStrings[i])) {
            $("#ModalCreateDialog").addClass("modal-lg");
            break;
        } else {
            $("#ModalCreateDialog").removeClass("modal-lg");
        }
    }

    $("#CreateTargetModal")
        .html(LoadingSpan)
        .load(url, function () {
            $("select.select2").select2({});
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]',
            });
        });
}

// For Creating product only
function createProductConfigs() {
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
    $("#IsOffer").is(":checked")
        ? $("#IsOffer").val("on")
        : $("#IsOffer").val("");
    $("#ProductWholesale").is(":checked")
        ? $("#ProductWholesale").val("on")
        : $("#ProductWholesale").val("");
}

$(document).on("submit", "#createForm", function (e) {
    e.preventDefault();
    let formBtn = $(this).find(":submit");
    let formData = new FormData(this);
    let formID = "#" + $(this).attr("id");
    let formUrl = $(this).attr("action");

    // Custom Configs for creating products
    if ($(this).attr("data-type") == "product") {
        createProductConfigs();
    }

    $.ajax({
        type: "POST",
        dataType: "json",
        url: formUrl,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $(".form-status").html(
                `<h6 class="text-muted"><i class="fas fa-circle-notch fa-spin"></i> ` +
                    "Processing" +
                    ` ...` +
                    `</h6>`
            );
            formBtn.prop("disabled", true);
        },
        success: function (data) {
            $(".form-status").html(
                `<h6 class="text-success"><i class="fas fa-check-circle"></i> ` +
                    "Done" +
                    `</h6>`
            );
            formBtn.prop("disabled", false);
            $("input, textarea", formID)
                .not(
                    ":input[type=button], :input[type=submit], :input[type=hidden], :input[type=reset]"
                )
                .val("");
            $(".dataTable").DataTable().ajax.reload();
        },
        error: function (data) {
            $(".form-status").html("");
            formBtn.prop("disabled", false);
            console.log(data);
            // $.each(xhr.responseJSON.errors, function(key, value) {
            $(".form-status").append(
                `<h6 class="text-danger"><i class="fas fa-exclamation-triangle"></i> ` +
                    (data.responseJSON ?? "Something went wrong") +
                    `</h6>`
            );
            // });
            formBtn.prop("disabled", false);
        },
    });
});

/**
 *
 * ===== Import Excel Script
 *
 */
$(document).on("submit", ".import-excel-form", function (e) {
    e.preventDefault();
    let formBtn = $(this).find(":submit");
    let formData = new FormData(this);
    let formID = "#" + $(this).attr("id");
    let formUrl = $(this).attr("action");
    $.ajax({
        type: "POST",
        dataType: "json",
        url: formUrl,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $(".import-status").html(
                `<h6 class="text-muted"><i class="fas fa-circle-notch fa-spin"></i> ` +
                    lang.creating +
                    `...` +
                    `</h6>`
            );
            formBtn.prop("disabled", true);
        },
        success: function (data) {
            $(".import-status").html(
                `<h6 class="text-success"><i class="fas fa-check-circle"></i> ` +
                    lang.created +
                    `</h6>`
            );
            formBtn.prop("disabled", false);
            $("input, textarea", formID)
                .not(
                    ":input[type=button], :input[type=submit], :input[type=hidden], :input[type=reset]"
                )
                .val("");
            $(".dataTable").DataTable().ajax.reload();
        },
        error: function (xhr) {
            $(".import-status").html("");
            formBtn.prop("disabled", false);
            $.each(xhr.responseJSON.errors, function (key, value) {
                $(".import-status").append(
                    `<h6 class="text-danger"><i class="fas fa-exclamation-triangle"></i> ` +
                        value[0] +
                        `</h6>`
                );
            });
            formBtn.prop("disabled", false);
        },
    });
});

/**
 *
 * ===== Edit Modal Section
 *
 */

function OpenEditModal(url) {
    // $("#EditModal").modal("show");
    $("#EditModal").modal({
        backdrop: true,
        show: true,
    });
    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });

    for (i = 0; i < LargeModalStrings.length; i++) {
        if (url.includes(LargeModalStrings[i])) {
            $("#ModalEditDialog").addClass("modal-lg");
            break;
        } else {
            $("#ModalEditDialog").removeClass("modal-lg");
        }
    }
    $("#EditTargetModal")
        .html(LoadingSpan)
        .load(url, function () {
            $("select.select2").select2({});
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]',
            });
        });
}

$(document).on("submit", "#editForm", function (e) {
    e.preventDefault();
    let formBtn = $(this).find(":submit");
    let formData = new FormData(this);
    let formID = "#" + $(this).attr("id");
    let formUrl = $(this).attr("action");

    $.ajax({
        type: "POST",
        dataType: "json",
        url: formUrl,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $(".form-status").html(
                `<h6 class="text-muted"><i class="fas fa-circle-notch fa-spin"></i> ` +
                    "lang.updating" +
                    ` ...` +
                    `</h6>`
            );
            formBtn.prop("disabled", true);
        },
        success: function (data) {
            $(".form-status").html(
                `<h6 class="text-success"><i class="fas fa-check-circle"></i> ` +
                    "lang.updated" +
                    `</h6>`
            );
            formBtn.prop("disabled", false);
            $(".dataTable").DataTable().ajax.reload();
        },
        error: function (xhr) {
            $(".form-status").html("");
            formBtn.prop("disabled", false);
            $.each(xhr.responseJSON.errors, function (key, value) {
                $(".form-status").append(
                    `<h6 class="text-danger"><i class="fas fa-exclamation-triangle"></i> ` +
                        value[0] +
                        `</h6>`
                );
            });
        },
    });
});
$("#EditModal").on("hidden.bs.modal", function () {
    $("#EditTargetModal").html("");
});
$("#CreateModal").on("hidden.bs.modal", function () {
    $("#CreateTargetModal").html("");
});

/**
 *
 * ===== Delete Function Section
 *
 */

function deleteRow(link, ModelDeleteType) {
    Swal.fire({
        title: "lang.are_you_sure",
        text: "lang.you_want_delete_this" + ModelDeleteType + " ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "lang.delete",
        cancelButtonText: "lang.cancel",
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: link,
                type: "DELETE",
                data: {
                    _token: CsrfToken,
                },
            })
                .done(function (data) {
                    if (data.status == 200) {
                        Swal.fire({
                            text: "lang.deleted_successfully",
                            confirmButtonText: "lang.ok",
                            type: "success",
                            toast: true,
                            position: "bottom",
                        });
                    } else {
                        Swal.fire({
                            text: data.response,
                            confirmButtonText: "lang.ok",
                            type: "error",
                            toast: true,
                            position: "bottom",
                        });
                    }
                    $(".dataTable").DataTable().ajax.reload();
                })
                .fail(function (data) {
                    Swal.fire({
                        text: data.response,
                        type: "error",
                        toast: true,
                        position: "bottom",
                    });
                });
        }
    });
}

$(document).on("click", ".delete-btn", function (event) {
    event.preventDefault();
    deleteRow($(this).attr("data-url"), $(this).attr("data-delete-type"));
});

// Branch change stock script
$(document).on("click", ".increment-stock", function () {
    let tr = $(this).closest("tr");
    let row = ProductStockTable.row(tr);

    if (row.child.isShown()) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass("shown");
    } else {
        // Open this row
        row.child(ChangeProductStock(row.data(), "+")).show();
        tr.addClass("shown");
    }
});

$(document).on("click", ".decrement-stock", function () {
    let tr = $(this).closest("tr");
    let row = ProductStockTable.row(tr);

    if (row.child.isShown()) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass("shown");
    } else {
        // Open this row
        row.child(ChangeProductStock(row.data(), "-")).show();
        tr.addClass("shown");
    }
});

// No space input
function disable_input_spaces(t) {
    if (t.value.match(/\s/g)) {
        t.value = t.value.replace(/\s/g, "");
    }
}

$(document).on("keypress", ".no-input-space", function (e) {
    if (e.which === 32) return false;

    if (!/[0-9a-zA-Z-]/.test(String.fromCharCode(e.which))) return false;
});

function hideProductSelector() {
    $("#CategorySelect, #SubCategorySelect, #ProductSelect").prop(
        "required",
        false
    );
    $("#RelatedProduct").hide();
}

// Choose expense type with product related
$(document).on("change", "#ExpenseType", function (e) {
    if ($(this).find(":selected").attr("data-product-related") == "1") {
        $("#CategorySelect, #SubCategorySelect, #ProductSelect").prop(
            "required",
            true
        );
        $("#RelatedProduct").show();
    } else {
        hideProductSelector();
    }
});

function moneyFormat(number) {
    let formatted = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        currencyDisplay: "code",
    })
        .format(number)
        .replace("USD", "")
        .trim();

    return formatted + " " + currency;
}

function generateUniqueProductBarcode(element) {
    $.ajax({
        type: "get",
        dataType: "json",
        url: `${url_generate_barcode}`,
        headers: headerParams,
        beforeSend: function () {},
        success: function (data) {
            if (data.success) {
                $(element).val(data.data);
            }
        },
    });
}

$(document).on("click", "#generateProductBarcode", function (e) {
    generateUniqueProductBarcode("#productBarcode");
});

function liveClock(element) {
    var span = $(element);
    var d = new Date();
    var s = d.getSeconds();
    var m = d.getMinutes();
    var hours = d.getHours();
    var ampm = hours >= 12 ? "pm" : "am";
    hours = hours % 12;
    hours = hours ? hours : 12;
    span.html(
        hours +
            ":" +
            ("0" + m).substr(-2) +
            ":" +
            ("0" + s).substr(-2) +
            ampm +
            " "
    );
}

function currentDate(date) {
    var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = "0" + month;
    if (day.length < 2) day = "0" + day;

    return [year, month, day].join("-");
}

document.onreadystatechange = function () {
    var state = document.readyState;
    if (state == "complete") {
        setTimeout(function () {
            $("#loader-content").fadeOut("0");
            $(".wrapper").fadeIn("0");
        }, 500);
    }
};

function stringToBarcode(string, format, elements, code_color) {
    var renderer = "svg";

    var settings = {
        output: renderer,
        color: code_color,
    };
    $.each(elements, function (i, element) {
        $(element).html("").show().barcode(string, format, settings);
    });
}

function stringToQRCode(string, size, elements, code_color) {
    const options = {
        render: "div",
        ecLevel: "H",
        minVersion: parseInt("6", 10),

        fill: code_color,
        background: "#ffffff",

        text: string,
        size: parseInt(size, 10),
        radius: parseInt("50", 10) * 0.01,
        quiet: parseInt("1", 10),

        mode: parseInt("2", 10),

        mSize: parseInt("11", 10) * 0.01,
        mPosX: parseInt("50", 10) * 0.01,
        mPosY: parseInt("50", 10) * 0.01,

        label: "jQuery.qrcode",
        fontname: "Ubuntu Mono",
        fontcolor: "#ff9818",

        image: null,
    };
    $.each(elements, function (i, element) {
        $(element).empty().qrcode(options);
    });
}

function filterTable(
    Route,
    TableID,
    from_date,
    to_date,
    init,
    cols,
    colDefs,
    dataExtra
) {
    // init
    if (init) {
        $(TableID).DataTable().destroy();
    }

    let data = {
        from_date: from_date ?? null,
        to_date: to_date ?? null,
        table: true,
    };
    if (dataExtra) {
        $.extend(data, dataExtra);
    }

    $(TableID).DataTable({
        order: [[0, "desc"]],
        processing: true,
        serverSide: true,
        columnDefs: colDefs ?? null,
        ajax: {
            url: Route,
            data: data,
        },
        columns: cols,
    });
}

$(document).on("submit", "#filterTable", function (e) {
    e.preventDefault();
    filterTable(
        route,
        tableID,
        $("#from_date").val(),
        $("#to_date").val(),
        true,
        cols
    );
});

$(document).on("click", "#duplicateBtn", function (e) {
    $("#mainContent:first-of-type").clone().appendTo("#mainContainer");
});

$(document).on("change", "#country_id", function (e) {
    let id = $(this).val();
    $.ajax({
        type: "GET",
        dataType: "json",
        url: `${url_get_cities}/${id}`,
        headers: headerParams,
        beforeSend: function () {
            $("#city_id").html("Loading...");
        },
        success: function (response) {
            $("#city_id").empty();
            $.each(response.data, function (i, city) {
                $("#city_id").append(
                    $("<option></option>").val(city.id).html(city.name)
                );
            });
            $("#city_id").prop("disabled", false);
        },
    });
});

$(document).on("click", ".open-create-modal, .open-edit-modal, .open-details-btn", function (e) {
    e.preventDefault();

    // Determine the modal ID based on the button's class
    let modalId;
    if ($(this).hasClass("open-create-modal")) {
        modalId = "createModal";
    } else if ($(this).hasClass("open-edit-modal")) {
        modalId = "editModal";
    } else if ($(this).hasClass("open-details-btn")) {
        modalId = "showModal";
    }

    // Get the data attributes from the button
    const url = $(this).data("modal-link");
    const title = $(this).data("modal-title");

    // Show the modal and set a loading message
    const $modal = $(`#${modalId}`);
    $modal.find(".modal-title").html(title);
    $modal.find(".modal-body").html("<p>Loading...</p>");
    $modal.modal("show");

    // Make the AJAX request
    $.ajax({
        url: url,
        method: "GET",
        success: function (response) {
            $modal.find(".modal-body").html(response);
            fireDependency();
        },
        error: function () {
            $modal
                .find(".modal-body")
                .html("<p>Failed to load content. Please try again.</p>");
        },
    });
});

$(document).on("change", ".upload-image", function (e) {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(e.target)
                .closest(".upload-image")
                .siblings(".preview-image")
                .attr("src", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    }
});

function fireDependency() {
    $(".form-toggle").bootstrapToggle();
    $(".select2").select2();
}
