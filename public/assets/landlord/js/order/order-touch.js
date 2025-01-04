
//  Up and Down Keyboard Buttons
function RowUp() {
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

function RowDown() {
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

$("#UpButton").click(function () {
    RowUp();
});
$("#DownButton").click(function () {
    RowDown();
});

$(document).keydown(function (event) {
    // Up Keyboard Button
    if (event.which == "38" && $(".item-on-active")[0]) {
        RowUp();
    }

    // Down Keyboard Button
    if (event.which == "40" && $(".item-on-active")[0]) {
        RowDown();
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

            // Get total order price without VAT
            let TotalOrderPriceNoVAT = $("#TotalOrderPriceNoVAT").text();
            $("#TotalOrderPriceNoVAT").html
            ((parseFloat(TotalOrderPriceNoVAT) - (OldPrice - OldPrice * VatValue / 100) + (PriceAfterDiscount - PriceAfterDiscount * VatValue / 100)).toFixed(2));

            // Get Current Total Order VAT
            let OldTotalOrderVAT = $("#TotalOrderVAT").text();
            $("#TotalOrderVAT").html
            ((parseFloat(OldTotalOrderVAT) - (OldPrice * VatValue / 100) + (PriceAfterDiscount * VatValue / 100)).toFixed(2));


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

            // Get total order price without VAT
            let TotalOrderPriceNoVAT = $("#TotalOrderPriceNoVAT").text();
            $("#TotalOrderPriceNoVAT").html
            ((parseFloat(TotalOrderPriceNoVAT) - (OldTotalPrice - OldTotalPrice * VatValue / 100) + (OldTotalPrice * OfferItem / 100 - OldTotalPrice * OfferItem / 100 * VatValue / 100)).toFixed(2));

            // Get Current Total Order VAT
            let OldTotalOrderVAT = $("#TotalOrderVAT").text();
            $("#TotalOrderVAT").html
            ((parseFloat(OldTotalOrderVAT) - (OldTotalPrice * VatValue / 100) + (OldTotalPrice * OfferItem / 100 * VatValue / 100)).toFixed(2));
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

            // Get total order price without VAT
            let TotalOrderPriceNoVAT = $("#TotalOrderPriceNoVAT").text();
            $("#TotalOrderPriceNoVAT").html
            ((parseFloat(TotalOrderPriceNoVAT) - (OldPrice - OldPrice * VatValue / 100)).toFixed(2));

            // Get Current Total Order VAT
            let OldTotalOrderVAT = $("#TotalOrderVAT").text();
            $("#TotalOrderVAT").html
            ((parseFloat(OldTotalOrderVAT) - (OldPrice * VatValue / 100)).toFixed(2));

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
$("#cancelItem, #CancelRowButton").on('click', function () {
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
            // Minus the price from the total
            let totalPrice = $("#TotalOrderPrice").text();
            let canceledItemPriceAmount = parseFloat($(".item-on-active").find('#itemPrice').text()) * parseFloat($(".item-on-active #itemAmount").text());
            $("#TotalOrderPrice").text((parseFloat(totalPrice) - parseFloat(canceledItemPriceAmount)).toFixed(2));

            // Get total order price without VAT
            let TotalOrderPriceNoVAT = $("#TotalOrderPriceNoVAT").text();
            $("#TotalOrderPriceNoVAT").html
            ((parseFloat(TotalOrderPriceNoVAT) - (canceledItemPriceAmount - canceledItemPriceAmount * VatValue / 100)).toFixed(2));

            // Get Current Total Order VAT
            let OldTotalOrderVAT = $("#TotalOrderVAT").text();
            $("#TotalOrderVAT").html
            ((parseFloat(OldTotalOrderVAT) - (canceledItemPriceAmount * VatValue / 100)).toFixed(2));
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

        // Get total order price without VAT
        let TotalOrderPriceNoVAT = $("#TotalOrderPriceNoVAT").text();
        $("#TotalOrderPriceNoVAT").html
        ((parseFloat(TotalOrderPriceNoVAT) - (OldPrice - OldPrice * VatValue / 100) + (PriceAfterChangePrice - PriceAfterChangePrice * VatValue / 100)).toFixed(2));

        // Get Current Total Order VAT
        let OldTotalOrderVAT = $("#TotalOrderVAT").text();
        $("#TotalOrderVAT").html
        ((parseFloat(OldTotalOrderVAT) - (OldPrice * VatValue / 100) + (PriceAfterChangePrice * VatValue / 100)).toFixed(2));

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
            // Remove TotalOrderPriceNoVAT
            $("#TotalOrderPriceNoVAT").html('0');
            // Remove TotalOrderVAT
            $("#TotalOrderVAT").html('0');

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
                            <h5 class="modal-title" id="exampleModalLabel">` + lang.product + ` </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button></div><div class="modal-body" id="ItemDescription" ><h5>` + lang.product_title + ` | ` + $(".item-on-active #itemName").text() + `</h5><h6>` + lang.description + ':' + $(".item-on-active").attr('data-product-description') + ` </h6>
 <h6 class="text-success font-weight-bold"> ` + lang.price_item + ` | ` + $(".item-on-active").find('#itemPrice').text() + ` * 1 </h6>
        <img src="` + Modelimg + `" class="model-image"></div></div></div></div>`);
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

        let PriceAfterAmount = (parseFloat($(".item-on-active #itemPrice").text()) * parseFloat($("#changeAmountInput").val()));

        $("#TotalOrderPrice").text((parseFloat(totalPrice) - parseFloat(OldPrice) + PriceAfterAmount).toFixed(2));

        // Get total order price without VAT
        let TotalOrderPriceNoVAT = $("#TotalOrderPriceNoVAT").text();
        $("#TotalOrderPriceNoVAT").html
        ((parseFloat(TotalOrderPriceNoVAT) - (OldPrice - OldPrice * VatValue / 100) + (PriceAfterAmount - PriceAfterAmount * VatValue / 100)).toFixed(2));

        // Get Current Total Order VAT
        let OldTotalOrderVAT = $("#TotalOrderVAT").text();
        $("#TotalOrderVAT").html
        ((parseFloat(OldTotalOrderVAT) - (OldPrice * VatValue / 100) + (PriceAfterAmount * VatValue / 100)).toFixed(2));

        // Change Current item price
        $("#CurrentItemPrice, .item-on-active #itemTotal").text((parseFloat($(".item-on-active").find("#itemPrice").text()) * parseFloat($("#changeAmountInput").val())).toFixed(2));
        // Remove input and set the value in the amount cell
        $(".item-on-active #itemAmount").html($("#changeAmountInput").val());
        $('#barcodeNumber').focus();
    }
});


//  Search by barcode
function searchByBarcode() {
    var id = $("#barcodeNumber").val();
    var NumProducts = $('#itemsTRs').children().length + 1;
    $.ajax({
        type: "get",
        dataType: 'json',
        url:  `${url_get_product}/${id}`,
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            Authorization: "Bearer " + localStorage.getItem("auth_token"),
            type:"barcode"
        },
        beforeSend: function () {
            $(".barcode-status").html(`<i class="fas fa-circle-notch fa-spin"></i> ` + lang.barcode_searching);
        }
        ,
        success: function (data) {

            if (data === 'empty') {
                $(".barcode-status").html(`<i class="fas fa-exclamation-circle"></i> ` + lang.no_data_found);
                // $('#barcodeNumber').val('');
            } else {
                $(".barcode-status").html('');
                $('#barcodeNumber').val('');
                $("tr#itemTR").removeClass('item-on-active').removeClass('badge-primary');
                var CurrentProductPrice = parseFloat(data.sale_price);

                // Get Current Item Price
                $("#CurrentItemPrice").text(CurrentProductPrice.toFixed(2));
                // Get All Total Items Price
                let totalPrice = $("#TotalOrderPrice").text();
                $("#TotalOrderPrice").html((parseFloat(totalPrice) + CurrentProductPrice).toFixed(2));
                // Get total order price without VAT
                let TotalOrderPriceNoVAT = $("#TotalOrderPriceNoVAT").text();
                $("#TotalOrderPriceNoVAT").html((parseFloat(TotalOrderPriceNoVAT) + (CurrentProductPrice - CurrentProductPrice * VatValue / 100)).toFixed(2));

                // Get Current Total Order VAT
                let OldTotalOrderVAT = $("#TotalOrderVAT").text();
                $("#TotalOrderVAT").html((parseFloat(OldTotalOrderVAT) + (CurrentProductPrice * VatValue / 100)).toFixed(2));

                // Add Item To Table
                $("#itemsTRs").append(`<tr id="itemTR" class="badge-primary item-on-active"
data-product-price="` + CurrentProductPrice.toFixed(2) + `"
data-product-description="` + data.description + `"
data-product-image="` + data.image + `"
data-product-id="` + data.id + `"
data-product-stock="` + data.stock + `"
>
                         <td id="ProductID" style="display:none;">` + data.id + `</td><td>` + NumProducts++ + `</td><td id="itemName">` + data.name + `</td><td id="itemAmount">1</td><td id="itemPrice">` + CurrentProductPrice.toFixed(2) + `</td><td id="itemTotal">` + CurrentProductPrice.toFixed(2) + `</td></tr>`);

                // Scroll bottom Table
                ScrollTableBottom();
            }
        }
        ,
        error: function () {
            $(".barcode-status").html('');
        }
    });
}


//  Search by Product Touch

$('div.most-product:not(#SubCategoryButton)').on('click', function () {
    var id = $(this).data('product-id');
    var NumProducts = $('#itemsTRs').children().length + 1;
    var CurrentProductPrice = parseFloat($(this).attr('data-product-price'));
    // Main Product Touch Function :
    $(".barcode-status").html('');
    $('#barcodeNumber').val('');
    $("tr#itemTR").removeClass('item-on-active').removeClass('badge-primary');
    // Get Current Item Price
    $("#CurrentItemPrice").text(CurrentProductPrice.toFixed(2));
    // Get All Total Items Price
    let totalPrice = $("#TotalOrderPrice").text();
    $("#TotalOrderPrice").text((parseFloat(totalPrice) + CurrentProductPrice).toFixed(2));

    // Get total order price without VAT
    let TotalOrderPriceNoVAT = $("#TotalOrderPriceNoVAT").text();
    $("#TotalOrderPriceNoVAT").html((parseFloat(TotalOrderPriceNoVAT) + (CurrentProductPrice - CurrentProductPrice * VatValue / 100)).toFixed(2));

    // Get Current Total Order VAT
    let OldTotalOrderVAT = $("#TotalOrderVAT").text();
    $("#TotalOrderVAT").html((parseFloat(OldTotalOrderVAT) + (CurrentProductPrice * VatValue / 100)).toFixed(2));

    // Add Item To Table
    $("#itemsTRs").append(`<tr id="itemTR" class="badge-primary item-on-active"
                            data-product-price="` + CurrentProductPrice.toFixed(2) + `"
                            data-product-description="` + $(this).attr('data-product-description') + `"
                            data-product-image="` + $(this).attr('data-product-image') + `"
                            data-product-id="` + $(this).attr('data-product-id') + `"
                            data-product-stock="` + $(this).attr('data-product-stock') + `">
                            <td id="ProductID" style="display:none;">` + $(this).attr('data-product-id') + `</td>
                            <td>` + NumProducts++ + `</td>
                            <td id="itemName">` + $(this).attr('data-product-name') + `</td>
                            <td id="itemAmount">1</td>
                            <td id="itemPrice">` + CurrentProductPrice.toFixed(2) + `</td>
                            <td id="itemTotal">` + CurrentProductPrice.toFixed(2) + `</td>
                        </tr>`);

    $("#barcodeNumber").focus();
    // Scroll bottom Table
    ScrollTableBottom();
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

        // Order Type
        var order_type = $("#OrderType").val();

        // Service Fee for dine in only
        var service_fee = '';
        if (order_type == 'dine_in') {
            var service_fee = setting_service_fee;
        } else {
            var service_fee = null;
        }

        // Client Order if exist
        var client_code = '';
        if (!$("#ClientData").is(':empty')) {
            $("#OrderClientName").html(lang.client + ': ' + ClientName);
            var client_code = ClientCode;
        }

        // Table Number
        var table_number = $("#TableNumber").val();

        // Send to steward function
        var send_steward = '';
        if ($('#SendSteward').is(':checked')) {
            var send_steward = 'on'
        } else {
            var send_steward = 'off'
        }

        // Steward Notes
        var steward_notes = $("#StewardNotes").val();

        // Print Current Order #No
        var current_order_no = localStorage.getItem('order_number');
        $("#CurrentOrderNo").html(`
            No.` + current_order_no);

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
            html: `<div class="text-success font-weight-bold">` + lang.remaining_amount + RemainAmount + `</div><div class="text-danger flash-text font-weight-bold" > ` + lang.close_drawer + `</div><div class="font-weight-bold" id ="OrderStatus"></div>`,
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
                _token: $('meta[name="_token"]').attr('content'),
                touch: true,
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
                table_number: table_number,
                order_type: order_type,
                service_fee: service_fee,
                send_steward: send_steward,
                current_order_no: current_order_no,
                steward_notes: steward_notes,
                transaction_number:transaction_number
            },
            beforeSend: function (wait) {
                $("#OrderStatus").html(`<span class="text-muted"><i class="fas fa-spinner fa-pulse"></i> `
                    + lang.transferring_order + '</span>');
            }, success: function (response) {
                if (response === 'VALID') {
                    $("#OrderStatus").html(`<span class="text-success"><i class="fas fa-check-circle"></i> `
                        + lang.order_transferred + '</span>');
                    $(".subtotal-price").html(total_price);
                    $("#ReceiptTotalWithoutVAT").html($("#TotalOrderPriceNoVAT").html());
                    $("#ReceiptTotalTax").html($("#TotalOrderVAT").html());


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
                    // If Client Data
                    if (!$("#ClientData").is(':empty')) {
                        $("#OrderClientName").html(lang.client + ':' + ClientName);
                    } else {
                        $("#OrderClientName").html("");
                    }
                    // If Dine in show service fee
                    if (order_type == 'dine_in') {
                        $("#ReceiptServiceFee").html(lang.service_fee + ':' + `<b>` + setting_service_fee + `</b>` + currency);
                    } else {
                        $("#ReceiptServiceFee").html('');
                    }
                    // Put Order Type in receipt
                    var ReceiptOrderType = '';
                    if (order_type == 'dine_in') {
                        var ReceiptOrderType = lang.dine_in;
                    } else if (order_type == 'take_away') {
                        var ReceiptOrderType = lang.take_away;
                    } else {
                        var ReceiptOrderType = lang.delivery;
                    }

                    $("#OrderTypeReceipt").html(lang.order_type + ' : ' + ReceiptOrderType);

                    // put the no of items to receipt
                    $(".no-items").text($("#ItemsCounter").text());

                    // Print Receipt
                    // $('#Final').printThis();
                    $("#CalculatorSection").hide();
                    $(this).find('i').toggleClass('fa-calculator');
                    $("#ExtraButtons").hide();
                    $(this).find('i').toggleClass('fa-ellipsis-v');
                    $(".anotherFullScreen").hide();
                    $("body").removeClass('swal2-shown swal2-height-auto');
                    window.print();


                    // Clear All Data
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
                    // Remove TotalOrderPriceNoVAT
                    $("#TotalOrderPriceNoVAT").html('0');
                    // Remove TotalOrderVAT
                    $("#TotalOrderVAT").html('0');
                    // Remove order data Data
                    $("#OrderType").val("");
                    // Remove table number Data
                    $("#TableNumber").val("");
                    // set the no of items to 0
                    $("#ItemsCounter").text('0');
                    // Check send to steward
                    $('#SendSteward').prop('checked', true);
                    // Remove Steward Notes
                    $("#StewardNotes").val('')


                    // Increase Cashier Order Counter
                    let OrderCounter = $("#OrderCounter").text();
                    $("#OrderCounter").html(parseInt(OrderCounter) + 1);
                    localStorage.setItem('order_number', parseInt(localStorage.getItem('order_number')) + 1)
                    localStorage.setItem('last_order', DateNow)

                } else {
                    $("#OrderStatus").html(` <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> ` + lang.order_not_transferred + '</span>');
                }
            }
        });
    }
}

// On click PayOrder Button
$("#PayOrder").on('click', function () {
    payOrder();
});


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
            // Remove Steward Notes
            $("#StewardNotes").val('')
            // Remove TotalOrderPriceNoVAT
            $("#TotalOrderPriceNoVAT").html('0');
            // Remove TotalOrderVAT
            $("#TotalOrderVAT").html('0');
        }
    });
}

//  Cancel Order
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
                    html: '',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: lang.submit,
                });
            } else {
                Swal.fire({
                    imageUrl: assets_bill,
                    html: `<h4><i class="fas fa-cog fa-spin"></i> ` + lang.printing_last_receipt + `</h4> `,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: lang.submit,
                });
                $(".subtotal-price").html(data[0].total_price);
                $(".amount-paid").html(data[0].amount_paid);
                $(".remaining-amount").html(data[0].total_price - data[0].amount_paid);

                $("#ReceiptTotalWithoutVAT").html(parseFloat(data[0].total_price - (data[0].total_price * VatValue / 100).toFixed(2)));
                $("#ReceiptTotalTax").html(parseFloat((data[0].total_price * VatValue / 100).toFixed(2)));


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

                $("#ReceiptTable").html(`<table><thead><tr><th> # </th><th>` + lang.r_item + `</th><th> ` + lang.amount + `</th><th>` + lang.price + `</th><th>` + lang.total + ` </th></tr></thead>` + `<tbody id="LastTableData"></tbody></table>`);

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
                $("body").removeClass('swal2-shown swal2-height-auto');
                window.print();
                // $('#Final').printThis();

            }
        }
    });
}

$("#PrintLast").on('click', function () {
    PrintLastReceipt();
});



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
                }
                ,
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
                         <td id="ProductID" style="display:none;">G</td><td>*</td><td id="itemName">-` + data + currency + lang.gift_card + ` </td><td id="itemAmount">1</
                    td > <td
                    id = "itemPrice" > -` + data + currency + `<
                    /td><td id="itemTotal">-` + data + currency + `</
                    td > </tr>`);
                        // Change Total Price
                        $("#TotalOrderPrice").text(parseFloat($("#TotalOrderPrice").text()) - data);
                    }
                }
            })
            ;

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
                _token: $('meta[name="_token"]').attr('content'),
                total_price: total_price,
                products: ProductsArray,
            },
            beforeSend: function (wait) {
                btn.html(`<i class="fas fa-spinner fa-pulse"></i> ` + lang.saving);
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
                    // Remove Delivery Data if exist
                    $("#ReceiptServiceFee").html('');
                    // Remove Vats
                    $("#TotalOrderPriceNoVAT").html('');
                    $("#TotalOrderVAT").html('');
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
                // عند بداية الاسكربت الخاص بالبحث عن المسودات يبدا العد للمتغير xy من -1
                // ثم يزداد داخل الحلقة بقيمة 1 ليكون اول قيمة 0
                var xy = -1;
                $.each(data[0], function (i, draft) {
                    let DraftPrice = draft.total_price;
                    let TableID = `DraftProducts` + (Math.random() + '').substring(2, 8);


                    $("#DraftOne").append(`<div class="timeline">
                                <div class="time-label"><span class="bg-primary">` + draft.created_at + `</span></div>
                                <div>
                                    <i class="fas fa-clipboard-list bg-primary"></i>
                                    <div class="timeline-item">
                                        <span class="total-price"><i class="far fa-money-bill-alt"></i>` + lang.total_price + `<span
                id = "DraftPrice" > ` + DraftPrice + ` </span>` + currency + `</span>
                <divclass = "timeline-body" >
                    <table style = "font-size: 13px;" >
                    <thead>
                    <tr>
                    <th> # </th>
                    <th> ` + lang.r_item + ` </th>
                    <th> ` + lang.amount + ` </th>
                    <th> ` + lang.price + ` </th>
                    <th> ` + lang.total + ` </th>
                    </tr>
                    </thead>
                    <tbody id="` + TableID + `" >
                    </tbody>
                    </table>
                    </div>
                    <div class="timeline-footer">
                    <button id = "DeleteDraft" data-draft-id="` + draft.id + `" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> ` + lang.delete + `</button><button id = "UseDraft" data-table-id="` + TableID + `" data-draft-id="` + draft.id + `" class="btn btn-success btn-sm"<i class="fas fa-mouse-pointer"></i> ` + lang.use + `</button>
                </div>
                </div>
                </div>
                </div>`);

                    // Foreach draft products table in drafts
                    $.each(draft.products, function (k, item) {
                        let productkey = xy++;
                        $("#" + TableID).append(`<tr data-product-price="` + item[3] + `" data-product-id="` + item[0] + `">
                                <td id="ProductID" style="display:none;">` + item[0] + `</td><td>` +
                            item[1] + `</td><td id="itemName">` +
                            data[1][xy]
                            + `</td><td id="itemAmount">` + item[2] + `</td><td id="itemPrice">` + item[3] + `</td><td id="itemPrice">` + item[4] + `</td></tr>`);
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
    let TotalOrderPrice = parseFloat($("#TotalOrderPrice").text());

    $("#TotalOrderPriceNoVAT").html(parseFloat(TotalOrderPrice - (TotalOrderPrice * VatValue / 100).toFixed(2)));

    $("#TotalOrderVAT").html(parseFloat((TotalOrderPrice * VatValue / 100).toFixed(2)));


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
    if($("#TransactionDebit").val().length === 0) {
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
    if($("#TransactionCredit").val().length === 0) {
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



//  Delivery

// Confirm Delivery Data
$(document).on('click', '#ConfirmDeliveryData', function (event) {
    $("#DeliveryData").html($("#DeliveryForm").clone());
    // Add delivery fee to the total price
    let totalPrice = $("#TotalOrderPrice").text();
    $("#TotalOrderPrice").text((parseFloat(totalPrice) + parseFloat($("#DeliveryFee").val())).toFixed(2));
    // Set order type as delivery
    $("#OrderType").val('delivery');
});


//======= TOUCH Scripts

//  Grid Buttons

$("#ShowExtraButtons").click(function () {
    $("#ExtraButtons").toggle(200);
    $(this).find('i').toggleClass('fa-ellipsis-v fa-ellipsis-h')
});

// Show and focus calculator
function showCalculator() {
    $("#CalculatorSection").toggle(200);
    $(this).find('i').toggleClass('fa-calculator fa-times-circle');
    $("#result").focus();
}

$("#ShowCalculator").click(function () {
    showCalculator();
});

//  Categories Slice

// Hide all categories from the 6 category
$("button#CategoryButton").slice(6,).hide();
// Prev button disable
$("#CatPrev").prop('disabled', true);

let CategoriesCount = $('.categories-buttons').length;
x = 6
// Next categories button
$("#CatNext").click(function () {
    x = x + 6;
    $("button#CategoryButton").slice(0, x).hide();
    $("button#CategoryButton").slice(x - 6, x).show();

    $("#CatPrev").prop('disabled', false);
    if (x >= CategoriesCount) {
        $("#CatNext").prop('disabled', true);
    }

});
// Prev categories button
$("#CatPrev").click(function () {

    $("button#CategoryButton").slice(x - 6, x).hide();
    $("button#CategoryButton").slice(x - 6 - 6, x - 6).show();

    $("#CatNext").prop('disabled', false);

    x = x - 6;
    if (x == 6) {
        $("#CatPrev").prop('disabled', true);
    }
});
// Disable first Category button


//  Products Slice

// First Category ID Button
let FirstCategoryID = $('.all-categories').children().first().find("#CategoryButton").attr('data-cat-id');
// Hide All Products Container execpt first category in buttons
$(".products-container[data-product-category-id!='" + FirstCategoryID + "']").hide();
// Show only 20 products in product container
$(".products-container .most-product").slice(20,).hide();
// Disable first Category button
$("#CategoryButton[data-cat-id='" + FirstCategoryID + "']").prop('disabled', true);
// Categories button Click
$("button#CategoryButton").click(function () {
    let ButtonCategoryID = $(this).attr('data-cat-id');
    // Disable this button and enable the others
    $("button#CategoryButton").prop('disabled', false);
    $(this).prop('disabled', true);
    // Remove Back button if exist
    $('#BackPage').remove();
    // Hide All Products Container execpt this button category id
    $(".products-container[data-product-category-id!='" + ButtonCategoryID + "']").hide();
    $(".products-container[data-product-category-id='" + ButtonCategoryID + "']").show();

    $(".products-container[data-product-category-id='" + ButtonCategoryID + "'] .most-product").hide();
    $(".products-container[data-product-category-id='" + ButtonCategoryID + "'] .most-product").slice(0, 20).show();

    // re-modify Products Control Buttons
    $("#ProductPrev, #ProductFirstPage").prop('disabled', true);

    xPages = 20
    ProductsCount = $(".products-container[data-product-category-id='" + ButtonCategoryID + "'] .most-product").length;
    if (ProductsCount <= 20) {
        $("#ProductNext, #ProductLastPage").prop('disabled', true);
    } else {
        $("#ProductNext, #ProductLastPage").prop('disabled', false);
    }
});

// Products Control Buttons
// Prev auto button disable
$("#ProductPrev, #ProductFirstPage").prop('disabled', true);
// Next Page
xPages = 20
$("#ProductNext").click(function () {
    xPages = xPages + 20;
    ProductsCount = $('.products-container:visible .most-product').length;

    $(".products-container .most-product").slice(0, xPages).hide();

    $(".products-container .most-product").slice(xPages - 20, xPages).show();

    $("#ProductPrev,#ProductFirstPage").prop('disabled', false);
    if (xPages >= ProductsCount) {
        $("#ProductNext, #ProductLastPage").prop('disabled', true);
    }

});
// Prev Page
$("#ProductPrev").click(function () {

    ProductsCount = $('.products-container:visible .most-product').length;
    $(".products-container .most-product").slice(xPages - 20, xPages).hide();
    $(".products-container .most-product").slice(xPages - 20 - 20, xPages - 20).show();

    $("#ProductNext, #ProductLastPage").prop('disabled', false);

    xPages = xPages - 20;
    if (xPages == 20) {
        $("#ProductPrev,#ProductFirstPage").prop('disabled', true);
    }
});
// Last Page
$("#ProductLastPage").click(function () {

    ProductsCount = $('.products-container:visible .most-product').length;
    EstProductsPages = (ProductsCount / 20);

    $(".products-container .most-product").slice(0, ProductsCount).hide();

    if (EstProductsPages % 1 != 0) {
        $(".products-container .most-product").slice(20 * Math.trunc(EstProductsPages),).show();
        xPages = 20 * Math.trunc(EstProductsPages) + 20;

    } else {
        $(".products-container .most-product").slice(ProductsCount - 20, ProductsCount).show();
        xPages = ProductsCount;

    }


    $("#ProductPrev,#ProductFirstPage").prop('disabled', false);
    $("#ProductNext, #ProductLastPage").prop('disabled', true);


});
// First Page
$("#ProductFirstPage").click(function () {
    xPages = 20;

    ProductsCount = $('.products-container:visible .most-product').length;

    $(".products-container .most-product").slice(0, 20).show();
    $(".products-container .most-product").slice(20,).hide();

    $("#ProductNext, #ProductLastPage").prop('disabled', false);

    $("#ProductPrev,#ProductFirstPage").prop('disabled', true);

});

//  Sub Category Button Click

$("div#SubCategoryButton").click(function () {
    let ButtonSubCategoryID = $(this).attr('data-sub-category-id');
    // Hide All Products Container execpt this button sub category id
    $(".products-container .most-product[data-sub-category-product-id!='" + ButtonSubCategoryID + "']").hide();
    // $(".products-container .most-product[data-sub-category-product-id='" + ButtonSubCategoryID + "']").show();
    $(".products-container .most-product[data-sub-category-product-id='" + ButtonSubCategoryID + "']").slice(0, 19).show();

    $(".products-container:visible").append('<i class="fas fa-arrow-circle-left" id="BackPage"></i>');

    xPages = 20
    ProductsCount = $(".products-container  .most-product[data-sub-category-product-id='" + ButtonSubCategoryID + "']").length;
    if (ProductsCount <= 20) {
        $("#ProductNext, #ProductLastPage").prop('disabled', true);
    } else {
        $("#ProductNext, #ProductLastPage").prop('disabled', false);
    }
});


$(document).on('click', '#BackPage', function () {
    CurrentProductsCategory = $(".products-container:visible").attr('data-product-category-id');
    // Hide All Products Container execpt this products category visible
    $(".products-container[data-product-category-id!='" + CurrentProductsCategory + "']").hide();
    $(".products-container[data-product-category-id='" + CurrentProductsCategory + "']").show();

    $(".products-container[data-product-category-id='" + CurrentProductsCategory + "'] .most-product").hide();
    $(".products-container[data-product-category-id='" + CurrentProductsCategory + "'] .most-product").slice(0, 20).show();

    $(this).remove();

    xPages = 20
    ProductsCount = $(".products-container:visible .most-product").length;
    if (ProductsCount <= 20) {
        $("#ProductNext, #ProductLastPage").prop('disabled', true);
    } else {
        $("#ProductNext, #ProductLastPage").prop('disabled', false);
    }
});

//  Increase Item Amount

$("#IncreaseItem").click(function () {
    let totalPrice = $("#TotalOrderPrice").text();
    let OldPrice = parseFloat($(".item-on-active #itemPrice").text()) * parseFloat($(".item-on-active #itemAmount").text());

    CurrentAmount = $(".item-on-active #itemAmount").text();
    CurrentAmount++;

    let PriceAfterAmount = (parseFloat($(".item-on-active #itemPrice").text()) * parseFloat(CurrentAmount));

    $("#TotalOrderPrice").text((parseFloat(totalPrice) - parseFloat(OldPrice) + PriceAfterAmount).toFixed(2));

    // Get total order price without VAT
    let TotalOrderPriceNoVAT = $("#TotalOrderPriceNoVAT").text();
    $("#TotalOrderPriceNoVAT").html
    ((parseFloat(TotalOrderPriceNoVAT) - (OldPrice - OldPrice * VatValue / 100) + (PriceAfterAmount - PriceAfterAmount * VatValue / 100)).toFixed(2));

    // Get Current Total Order VAT
    let OldTotalOrderVAT = $("#TotalOrderVAT").text();
    $("#TotalOrderVAT").html
    ((parseFloat(OldTotalOrderVAT) - (OldPrice * VatValue / 100) + (PriceAfterAmount * VatValue / 100)).toFixed(2));

    // Change Current item price
    $("#CurrentItemPrice, .item-on-active #itemTotal").text((parseFloat($(".item-on-active").find("#itemPrice").text()) * parseFloat(CurrentAmount)).toFixed(2));
    // Remove input and set the value in the amount cell
    $(".item-on-active #itemAmount").html(CurrentAmount);
    $('#barcodeNumber').focus();
});

//  Decrease Item Amount

$("#DecreaseItem").click(function () {
    CurrentAmount = $(".item-on-active #itemAmount").text();
    // if amount = 1 dont decrease !
    if (CurrentAmount == 1) {
        return false
        $('#barcodeNumber').focus();
    } else {
        CurrentAmount--;

        let totalPrice = $("#TotalOrderPrice").text();

        let OldPrice = parseFloat($(".item-on-active #itemPrice").text()) * parseFloat($(".item-on-active #itemAmount").text());

        let PriceAfterAmount = (parseFloat($(".item-on-active #itemPrice").text()) * parseFloat(CurrentAmount));

        $("#TotalOrderPrice").text((parseFloat(totalPrice) - parseFloat(OldPrice) + PriceAfterAmount).toFixed(2));

        // Get total order price without VAT
        let TotalOrderPriceNoVAT = $("#TotalOrderPriceNoVAT").text();
        $("#TotalOrderPriceNoVAT").html
        ((parseFloat(TotalOrderPriceNoVAT) - (OldPrice - OldPrice * VatValue / 100) + (PriceAfterAmount - PriceAfterAmount * VatValue / 100)).toFixed(2));

        // Get Current Total Order VAT
        let OldTotalOrderVAT = $("#TotalOrderVAT").text();
        $("#TotalOrderVAT").html
        ((parseFloat(OldTotalOrderVAT) - (OldPrice * VatValue / 100) + (PriceAfterAmount * VatValue / 100)).toFixed(2));

        // Change Current item price
        $("#CurrentItemPrice, .item-on-active #itemTotal").text((parseFloat($(".item-on-active").find("#itemPrice").text()) * parseFloat(CurrentAmount)).toFixed(2));
        // Remove input and set the value in the amount cell
        $(".item-on-active #itemAmount").html(CurrentAmount);
    }
    $('#barcodeNumber').focus();
});

//  websocket scripts

if (localStorage.getItem('user_id') == null) {
    localStorage.setItem('user_id', setting_user_id)
}

//  if dine_in add service fee to the total price

$("#OrderType").on('change', function () {
    if ($(this).val() == 'dine_in') {
        let totalPrice = $("#TotalOrderPrice").text();
        let ServiceFeePrice = setting_service_fee;

        $("#TotalOrderPrice").text((parseFloat(totalPrice) + parseFloat(ServiceFeePrice)).toFixed(2));
    }
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

//   HOT Keys

$(document).keydown(function (event) {
    if (event.which == 9) {
        $("#barcodeNumber").focus();
    }
});

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

//  Steward Notes

$(".steward-notes").click(function () {
    $("#StewardNotes").toggle();
    $(this).find('i').toggleClass('fas far');
});
//  Enter Button
$("#EnterButton").click(function () {
    searchByBarcode();
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
