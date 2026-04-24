
//  Up and Down Keyboard Buttons
$(document).keydown(function (event) {
    // Up Keyboard Button
    if (event.which == "38" && $(".item-on-active")[0]) {
        let CurrentActive = $(".item-on-active");
        if (CurrentActive.prev('tr').length > 0) {
            CurrentActive.prev().addClass('badge-primary item-on-active');
            CurrentActive.removeClass('badge-primary item-on-active');
            // Change Current Item Price
            $("#CurrentItemPrice").text(parseFloat($(".item-on-active").find('#itemPrice').text()) * parseFloat($(".item-on-active").find('#itemAmount').text()));
        } else {
            return false;
        }
    }
    // Down Keyboard Button
    if (event.which == "40" && $(".item-on-active")[0]) {
        let CurrentActive = $(".item-on-active");
        if (CurrentActive.next('tr').length > 0) {
            CurrentActive.next().addClass('badge-primary item-on-active');
            CurrentActive.removeClass('badge-primary item-on-active');
            // Change Current Item Price
            $("#CurrentItemPrice").text(parseFloat($(".item-on-active").find('#itemPrice').text()) * parseFloat($(".item-on-active").find('#itemAmount').text()));
        } else {
            return false;
        }
    }
});

//  Right Bottom Buttons
//  Right Bottom Buttons (Up)

// 5 / 10 / 30 / 50% Discount Buttons
$("button#offItem").on('click', function (event) {
    event.preventDefault();
    let OfferItem = $(this).attr('data-offer-item');
    Swal.fire({
        title: lang.set_offer + OfferItem + '% ?',
        imageUrl: assets_pricetag,
        imageSize: '512x512',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: lang.no,
        confirmButtonText: lang.yes,
    }).then((result) => {
        if (result.value) {
            $(".item-on-active #itemName").append(`<span class="font-weight-bold">` + ' ' + OfferItem + '% ' + lang.off + `</span>`);

            let OldPrice = parseFloat($(".item-on-active #itemAmount").text()) *
                parseFloat($(".item-on-active #itemPrice").text());

            let DiscountPrice = OldPrice * OfferItem / 100;
            let ItemAfterDiscount = OldPrice - DiscountPrice;
            let PriceAfterDiscount = parseFloat($(".item-on-active #itemPrice").text()) - parseFloat($(".item-on-active #itemPrice").text()) * OfferItem / 100;

            // Change Price
            $(".item-on-active #itemPrice").text(PriceAfterDiscount.toFixed(2));
            // Change Current Item Price
            $("#CurrentItemPrice, .item-on-active #itemTotal").text(ItemAfterDiscount.toFixed(2));
            // Change Total Price
            $("#TotalOrderPrice").text((parseFloat($("#TotalOrderPrice").text()) - OldPrice + ItemAfterDiscount).toFixed(2));
        }

    });
});
// 5 / 10 / 30 / 50% Total Items Discount Buttons
$("button#offAll").on('click', function (event) {
    event.preventDefault();
    let OfferItem = $(this).attr('data-offer-item');
    Swal.fire({
        title: lang.set_all_offer + OfferItem + '% ?',
        imageUrl: assets_discountall,
        imageSize: '512x512',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: lang.no,
        confirmButtonText: lang.yes,
    }).then((result) => {
        if (result.value) {
            $("td#itemName").append(`<span class="font-weight-bold">` +
                ' ' + OfferItem + '% ' + lang.off_all + `</span>`);

            // Change Current Item Price
            let OldPrice = parseFloat($(".item-on-active #itemAmount").text()) *
                parseFloat($(".item-on-active #itemPrice").text());

            let DiscountPrice = OldPrice * OfferItem / 100;
            let ItemAfterDiscount = OldPrice - DiscountPrice;

            $("#CurrentItemPrice, .item-on-active #itemTotal").text(ItemAfterDiscount.toFixed(2));
            // Change Total Price
            let OldTotalPrice = parseFloat($("#TotalOrderPrice").text());

            $("#TotalOrderPrice").text((OldTotalPrice * OfferItem / 100).toFixed(2));
        }

    });
});
// Free Item Button
$("button#freeItem").on('click', function (event) {
    event.preventDefault();
    Swal.fire({
        title: lang.set_free_item + ` ?`,
        imageUrl: assets_freeitem,
        imageSize: '512x512',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: lang.no,
        confirmButtonText: lang.yes,
    }).then((result) => {
        if (result.value) {
            $(".item-on-active #itemName").append(`<span class="font-weight-bold"> ` + lang.free_item + ` </span>`);

            let OldPrice = parseFloat($(".item-on-active #itemAmount").text()) *
                parseFloat($(".item-on-active #itemPrice").text());

            // Change Price
            $(".item-on-active #itemPrice").text('0');
            // Change Current Item Price
            $("#CurrentItemPrice, .item-on-active #itemTotal").text('0');
            // Change Total Price
            $("#TotalOrderPrice").text((parseFloat($("#TotalOrderPrice").text()) - OldPrice).toFixed(2));
        }

    });
});
// Custom Discount Button
$(document).on('click', '#customDiscount', function (event) {
    let CustomBtn = $(this);
    event.preventDefault();
    Swal.fire({
        title: lang.set_custom_discount + ` ?`,
        imageUrl: assets_customdiscount,
        imageSize: '512x512',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: lang.no,
        confirmButtonText: lang.yes,
    }).then((result) => {
        if (result.value) {

            CustomBtn.replaceWith(`<input value="" id="customDiscountInput" min="1" max="100" type="number" autofocus/>`);
            $("#customDiscountInput").focus();

            $(document).keydown(function (event) {
                if (event.which == "13" && $("#customDiscountInput")[0]) {
                    let CustomDiscount = $("#customDiscountInput").val();
                    // Set up the discount in the active product

                    $(".item-on-active #itemName").append(`<span class="font-weight-bold">` + ' ' + CustomDiscount + '% ' + lang.off + `</span>`);

                    let OldPrice = parseFloat($(".item-on-active #itemAmount").text()) *
                        parseFloat($(".item-on-active #itemPrice").text());

                    let DiscountPrice = OldPrice * CustomDiscount / 100;
                    let ItemAfterDiscount = OldPrice - DiscountPrice;
                    let PriceAfterDiscount = parseFloat($(".item-on-active #itemPrice").text()) - parseFloat($(".item-on-active #itemPrice").text()) * CustomDiscount / 100;

                    // Change Price
                    $(".item-on-active #itemPrice").text(PriceAfterDiscount.toFixed(2));
                    // Change Current Item Price
                    $("#CurrentItemPrice, .item-on-active #itemTotal").text(ItemAfterDiscount.toFixed(2));
                    // Change Total Price
                    $("#TotalOrderPrice").text((parseFloat($("#TotalOrderPrice").text()) - OldPrice + ItemAfterDiscount).toFixed(2));

                    // Bring back the button
                    $("#customDiscountInput").replaceWith(`<button class="btn btn-info" id="customDiscount">` + lang.custom_discount + `</button>`);
                    $('#barcodeNumber').focus();
                }
            });
        }
    });
});


//  Left Bottom Buttons
//  Left Bottom Buttons (Up)

// Cancel Item
$("#cancelItem").on('click', function () {
    Swal.fire({
        title: lang.cancel_item,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: lang.no,
        confirmButtonText: lang.yes
    }).then(function (isConfirm) {
        if (isConfirm) {
            // Miuns the price from the total
            let totalPrice = $("#TotalOrderPrice").text();
            let canceledItemPriceAmount = parseFloat($(".item-on-active").find('#itemPrice').text()) * parseFloat($(".item-on-active #itemAmount").text());
            $("#TotalOrderPrice").text((parseFloat(totalPrice) - parseFloat(canceledItemPriceAmount)).toFixed(2));
            // Delete Raw from the table and select active last item
            $(".item-on-active").remove();
            $("#itemsTRs").find('tr:last-child').addClass('badge-primary item-on-active')

        }
    });
});

// Change Price
function changePrice() {
    $(".item-on-active #itemPrice").html(`<input value="" data-before-input="` + $(".item-on-active #itemPrice").text() + `" id="changePriceInput" min="0" type="number" autofocus/>`);
    $("#changePriceInput").focus();
}

$("#changePrice").on('click', function () {
    changePrice();
});
// After Change the Price press enter
$(document).keydown(function (event) {
    if (event.which == "13" && $("#changePriceInput")[0]) {
        if (!$("#changePriceInput").val()) {
            $("#changePriceInput").val($("#changePriceInput").attr('data-before-input'))
        }
        let totalPrice = $("#TotalOrderPrice").text();

        let OldPrice = parseFloat($("#changePriceInput").attr('data-before-input')) * parseFloat($(".item-on-active #itemAmount").text());

        let PriceAfterChangePrice = parseFloat($("#changePriceInput").val()) * parseFloat($(".item-on-active #itemAmount").text());

        $("#TotalOrderPrice").text((parseFloat(totalPrice) - parseFloat(OldPrice) + parseFloat(PriceAfterChangePrice)).toFixed(2));


        // Current Item Price
        $("#CurrentItemPrice, .item-on-active #itemTotal").text((PriceAfterChangePrice).toFixed(2));
        // Remove input and set the value in the price cell
        $(".item-on-active #itemPrice").html($("#changePriceInput").val());
        $('#barcodeNumber').focus();
    }
});
// Cancel All Items
$("#cancelAll").on('click', function () {
    Swal.fire({
        title: lang.do_cancel_all,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: lang.no,
        confirmButtonText: lang.yes_delete_all
    }).then(function (isConfirm) {
        if (isConfirm) {
            // Set the Total Price 0
            $("#TotalOrderPrice").text('0');
            // Set the current item 0
            $("#CurrentItemPrice").text('0');
            // Delete Raws from the table
            $("#itemsTRs tr").remove('');
            // Remove Delivery Data if exist
            $("#DeliveryData").html('');
            // set the no of items to 0
            $("#ItemsCounter").text('0');

            $('#barcodeNumber').focus();

        }
    });
});

//  Left Bottom Buttons (Under)
// Display Button - Model (Name + Descirption + Price*1 + Image)
$("#displayBtn").on('click', function () {
    let Modelimg = assets_productimg_path + '/' + $(".item-on-active").attr('data-product-image');
    $("#models").html(`
            <div class="modal fade" id="displayModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">` + lang.product + `</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="ItemDescription">
                        <h5>` + lang.product_title + '|' + $(".item-on-active #itemName").text() + `</h5>
                        <h6>` + lang.description + ':' + $(".item-on-active").attr('data-product-description') + `</h6>
                        <h6 class="text-success font-weight-bold">` + lang.price_item + '|' + $(".item-on-active").find('#itemPrice').text() + `* 1</h6>
                        <img src="` + Modelimg + `" class="model-image">
                        </div>
                    </div>
                </div>
            </div>
            `);
});

// Change Amount
function changeAmount() {
    $(".item-on-active #itemAmount").html(`<input value="" id="changeAmountInput" data-before-input="` + $(".item-on-active #itemAmount").text() + ` min="1" type="number" autofocus/>`);
    $("#changeAmountInput").focus();
}

// After Change the amount press enter
$(document).keydown(function (event) {
    if (event.which == "13" && $("#changeAmountInput")[0]) {
        if (!$("#changeAmountInput").val()) {
            $("#changeAmountInput").val(1)
        }

        let totalPrice = $("#TotalOrderPrice").text();

        let OldPrice = parseFloat($("#changeAmountInput").attr('data-before-input')) * parseFloat($(".item-on-active #itemPrice").text());

        let OldItemTotal = parseFloat($(".item-on-active #itemTotal").text());

        let PriceAfterAmount = (parseFloat($(".item-on-active #itemPrice").text()) * parseFloat($("#changeAmountInput").val()));

        // Change Total Order Price
        $("#TotalOrderPrice").text((parseFloat(totalPrice) - parseFloat(OldPrice) + PriceAfterAmount).toFixed(2));
        // Change Current item price + Total item price
        $("#CurrentItemPrice, .item-on-active #itemTotal").text((parseFloat($(".item-on-active").find("#itemPrice").text()) * parseFloat($("#changeAmountInput").val())).toFixed(2));
        // Remove input and set the value in the amount cell
        $(".item-on-active #itemAmount").html($("#changeAmountInput").val());
        $('#barcodeNumber').focus();
    }
});


//  if passing $turbo_mode show turbo mode script else show defualt script
if (if_turbo_mode == true) {
//  Search by barcode [Turbo Mode]
    function searchByBarcode() {
        var barcode_number = $("#barcodeNumber").val();
        var NumProducts = $('#itemsTRs').children().length + 1;

        let ProductData = $("span#TurboProduct[data-product-barcode='" + barcode_number + "']");

        if (ProductData.length == 0) {
            $(".barcode-status").html(`<i class="fas fa-exclamation-circle"></i> ` + lang.no_data_found);
        } else {
            $(".barcode-status").html(``);
            $('#barcodeNumber').val('');
            $("tr#itemTR").removeClass('item-on-active').removeClass('badge-primary');
            // Get Current Item Price
            var CurrentProductPrice = parseFloat(ProductData.attr('data-product-price'));

            $("#CurrentItemPrice").text(CurrentProductPrice.toFixed(2));
            // Get All Total Items Price
            let totalPrice = $("#TotalOrderPrice").text();
            $("#TotalOrderPrice").html((parseFloat(totalPrice) + CurrentProductPrice).toFixed(2));
            // Add Item To Table
            $("#itemsTRs").append(`<tr id="itemTR" class="badge-primary item-on-active"
data-product-price="` + CurrentProductPrice.toFixed(2) + `"
data-product-description="` + ProductData.attr('data-product-description') + `"
data-product-image="` + ProductData.attr('data-product-image') + `"
data-product-id="` + ProductData.attr('data-product-id') + `"
data-product-stock="` + ProductData.attr('data-product-stock') + `"
>
                 <td id="ProductID" style="display:none;">` + ProductData.attr('data-product-id') + `</td><td>` + NumProducts++ + `</td><td id="itemName">` + ProductData.attr('data-product-name') + `</td><td id="itemAmount">1</td><td id="itemPrice">` + CurrentProductPrice.toFixed(2) + `</td><td id="itemTotal">` + CurrentProductPrice.toFixed(2) + `</td></tr>`);


            // Scroll bottom Table
            ScrollTableBottom();
        }
    }

} else {
//  Search by barcode [Default Mode]
    function searchByBarcode() {
        var id = $('#barcodeNumber').val();
        var NumProducts = $('#itemsTRs').children().length + 1;
        $.get({
            type: "get",
            dataType: 'json',
            url:  `${url_get_product}/${id}`,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                Authorization: "Bearer " + localStorage.getItem("auth_token"),
                type:"barcode"
            },
            beforeSend: function () {
                //$(".barcode-status").html(`<i class="fas fa-circle-notch fa-spin"></i> ` + lang.barcode_searching    );
            }, success: function (data) {
                if (data === 'empty') {
                    $(".barcode-status").html(`<i class="fas fa-exclamation-circle"></i> ` + lang.no_data_found);
                } else {
                    $(".barcode-status").html(``);
                    $('#barcodeNumber').val('');
                    $("tr#itemTR").removeClass('item-on-active').removeClass('badge-primary');
                    // Get Current Item Price
                    var CurrentProductPrice = parseFloat(data.sale_price);
                    $("#CurrentItemPrice").text(CurrentProductPrice.toFixed(2));
                    // Get All Total Items Price
                    let totalPrice = $("#TotalOrderPrice").text();
                    $("#TotalOrderPrice").html((parseFloat(totalPrice) + CurrentProductPrice).toFixed(2));
                    // Add Item To Table
                    $("#itemsTRs").append(`<tr id="itemTR" class="badge-primary item-on-active"
data-product-price="` + CurrentProductPrice.toFixed(2) + `"
data-product-description="` + data.description + `"
data-product-image="` + data.image + `"
data-product-id="` + data.id + `"
data-product-stock="` + data.stock + `"
>
                         <td id="ProductID" style="display:none;">` + data.id + `</td><td>` + NumProducts++ + `</td><td id="itemName">` + data.name + `</td><td id="itemAmount">1</td><td id="itemPrice">` + CurrentProductPrice.toFixed(2) + `</td><td id="itemTotal">` + CurrentProductPrice.toFixed(2) + `</td></tr>`);

                }
                // Scroll bottom Table
                ScrollTableBottom();
            },
            error: function () {
                $(".barcode-status").html(``);
            }
        });
    }
}
//  Search by Category Select

$('#CategorySelect').on('input', function () {
    var id = $(this).val();
    $.ajax({
        type: "get",
        dataType: 'json',
        url:  `${url_search_category_get}/${id}`,
        headers: headerParams,
        beforeSend: function () {
            $(".barcode-status").html(`<i class="fas fa-circle-notch fa-spin"></i> ` + lang.barcode_searching);
        }, success: function (data) {

            if (data === 'empty') {
                $(".barcode-status").html(`<i class="fas fa-exclamation-circle"></i> ` + lang.no_data_found);
                $('#barcodeNumber').val('');
                $("#ProductSelect").html(`<option value="">` + lang.choose + ' ' +lang.product + `</option>`);
                $("#ProductSelect").prop('disabled', true);


            } else {
                $("#ProductSelect").prop('disabled', false);

                $("#ProductSelect").html('');
                $("#ProductSelect").append(`<option value="0">` + lang.choose + ' ' + lang.product + `</option>`);

                $.each(data, function (dataMy) {
                    $("#ProductSelect").append("<option value='" + data[dataMy].id + "'>" + data[dataMy].name + "</option>");
                });

                $(".barcode-status").html(``);
                $('#barcodeNumber').val('');


            }
            // Scroll bottom Table
            ScrollTableBottom();
        }
    });

    // Get Sub Categories
    $.ajax({
        type: "get",
        dataType: 'json',
        url:  `${url_search_category_get_sub}/${id}`,
        headers: headerParams,
        beforeSend: function () {
        }, success: function (data) {
            if (data === 'empty') {
                $("#SubCategory").html(`<option value="">` + lang.choose + ' ' + lang.sub_category + `</option>`);
                $("#SubCategory").prop('disabled', true);
            } else {
                $("#SubCategory").html(`<option value="">` + lang.choose + ' ' + lang.sub_category + `</option>`).prop('disabled', false);

                $.each(data, function (dataMy) {
                    $("#SubCategory").append("<option value='" + data[dataMy].id + "'>" + data[dataMy].name + "</option>");
                });
            }
        }
    })
    ;
});

//  Search By Sub Categories
$("#SubCategory").on("change", function () {
    let id = $(this).val();
    $.ajax({
        type: "get",
        dataType: 'json',
        url:  `${url_search_sub_get_product}/${id}`,
        headers: headerParams,
        beforeSend: function () {
            $(".barcode-status").html(`<i class="fas fa-circle-notch fa-spin"></i> ` + lang.barcode_searching);
        },
        success: function (data) {
            $(".barcode-status").html('');
            if (data === 'empty') {
                $("#ProductSelect").html(`<option value="">` + lang.choose + ' ' + lang.product + `</option>`);
                $("#ProductSelect").prop('disabled', true);

            } else {
                $("#ProductSelect").prop('disabled', false);

                $("#ProductSelect").html('');
                $("#ProductSelect").append(`<option value="0">` + lang.choose + ' ' + lang.product + `</option>`);

                $.each(data, function (dataMy) {
                    $("#ProductSelect").append("<option data-tags='"+(data[dataMy].tag_id ?? '')+"' value='" + data[dataMy].id + "'>" + data[dataMy].name  + ' ' + (data[dataMy].tag_name ?? '') + "</option>");
                });
            }
        }
    })
    ;

});


//  Search by Product Select

$('#ProductSelect').on('change', function () {
    var id = $(this).val();
    var NumProducts = $('#itemsTRs').children().length + 1;


    let ProductSelectName = $("#ProductSelect option:selected").text();
    let ProductSelectTags = $("#ProductSelect option:selected").attr('data-tags');

    $.ajax({
        type: "get",
        dataType: 'json',
        url:  `${url_search_product_get}/${id}`,
        headers: headerParams,
        beforeSend: function () {
            $(".barcode-status").html(`<i class="fas fa-circle-notch fa-spin"></i> ` + lang.barcode_searching);
        },
        success: function (data) {
            if (data === 'empty') {
                $(".barcode-status").html(`<i class="fas fa-exclamation-circle"></i> ` + lang.no_data_found);
                $('#barcodeNumber').val('');
                $("#barcodeNumber").focus();


            } else {
                $(".barcode-status").html(``);
                $('#barcodeNumber').val('');
                $("tr#itemTR").removeClass('item-on-active').removeClass('badge-primary');
                var CurrentProductPrice = parseFloat(data.sale_price);

                // Get Current Item Price
                $("#CurrentItemPrice").text(CurrentProductPrice.toFixed(2));
                // Get All Total Items Price
                let totalPrice = $("#TotalOrderPrice").text();
                $("#TotalOrderPrice").text((parseFloat(totalPrice) + CurrentProductPrice).toFixed(2));
                // Add Item To Table
                $("#itemsTRs").append(`<tr id="itemTR" class="badge-primary item-on-active"
data-product-price="` + CurrentProductPrice.toFixed(2) + `"
data-product-description="` + data.description + `"
data-product-image="` + data.image + `"
data-product-id="` + data.id + `"
data-product-stock="` + data.stock + `"
>
                         <td id="ProductID" style="display:none;">` + data.id + `</td><td>` + NumProducts++ + `</td><td id="itemName">` + ProductSelectName + `</td><td id="itemAmount">1</td><td id="itemPrice">` + CurrentProductPrice.toFixed(2) + `</td><td id="itemTotal">` + CurrentProductPrice.toFixed(2) + `</td><td id="ProductTags" style="display:none;">${ProductSelectTags}</td></tr>`);
                $("#barcodeNumber").focus();
                // Scroll bottom Table
                ScrollTableBottom();
            }
        }
    })
    ;
});

//  Search by Product Click

$('div.most-product').on('click', function () {
    var id = $(this).data('product-id');
    var NumProducts = $('#itemsTRs').children().length + 1;
    $.ajax({
        type: "get",
        dataType: 'json',
        url:  `${url_search_product_get}/${id}`,
        headers: headerParams,
        beforeSend: function () {
            $(".barcode-status").html(`<i class="fas fa-circle-notch fa-spin"></i> ` + lang.barcode_searching);
        },
        success: function (data) {
            if (data === 'empty') {
                $(".barcode-status").html(`<i class="fas fa-exclamation-circle"></i> ` + lang.no_data_found);
                $('#barcodeNumber').val('');
                $("#barcodeNumber").focus();


            } else {
                $(".barcode-status").html(``);
                $('#barcodeNumber').val('');
                $("tr#itemTR").removeClass('item-on-active').removeClass('badge-primary');
                var CurrentProductPrice = parseFloat(data.sale_price);

                // Get Current Item Price
                $("#CurrentItemPrice").text(CurrentProductPrice.toFixed(2));
                // Get All Total Items Price
                let totalPrice = $("#TotalOrderPrice").text();
                $("#TotalOrderPrice").text((parseFloat(totalPrice) + CurrentProductPrice).toFixed(2));
                // Add Item To Table
                $("#itemsTRs").append(`<tr id="itemTR" class="badge-primary item-on-active"
data-product-price="` + CurrentProductPrice.toFixed(2) + `"
data-product-description="` + data.description + `"
data-product-image="` + data.image + `"
data-product-id="` + data.id + `"
data-product-stock="` + data.stock + `"
>
                         <td id="ProductID" style="display:none;">` + data.id + `</td><td>` + NumProducts++ + `</td><td id="itemName">` + data.name + `</td><td id="itemAmount">1</td><td id="itemPrice">` + CurrentProductPrice.toFixed(2) + `</td><td id="itemTotal">` + CurrentProductPrice.toFixed(2) + `</td></tr>`);
                // Barcode Number Focus
                $("#barcodeNumber").focus();
                // Scroll bottom Table
                ScrollTableBottom();
            }
        }
    })
    ;
});

//  Pay Order
function payOrder() {
    if ($('#itemsTRs').children().length == '0') {
        return false;
    } else {
        // Get All Money Data
        let RemainAmount = parseFloat(parseFloat($('input[name ="amount_paid"]').val())
            - parseFloat($("#TotalOrderPrice").text())).toFixed(2);
        let amount_paid = parseFloat($('input[name ="amount_paid"]').val());
        let total_price = parseFloat($("#TotalOrderPrice").text());

        // Payment Method (Cash - Credit - Debit)
        var pay_method = "";
        var transaction_number = null;
        if ($("#CreditCard").attr("data-pay-use") === "1") {
            var pay_method = "Credit Card";
            var transaction_number = $("#TransactionCredit").val();
            RemainAmount = 0;
            amount_paid = parseFloat($("#TotalOrderPrice").text()).toFixed(2);
            // if order credit and the transaction number empty show error
            if ($("#TransactionCredit").val().length === 0) {
                Swal.fire({
                    title: lang.please_transaction_number + ' !',
                    type: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: lang.submit,
                    allowOutsideClick: false
                });
                return false;
            }
        } else if ($("#DebitCard").attr("data-pay-use") === "1") {
            var pay_method = "Debit Card";
            var transaction_number = $("#TransactionDebit").val();
            RemainAmount = 0;
            amount_paid = parseFloat($("#TotalOrderPrice").text()).toFixed(2);
            if ($("#TransactionDebit").val().length === 0) {
                Swal.fire({
                    title: lang.please_transaction_number + ' !',
                    type: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: lang.submit,
                    allowOutsideClick: false
                });
                return false;
            }
        } else {
            var pay_method = "Cash";
            if ($("#result").val().length == 0) {
                Swal.fire({
                    title: lang.please_fill_amount_paid + ' !',
                    type: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: lang.submit,
                    allowOutsideClick: false
                });
                return false;
            }
        }

        // Delivery Data if exist
        var full_name = '', phone_number = '', address = '', delivery_man = '', delivery_fee = '';
        if (!$('#DeliveryData').is(':empty')) {
            var full_name = $("#ClientFullName").val(),
                phone_number = $("#ClientPhone").val(),
                address = $("#ClientAddress").val(),
                delivery_man = $("#DeliveryMan").val();
            delivery_fee = $("#DeliveryFee").val();

        }

        // Client Order if exist
        var client_code = '';
        if (!$("#ClientData").is(':empty')) {
            $("#OrderClientName").html(lang.client + ':' +ClientName);
            var client_code = ClientCode;
        }

        // Get All products in the table to array
        let ProductsArray = Array();
        $("#OrderTable tr").each(function (i, v) {
            ProductsArray[i] = Array();
            $(this).children('td:not(#itemName)').each(function (ii, vv) {
                ProductsArray[i][ii] = $(this).text();
            });
        });

        // Generate Barcode on the receipt
        generateBarcode();
        let barcode = $("#barcodeTarget div:last-child").text();

        Swal.fire({
            title: lang.order_done,
            imageUrl: assets_order,
            html: `<div class="text-success font-weight-bold">` +
                lang.remaining_amount + ':' + RemainAmount +
                `</div>
                    <div class="text-danger flash-text font-weight-bold">` + lang.close_drawer + `</div>
                    <div class="font-weight-bold" id="OrderStatus"></div>`,
            confirmButtonColor: '#3085d6',
            confirmButtonText: lang.submit,
            allowOutsideClick: false
        }).then(function () {
            $("#barcodeNumber").focus();
            $(".anotherFullScreen").show();
            setTimeout(function () {
                $("#barcodeNumber").focus();
            }, 1000);

        });

        $.ajax({
            type: "POST",
            url: url_orders_store,
            data: {
                '_token': $('meta[name="_token"]').attr('content'),
                total_price: total_price,
                amount_paid: amount_paid,
                products: ProductsArray,
                barcode: barcode,
                pay_method: pay_method,
                full_name: full_name,
                phone_number: phone_number,
                address: address,
                delivery_man: delivery_man,
                delivery_fee: delivery_fee,
                client_code: client_code,
                transaction_number: transaction_number
            },
            beforeSend: function (wait) {
                $("#OrderStatus").html(`<span class="text-muted"><i class="fas fa-spinner fa-pulse"></i> `
                    + lang.transferring_order + '</span>');
            }, success: function (response) {
                if (response === 'VALID') {
                    $("#OrderStatus").html(`<span class="text-success"><i class="fas fa-check-circle"></i> `
                        + lang.order_transferred + '</span>');
                    $(".subtotal-price").html(total_price);
                    $(".amount-paid").html(amount_paid);
                    $(".remaining-amount").html(amount_paid - total_price);

                    $("#ReceiptTable").html($("#OrderTable").clone());

                    $("#ReceiptTable #OrderTable tbody,#ReceiptTable #OrderTable tbody tr,#ReceiptTable #OrderTable tbody tr td")
                        .removeAttr('id').removeClass('badge-primary item-on-active');

                    $("#ReceiptTable #OrderTable").removeClass().removeAttr('id');
                    // If Delivery Data Exists
                    if (!$('#DeliveryData').is(':empty')) {
                        $("#ReceiptFullName").html(lang.full_name + ':' + $("#ClientFullName").val());
                        $("#ReceiptPhone").html(lang.phone + ':' + $("#ClientPhone").val());
                        $("#ReceiptAddress").html(lang.address + ':' + $("#ClientAddress").val());
                        $("#ReceiptDeliveryFee").html(lang.delivery_fee + ':' + $("#DeliveryFee").val());
                    } else {
                        $("#ReceiptFullName").html('');
                        $("#ReceiptPhone").html('');
                        $("#ReceiptAddress").html('');
                        $("#ReceiptDeliveryFee").html('');

                    }

                    if (!$("#ClientData").is(':empty')) {
                        $("#OrderClientName").html(lang.client + ':' + ClientName);
                    } else {
                        $("#OrderClientName").html("");
                    }

                    // put the no of items to receipt
                    $(".no-items").text($("#ItemsCounter").text());

                    // Print the receipt
                    // $('#Final').printThis();
                    $(".anotherFullScreen").hide();
                    $("body").removeClass('swal2-shown swal2-height-auto');
                    window.print();

                    // Set the Total Price 0
                    $("#TotalOrderPrice").text('0');
                    $("#result").val('');
                    // Set the current item 0
                    $("#CurrentItemPrice").text('0');
                    // Delete Raws from the table
                    $("#itemsTRs tr").remove('');
                    // Remove Uses of Credit and Debit
                    $("#CreditCard, #DebitCard").attr("data-pay-use", '0').css("background-color", '#17a2b8');
                    // Remove Delivery Data if exist
                    $("#DeliveryData").html('');
                    // Remove Transaction number Data if exist
                    $("#TransactionCredit").val('');
                    $("#TransactionDebit").val('');
                    // Remove Client Data if exist
                    $("#ClientData").html('');
                    // set the no of items to 0
                    $("#ItemsCounter").text('0');
                    // Increase Cashier Order Counter
                    let OrderCounter = $("#OrderCounter").text();
                    $("#OrderCounter").html(parseInt(OrderCounter) + 1);
                    localStorage.setItem('order_number', parseInt(localStorage.getItem('order_number')) + 1)
                    localStorage.setItem('last_order', DateNow)

                    $('.swal2-confirm').focus();
                } else {
                    $("#OrderStatus").html(`<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> `
                        + lang.order_not_transferred + '</span>');
                }
            }
        });
    }
}

// On click PayOrder Button
$("#PayOrder").on('click', function () {
    payOrder();
});

//  Cancel Order
function cancelOrder() {
    Swal.fire({
        title: lang.cancel_order,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: lang.no,
        confirmButtonText: lang.yes
    }).then(function (isConfirm) {
        if (isConfirm) {
            // Set the Total Price 0
            $("#TotalOrderPrice").text('0');
            $("#result").val('');
            // Set the current item 0
            $("#CurrentItemPrice").text('0');
            // Delete Raws from the table
            $("#itemsTRs tr").remove('');
            // Remove Delivery Data if exist
            $("#DeliveryData").html('');
            // Remove Client Data if exist
            $("#ClientData").html('');
            // set the no of items to 0
            $("#ItemsCounter").text('0');
        }
    });
}

$("#CancelOrder").on('click', function () {
    cancelOrder();
});


//  Print Last Receipt

function PrintLastReceipt() {
    $.ajax({
        type: "get",
        dataType: 'json',
        url:  `${url_last_receipt}`,
        headers: headerParams,
        success: function (data) {
            if (data === 'empty') {
                Swal.fire({
                    title: lang.no_last_receipt,
                    imageUrl: assets_shredder,
                    html: ``,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: lang.submit,
                });
            } else {
                Swal.fire({
                    imageUrl: assets_bill,
                    html: `<h4><i class="fas fa-cog fa-spin"></i> ` + lang.printing_last_receipt + `</h4>`,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: lang.submit,
                });
                $(".subtotal-price").html(data[0].total_price);
                $(".amount-paid").html(data[0].amount_paid);
                $(".remaining-amount").html(data[0].total_price - data[0].amount_paid);

                var value = data[0].barcode;
                +(Math.random() + '').substring(2, 9);
                var btype = "ean13";
                var renderer = "css";

                var settings = {
                    output: renderer,
                    bgColor: $("#bgColor").val(),
                    color: $("#color").val(),
                    barWidth: $("#barWidth").val(),
                    barHeight: $("#barHeight").val(),
                    moduleSize: $("#moduleSize").val(),
                    posX: $("#posX").val(),
                    posY: $("#posY").val(),
                    addQuietZone: $("#quietZoneSize").val()
                };
                if ($("#rectangular").is(':checked') || $("#rectangular").attr('checked')) {
                    value = {code: value, rect: true};
                }

                if (renderer == 'canvas') {
                    clearCanvas();
                    $("#barcodeTarget").hide();
                    $("#canvasTarget").show().barcode(value, btype, settings);
                } else {
                    $("#canvasTarget").hide();
                    $("#barcodeTarget").html("").show().barcode(value, btype, settings);
                }


                $("#ReceiptTable").html(`<table><thead><tr><th > # </th><th>` + lang.r_item + `</th><th> ` + lang.amount + `</th><th>` + lang.price + `</th><th>` + lang.total + ` </th></tr></thead>` + `<tbody id="LastTableData"></tbody></table>`);

                // Foreach order table in receipt
                $.each(data[0].products, function (i, item) {
                    $("#LastTableData").append(`<tr>
                            <td style="display:none;">` + item[0] + `</td><td>` + item[1] + `</td><td>` + data[1][i - 1] + `</td><td>` + item[2] + `</td><td>` + item[3] + `</td><td>` + item[4] + `</td></tr>`);
                });

                var sum = 0;
                $('#ReceiptTable tbody tr td:nth-child(4)').each(function () {
                    sum += parseFloat($(this).text());
                    $(".no-items").text(Math.ceil(sum));

                });

                // Print now
                // $('#Final').printThis();
                $("body").removeClass('swal2-shown swal2-height-auto');
                window.print();
            }
        }
    });

}

$("#PrintLast").on('click', function () {
    PrintLastReceipt();
});

// Focus Calculator
function showCalculator() {
    $("#result").focus();
}

//  Gift Card

$(document).on('click', '#GiftCard', function (event) {
    let GiftCardBtn = $(this);
    event.preventDefault();
    GiftCardBtn.replaceWith(`<input value="" id="GiftCardInput" type="number" autofocus/>`);
    $("#GiftCardInput").focus();

    $(document).keydown(function (event) {
        if (event.which == "13" && $("#GiftCardInput")[0]) {
            let code = $("#GiftCardInput").val();
            $.ajax({
                type: "get",
                dataType: 'json',
                url:  `${url_get_gift}/${code}`,
                headers: headerParams,
                beforeSend: function () {
                },
                success: function (data) {
                    if (data === 'empty') {
                        Swal.fire({
                            title: lang.no_gift_card,
                            type: 'error',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: lang.submit
                        });
                    } else {
                        Swal.fire({
                            title: lang.gift_card_found_with + data + ' ' + currency,
                            type: 'success',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: lang.submit
                        });
                        // Add Gift Card to the table
                        $("#itemsTRs").append(`<tr id="itemTR">
                         <td id="ProductID" style="display:none;">G</td><td>*</td><td id="itemName">-` + data + currency + lang.gift_card + `</td><td id="itemAmount">1</td><td id="itemPrice">-` + data + currency + `</td></tr>`);
                        // Change Total Price
                        $("#TotalOrderPrice").text(parseFloat($("#TotalOrderPrice").text()) - data);
                    }
                }
            });

            // Bring back the button
            $("#GiftCardInput").replaceWith(`<button class="btn btn-info" id="GiftCard">` + lang.gift_card + `</button>`);

            $('#barcodeNumber').focus();
        }
    });
});

//  Save Draft

$("#SaveDraft").on('click', function () {
    if ($('#itemsTRs').children().length == '0') {
        return false;
    } else {
        let btn = $(this);

        let total_price = parseFloat($("#TotalOrderPrice").text());
        let ProductsArray = Array();
        $("#OrderTable tr").each(function (i, v) {
            ProductsArray[i] = Array();
            $(this).children('td:not(#itemName)').each(function (ii, vv) {
                ProductsArray[i][ii] = $(this).text();
            });
        });
        $.ajax({
            type: "POST",
            url: url_drafts_store,
            data: {
                '_token': $('meta[name="_token"]').attr('content'),
                total_price: total_price,
                products: ProductsArray,
            },
            beforeSend: function (wait) {
                btn.html(`<i class="fas fa-spinner fa-pulse"></i> ` + lang.saving + '...');
            }, success: function (response) {
                if (response === 'VALID') {
                    // Set the Total Price 0
                    $("#TotalOrderPrice").text('0');
                    $("#result").val('');
                    // Set the current item 0
                    $("#CurrentItemPrice").text('0');
                    // Delete Raws from the table
                    $("#itemsTRs tr").remove('');
                    // Remove Delivery Data if exist
                    $("#DeliveryData").html('');
                    // set the no of items to 0
                    $("#ItemsCounter").text('0');
                    // Bring back btn text
                    btn.html(`<i class="fas fa-save"></i> ` + lang.save_draft);
                } else {
                    btn.html(`<i class="fas fa-exclamation-triangle"> ` + lang.error);
                }
            }
        });
    }
});

//  Open Drafts

$("#OpenDrafts").on('click', function () {
    $("#drafts").css('width', "500px");
    $("#drafts #loader").show();
    $.ajax({
        type: "get",
        dataType: 'json',
        url:  `${url_get_drafts}`,
        headers: headerParams,
        success: function (data) {
            $("#drafts #loader").fadeOut("200");
            if (data === 'empty') {
                $("#drafts-section").html(`<div class="text-center font-weight-bold text-success" style="font-size:17px">` + `<i class="fas fa-clipboard-check"></i> ` + lang.no_drafts_found + `</div>`);
            } else {
                var xy = -1;
                $.each(data[0], function (i, draft) {
                    let DraftPrice = draft.total_price;
                    let TableID = `DraftProducts` + (Math.random() + '').substring(2, 8);


                    $("#DraftOne").append(`<div class="timeline">
                                <div class="time-label"><span class="bg-primary">` + draft.created_at + `</span></div>
                                <div>
                                    <i class="fas fa-clipboard-list bg-primary"></i>
                                    <div class="timeline-item">
                                        <span class="total-price"><i class="far fa-money-bill-alt"></i>` + lang.total_price + `<span id="DraftPrice">` + DraftPrice + `</span>` + ' ' + currency + `</span>
                <div class="timeline-body" >
                    <table style = "font-size: 13px;" >
                    <thead>
                    <tr>
                    <th>#</th>
                    <th>` + lang.r_item + `</th>
                    <th>` + lang.amount + `</th>
                    <th>` + lang.price + `</th>
                    <th>` + lang.total + `</th>
                    </tr>
                    </thead>
                    <tbody id= "` + TableID + `"></tbody></table></div>
                    <div class= "timeline-footer" >
                    <button id = "DeleteDraft" data-draft-id="` + draft.id + `" class= "btn btn-danger btn-sm" > <i class="fa fa-trash"></i> ` + lang.delete + `</button>
                    <button id = "UseDraft" data-table-id= "` + TableID + `" data-draft-id= "` + draft.id + `" class="btn btn-success btn-sm"><i class="fas fa-mouse-pointer"></i>  ` + lang.use + `</button></div></div></div></div>`);

                    // Foreach draft products table in drafts
                    $.each(draft.products, function (k, item) {
                        let productkey = xy++;
                        $("#" + TableID).append(`<tr data-product-price="` + item[3] + `" data-product-id="` + item[0] + `">
                                <td id="ProductID" style="display:none;">` + item[0] + `</td><td>` + item[1] + `</td><td id="itemName">` +
                            data[1][xy] + `</td><td id="itemAmount">` + item[2] + `</td><td id="itemPrice">` + item[3] + `</td><td id="itemTotal">`
                            + item[4] + `</td></tr>`);
                    });
                });
            }
        }
    })
    ;
});



//  Use Draft
$(document).on('click', '#UseDraft', function (event) {
    let DraftID = $(this).attr('data-draft-id');
    let TableID = $(this).attr('data-table-id');
    $("#itemsTRs").replaceWith($("#" + TableID).clone());
    $("#OrderTable tbody").attr('id', 'itemsTRs');
    $("#itemsTRs tr").attr('id', 'itemTR');
    $("#itemsTRs tr:last-of-type").attr('class', 'badge-primary item-on-active');

    $("#TotalOrderPrice").html($(this).parent().prev().prev().find("#DraftPrice").html());
});

// DebitCard
function pay_debit() {
    $(this).attr('data-pay-use', '1').css('background-color', '#28a745');
    $("#CreditCard").attr('data-pay-use', '0').css('background-color', '#17a2b8');

    // Show Credit transaction number field
    $(".transcation-debit").show();
    $("#TransactionDebit").focus();

    cancel_credit();


    $("#DebitCard").one("click", cancel_debit);
}

// if pressed confirm credit transcation number
$(".transcation-debit button").on("click", function () {
    if ($("#TransactionDebit").val().length === 0) {
        emptyTransactionNumber();
    } else {
        // hide credit transcation number field
        $(".transcation-debit").hide();
    }
});

function cancel_debit() {
    $(this).attr('data-pay-use', '0');
    $(this).css('background-color', '#17a2b8');

    // Remove Credit transaction number field value
    $("#TransactionDebit").val('');
    $(".transcation-debit").hide();


    $("#DebitCard").one("click", pay_debit);
}

$("#DebitCard").one("click", pay_debit);

// CreditCard
function pay_credit() {
    $(this).attr('data-pay-use', '1').css('background-color', '#28a745');
    $("#DebitCard").attr('data-pay-use', '0').css('background-color', '#17a2b8');

    // Show Credit transaction number field
    $(".transcation-credit").show();
    $("#TransactionCredit").focus();

    $("#CreditCard").one("click", cancel_credit);

    cancel_debit();
}

// if pressed confirm credit transcation number
$(".transcation-credit button").on("click", function () {
    if ($("#TransactionCredit").val().length === 0) {
        emptyTransactionNumber();
    } else {
        // hide credit transcation number field
        $(".transcation-credit").hide();
    }
});

function cancel_credit() {
    $(this).attr('data-pay-use', '0');
    $(this).css('background-color', '#17a2b8');

    // Remove Credit transaction number field value
    $("#TransactionCredit").val('');
    $(".transcation-credit").hide();

    $("#CreditCard").one("click", pay_credit);
}

$("#CreditCard").one("click", pay_credit);


$(document).on('click', '#ConfirmDeliveryData', function (event) {
    $("#DeliveryData").html($("#DeliveryForm").clone());
    // Add delivery fee to the total price
    let totalPrice = $("#TotalOrderPrice").text();
    $("#TotalOrderPrice").text((parseFloat(totalPrice) + parseFloat($("#DeliveryFee").val())).toFixed(2));
});



//  Order Number

if (localStorage.getItem('last_order') && localStorage.getItem('order_number')) {

    if (localStorage.getItem('last_order') != DateNow) {
        // The cookie is yesterday and let's start from 1
        localStorage.setItem('last_order', DateNow)
        localStorage.setItem('order_number', '1')
        $("#OrderCounter").text(localStorage.getItem('order_number'));
    }
    $("#OrderCounter").text(localStorage.getItem('order_number'));

} else {
    localStorage.setItem('last_order', DateNow)
    localStorage.setItem('order_number', '1')

    $("#OrderCounter").text(localStorage.getItem('order_number'));
}

//  On Change Table Show no of items

function NoOfItems() {
    var sum = 0;
    $('td#itemAmount').each(function () {
        sum += parseFloat($(this).text());
        $("#ItemsCounter").text(Math.ceil(sum));
    });
}

$('#OrderTable').bind('DOMSubtreeModified', function (e) {
    NoOfItems();
});

// Transaction Empty Show Error

function emptyTransactionNumber() {
    Swal.fire({
        title: lang.please_transaction_number + ' !',
        type: 'error',
        confirmButtonColor: '#3085d6',
        confirmButtonText: lang.submit,
        allowOutsideClick: false
    });
    return false;
}

// Order Installment
$("#PayInstallment").on('click',function(){
    // Check if there's a client or not
    if(!Client) {
        Swal.fire({
            title: lang.please_add_client_to_make_installment + ' !',
            type: 'error',
            confirmButtonColor: '#3085d6',
            confirmButtonText: lang.submit,
            allowOutsideClick: false
        });
        return false
    }
    // else show the installment plans
    $('.installment-modal').show();
});
// Installment Plan on change
$("#InstallmentPlan").on('change',function(){
    // show installment method for chosen plan

});

