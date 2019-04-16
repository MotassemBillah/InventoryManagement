$(document).ready(function() {
    $(document).on("change", "#frmSale input[type='checkbox']", function() {
        if ($(this).is(":checked")) {
            $(this).closest('tr').css('background-color', '#faebcc');
        } else {
            $(this).closest('tr').removeAttr('style');
        }
    });

    $(document).on("change", "#company", function() {
        showLoader("Processing...", true);
        var _url = ajaxUrl + "/company/findmeta";
        if ($(this).val() !== "") {
            $.post(_url, {com_id: $(this).val()}, function(response) {
                if (response.success === true) {
                    $("#type").html(response.html);
                } else {
                    $("#type").html(response.html);
                }
                showLoader("", false);
            }, "json");
        } else {
            $("#type").html("<option value=''>All</option>");
        }
    });

    $(document).on("change", "#company, #type, #category", function(e) {
        showLoader("Processing...", true);
        var _form = $("#frmSearch");
        $.ajax({
            type: "POST",
            url: ajaxUrl + "/sales/search",
            data: _form.serialize(),
            success: function(res) {
                showLoader("", false);
                $("#ajaxContent").html('');
                $("#ajaxContent").html(res);
            }
        });
        e.preventDefault();
    });

    $(document).on("input", "input#q", function(e) {
        showLoader("Processing...", true);
        var _form = $("#frmSearch");
        $.ajax({
            type: "POST",
            url: ajaxUrl + "/sales/search",
            data: _form.serialize(),
            success: function(res) {
                showLoader("", false);
                $("#ajaxContent").html('');
                $("#ajaxContent").html(res);
            }
        });
        e.preventDefault();
    });

    $(document).on("change", "#frmSale input[type='checkbox']", function() {
        if ($(this).is(":checked") === false) {
            $("#ajaxMessage").html("").hide();
        }
    });

    $(document).on("click", ".add_to_cart", function(e) {
        var _id = $(this).attr('data-rel');
        var _txt = $(this).attr('data-info');
        var _url = ajaxUrl + '/sales/add_to_cart';
        var _inputProduct = $("#product_" + _id);
        var _inputQty = $("#qty_" + _id);
        var _inputPrice = $("#price_" + _id);

        if ($(_inputProduct).is(":checked") === false) {
            $("#ajaxMessage").removeClass('alert-success').addClass('alert-danger').html("");
            $("#ajaxMessage").html("Product " + _txt + " need to be selected").show();
            return false;
        }

        if ($(_inputQty).val() == "") {
            $("#ajaxMessage").removeClass('alert-success').addClass('alert-danger').html("");
            $("#ajaxMessage").html("Quantity required for " + _txt).show();
            return false;
        }

        if ($(_inputPrice).val() == "") {
            $("#ajaxMessage").removeClass('alert-success').addClass('alert-danger').html("");
            $("#ajaxMessage").html("Price required for " + _txt).show();
            return false;
        }

        $.post(_url, {pid: _id, qty: $(_inputQty).val(), price: $(_inputPrice).val()}, function(response) {
            if (response.success === true) {
                $("#ajaxMessage").removeClass('alert-danger').addClass('alert-success').html("");
                $("#ajaxMessage").html(response.message).show();
                $(_inputProduct).prop("checked", false);
                $(_inputQty).val("");
                $(_inputPrice).val("");
                $("#tr_" + _id).removeAttr("style");
                setTimeout(hide_ajax_message, 3000);
            } else {
                $("#ajaxMessage").removeClass('alert-success').addClass('alert-danger').html("");
                $("#ajaxMessage").html(response.message).show();
            }
        }, "json");
        e.preventDefault();
    });

    $(document).on("change", ".customer_toggle", function(e) {
        var $this = $(this);
        var target = $this.attr('data-target');
        $(".customer_div").slideUp(200);
        $(target).slideDown(200);
        e.preventDefault();
    });
});