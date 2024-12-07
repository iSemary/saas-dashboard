
/**
 *
 * Initial Functions
 *
 */


/**
 *
 * Start Barcode Generator Script
 */

 function generateBarcode() {
    // Rand 13 Number
    var value = '4' + (Math.random() + '').substring(2, 8)
        + (Math.random() + '').substring(2, 9);
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
}

function showConfig1D() {
    $('.config .barcode1D').show();
    $('.config .barcode2D').hide();
}

function showConfig2D() {
    $('.config .barcode1D').hide();
    $('.config .barcode2D').show();
}

function clearCanvas() {
    var canvas = $('#canvasTarget').get(0);
    var ctx = canvas.getContext('2d');
    ctx.lineWidth = 1;
    ctx.lineCap = 'butt';
    ctx.fillStyle = '#FFFFFF';
    ctx.strokeStyle = '#000000';
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.strokeRect(0, 0, canvas.width, canvas.height);
}

$(function () {
    $('input[name=btype]').click(function () {
        if ($(this).attr('id') == 'datamatrix') showConfig2D(); else showConfig1D();
    });
    $('input[name=renderer]').click(function () {
        if ($(this).attr('id') == 'canvas') $('#miscCanvas').show(); else $('#miscCanvas').hide();
    });
    generateBarcode();
});

/**
 *  End Barcode Generator Script
 *
 */

//  Date-Only Now
var d = new Date();
var month = d.getMonth() + 1;
var day = d.getDate();
var DateNow = (day < 10 ? '0' : '') + day + '-' +
    (month < 10 ? '0' : '') + month + '-' +
    d.getFullYear();

//  Scroll to table bottom
function ScrollTableBottom() {
    $('.table-section').scrollTop($('.table-section')[0].scrollHeight);
}


// Item Selector
$(document).on('click', '#itemTR', function () {
    $("tr#itemTR").removeClass('item-on-active').removeClass('badge-primary');
    $(this).addClass('badge-primary').addClass('item-on-active');
    // Current Item Price
    $("#CurrentItemPrice").text(parseFloat($(this).find('#itemPrice').text()) * parseFloat($(".item-on-active").find('#itemAmount').text()));
});


// Price / Item
$("#priceItem").on('click', function () {
    $("#models").html(`
            <div class="modal fade" id="priceItemModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">` + lang.product + ' ' + lang.price + ' ' + lang.amount + `</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body" id="ItemDescription"><div><label>` + lang.calculate + `</label><label> `+ lang.price + `</label><input type="number" class="form-control" id="CurrentPriceCalc" disabled value="` + $(".item-on-active").find("#itemPrice").text() + `"><label>` + lang.amount + `</label>
                            <input type="number" id="CurrentAmountCalc" class="form-control" value="1">
                            </div>
                            <br>
                            <div class="alert alert-primary" id="CalculatePAResult">` + $(".item-on-active").find("#itemPrice").text() + `</div>
                        </div>
                    </div>
                </div>
            </div>`);
    $('#CurrentAmountCalc').on('input', function () {
        $("#CalculatePAResult").text(parseFloat($("#CurrentPriceCalc").val()) * parseFloat($("#CurrentAmountCalc").val()));
    });
});

// Review Receipt
$("#ReviewReceipt").on("click", function () {
    $("#models").html(`
            <div class="modal fade" id="receiptModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="ReceiptViewBody"></div>
                    </div>
                </div>
            </div>
            `);
    $("#ReceiptViewBody").html($("#OrderTable").clone());

    $("#ReceiptViewBody #OrderTable tbody,#ReceiptViewBody #OrderTable tbody tr,#ReceiptViewBody #OrderTable tbody tr td").removeAttr('id').removeClass('badge-primary item-on-active');

    $("#ReceiptViewBody #OrderTable").removeClass().removeAttr('id');
});
//  Left Bottom Buttons (Under)
// Description Button - Model
$("#descriptionBtn").on('click', function () {
    $("#models").html(`
            <div class="modal fade" id="descriptionModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">` + lang.product + ' ' +lang.description + `</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="ItemDescription">` + $(".item-on-active").attr('data-product-description') + `</div>
                    </div>
                </div>
            </div>
    `);
});

// Remain Pieces in Stock Button - Model
$("#remainBtn").on('click', function () {
    let remainItems = parseFloat($(".item-on-active").attr('data-product-stock')) - parseFloat($(".item-on-active #itemAmount").text());
    $("#models").html(`
            <div class="modal fade" id="remainModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">` + lang.remain_pieces + `</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="ItemDescription">
                        <h6 class="text-success font-weight-bold">` + remainItems + `</h6>
                        </div>
                    </div>
                </div>
            </div>
    `);
});

// HighLight Item Button - Model
$("#highlightBtn").on('click', function () {
    $(".item-on-active").addClass('highlight-item');
});

// Show Delivery Form Function
function showDelivery() {
    $("#models").html(`
            <div class="modal fade" id="DeliveryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-header">
                            <h4>` + lang.delivery + `</h4>
                        </div>
                        <div class="modal-body" id="DeliveryForm">
                            <div class="form-group"><input type="text" class="form-control" id="ClientFullName" placeholder="` + lang.full_name + `"></div>
                            <div class="form-group"><input type="text" class="form-control" id="ClientPhone" placeholder="` + lang.phone + `"></div>
                            <div class="form-group"><input type="text" class="form-control" id="ClientAddress" placeholder="` + lang.address + `"></div>
                            <div class="form-group"><input type="text" class="form-control" id="DeliveryMan" placeholder="` + lang.delivery_man + `"></div>
                            <div class="form-group"><input type="number" class="form-control" id="DeliveryFee"` + setting_delivery_changeable + `
                             placeholder="` + lang.delivery_fee + `"></div>
                             <div class="form-group">
                                <button class="btn btn-success" id="ConfirmDeliveryData" type="button" data-dismiss="modal">` + lang.confirm + `</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`);
}

$("#Delivery").on("click", function () {
    showDelivery();
});


//  Reset Barcode
$("#ResetBarcode").click(function () {
    $("#barcodeNumber").val('');
    $(".barcode-status").html('');
});


//  Time Now

$(document).ready(function () {
    $('span#date-part').html(currentDate(new Date()))
    window.setInterval(function() {
        liveClock('span#time-part')
    }, 1000)
});

// Calculator
window.onload = function () {

    var current,
        screen,
        output,
        limit,
        zero,
        period,
        operator;

    screen = document.getElementById("result");

    var elem = document.querySelectorAll(".num");

    var len = elem.length;

    for (var i = 0; i < len; i++) {

        elem[i].addEventListener("click", function () {

            num = this.value;

            output = screen.value += num;

            limit = output.length;

            if (limit > 16) {

                alert("Sorry no more input is allowed");

            }

        }, false);

    }

    $("#delete").on('click', function () {
        screen.value = "";
    });


    $("#period").on('click', function () {
        period = this.value;
        if (screen.value === "") {
            output = screen.value = screen.value.concat("0.");
        } else if (screen.value === output) {
            screen.value = screen.value.concat(".");
        }
    });


    var elem1 = document.querySelectorAll(".operator");

    var len1 = elem1.length;

    for (var i = 0; i < len1; i++) {

        elem1[i].addEventListener("click", function () {

            operator = this.value;

            if (screen.value === "") {

                screen.value = screen.value.concat("");

            } else if (output) {

                screen.value = output.concat(operator);

            }

        }, false);

    }
}



//  Exit
$("#ExitPage").on('click', function () {
    Swal.fire({
        title: lang.are_you_sure_exit,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: lang.cancel,
        confirmButtonText: lang.yes
    }).then((result) => {
        if (result.value) {
            document.getElementById('logout-form').submit();
        }
    });
});


//  Delete Draft
$(document).on('click', '#DeleteDraft', function (event) {
    let DraftID = $(this).attr('data-draft-id');
    $(this).parents('.timeline').slideUp(1000);

    $.ajax({
        url:  `${url_delete_draft}/${DraftID}`,
        headers: headerParams,
        type: 'delete',
        dataType: "JSON",
        data: {
            '_token': $('meta[name="_token"]').attr('content'), "id": DraftID
        },
    });
});


//  Close Drafts

$("#CloseDrafts").on('click', function () {
    $("#drafts").css('width', "0");
    $("#DraftOne").html('');
});


//  Add order to client
let Client = false;
let ClientName = null;
let ClientCode = null;
$(document).on('click', '#ClientOrder', function (event) {
    let ClientOrderBtn = $(this);
    event.preventDefault();

    /**
     *
     * if 0 then there's no user added to the order
     * if 1 there's a user added and opens the modal of user data
     */
    if(ClientOrderBtn.attr('data-type') == 0) {
        ClientOrderBtn.replaceWith(`<input value="" id="ClientCodeInput" style="width: 130px;" type="text" class="form-control" autofocus/>`);
        $("#ClientCodeInput").focus();
    }else {
        $('#ClientModal').modal('show');
    }
});

$(document).keydown(function (event) {
    if (event.which == "13" && $("#ClientCodeInput")[0]) {
        let code = $("#ClientCodeInput").val();
        $.ajax({
            type: "get",
            dataType: 'json',
            url:  `${url_get_search_client}/${code}`,
            headers: headerParams,
            beforeSend: function () {
                $(".barcode-status").html(`<i class="fas fa-circle-notch fa-spin"></i> ` + lang.client_searching);
            },
            success: function (data) {
                if (data === 'empty') {
                    $(".barcode-status").html(`<i class="fas fa-times"></i> ` + lang.no_client_exists);
                    $('#ClientOrder').attr('data-type', 0);
                } else {
                    $('#ClientOrder').removeClass('btn-info').addClass('btn-success');
                    $('#ClientOrder').attr('data-type', 1);
                    $(".barcode-status").html(`<i class="fas fa-check-circle"></i> ` + lang.order_added_to + data.name);


                    // Store client data used in confirm pay order
                    Client = true;
                    ClientName = data.name;
                    ClientCode = data.code;

                    // Add Data to client modal
                    $("#ClientFullName").html(data.name);
                    $("#ClientPhone").html(data.phone.join(' - '));
                    $("#ClientCode").html(data.code);
                    $("#ClientGift").html(data.gift);

                    barcodeFocus();
                }
            }
        });

        // Bring back the button
        $("#ClientCodeInput").replaceWith(`<button class="btn btn-info" data-toggle="tooltip" data-placement="bottom" title="${lang.client}" id="ClientOrder"><i class="far fa-user"></i></button>`);
    }
});
$("button[data-dismiss='client']").click(function() {
    // Destroy stored client data
    Client = false;
    ClientName = null;
    ClientCode = null;
    // Set client button to able to add clients and close modal section
    $('#ClientOrder').attr('data-type', 0).removeClass('btn-success').addClass('btn-info');
    $('#ClientModal').modal('hide');

    clearStatus();
    barcodeFocus();
});

