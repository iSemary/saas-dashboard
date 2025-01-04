// Category Selector
$(document).on("change", "#CategorySelect", function(e) {
    var id = $(this).val();
    if (id == "") {
        return false;
    }

    $.ajax({
        type: "get",
        dataType: "json",
        url: `${url_search_category_get_sub}/${id}`,
        headers: headerParams,
        beforeSend: function() {
            barcodeSearchingStatus();
        },
        success: function(data) {
            if (data.length == 0) {
                $("#SubCategorySelect").html(
                    `<option value="">` +
                        lang.choose +
                        " " +
                        lang.sub_category +
                        `</option>`
                );
                $("#SubCategorySelect, #ProductSelect").prop("disabled", true);
                barcodeNotFoundStatus();
            } else {
                barcodeFoundStatus();

                $("#SubCategorySelect")
                    .html(
                        `<option value="">` +
                            lang.choose +
                            " " +
                            lang.sub_category +
                            `</option>`
                    )
                    .prop("disabled", false);
                $("#ProductSelect").html(
                    `<option value="">` +
                        lang.choose +
                        " " +
                        lang.product +
                        `</option>`
                );

                $.each(data, function(dataMy) {
                    $("#SubCategorySelect").append(
                        "<option value='" +
                            data[dataMy].id +
                            "'>" +
                            data[dataMy].name +
                            "</option>"
                    );
                });
            }
        }
    });
});
// Sub Category Selector
$(document).on("change", "#SubCategorySelect", function(e) {
    let id = $(this).val();
    if (id == "") {
        return false;
    }
    $.ajax({
        type: "get",
        dataType: "json",
        url: `${url_search_sub_get_product}/${id}`,
        headers: headerParams,
        beforeSend: function() {
            barcodeSearchingStatus();
        },
        success: function(data) {
            $(".barcode-status").html("");
            if (data.length == 0) {
                $("#ProductSelect").html(
                    `<option value="">` +
                        lang.choose +
                        " " +
                        lang.product +
                        `</option>`
                );
                $("#ProductSelect").prop("disabled", true);
                barcodeNotFoundStatus();
            } else {
                barcodeFoundStatus();
                $("#ProductSelect")
                    .prop("disabled", false)
                    .html("");

                $("#ProductSelect").append(
                    `<option value="0">` +
                        lang.choose +
                        " " +
                        lang.product +
                        `</option>`
                );

                $.each(data, function(i) {
                    ProductID = data[i].id;
                    ProductName = data[i].name;
                    ProductBranch = data[i].branch;
                    ProductAmount = data[i].amount;
                    ProductAmountType = data[i].amount_type;
                    ProductPurchasePrice = data[i].purchase_price;

                    $("#ProductSelect").append(
                        `<option data-purchase-price=`+ProductPurchasePrice+` value="` +
                            ProductID +
                            `">` +
                            ProductName +
                            ` : (` +
                            ProductAmount +
                            ProductAmountType +
                            `) : ` +
                            (ProductBranch ?? '')+
                            `</option>`
                    );
                });
            }
        }
    });
});
