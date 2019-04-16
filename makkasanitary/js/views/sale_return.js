$(document).ready(function() {
    $(document).on("change", "#frmSaleReturn input[type='checkbox']", function() {
        if ($(this).is(":checked")) {
            $(this).closest('tr').css('background-color', '#faebcc');
        } else {
            $(this).closest('tr').removeAttr('style');
        }
    });

    $(document).on("click", "#btnSaleReturn", function(e) {
        showLoader("Processing...", true);
        var _form = $("#frmSaleReturn");
        var _url = ajaxUrl + '/sales/create_return';
        var _saleReturnUrl = baseUrl + '/sales_return';

        $.post(_url, _form.serialize(), function(res) {
            if (res.success === true) {
                $("#ajaxMessage").removeClass('alert-danger').addClass('alert-success').html("");
                $("#ajaxMessage").html(res.message).show();
                setTimeout(redirectTo(_saleReturnUrl), 3000);
            } else {
                $("#ajaxMessage").removeClass('alert-success').addClass('alert-danger').html("");
                $("#ajaxMessage").html(res.message).show();
            }
            showLoader("", false);
        }, "json");
        e.preventDefault();
        return false;
    });

    $(document).on("click", "#search", function(e) {
        showLoader("Processing...", true);
        var _form = $("#frmSearch");

        $.ajax({
            type: "POST",
            url: ajaxUrl + "/sales/return_items",
            data: _form.serialize(),
            success: function(res) {
                showLoader("", false);
                $("#ajaxContent").html('');
                $("#ajaxContent").html(res);
            }
        });
        e.preventDefault();
    });

    $(document).on("click", "#searchSale", function(e) {
        showLoader("Processing...", true);
        var _form = $("#frmSearch");

        if ($("#invoice_no").val() == "") {
            $("#ajaxMessage").removeClass('alert-success').addClass('alert-danger').html("");
            $("#ajaxMessage").html("you must supply a invoice number").show();
            showLoader("", false);
            return false;
        } else {
            $("#ajaxMessage").html("").slideUp(200);
            showLoader("", false);
        }

        $.ajax({
            type: "POST",
            url: ajaxUrl + "/sales/view",
            data: _form.serialize(),
            success: function(res) {
                showLoader("", false);
                $("#ajaxContent").html('');
                $("#ajaxContent").html(res);
            }
        });
        e.preventDefault();
    });

    $(document).on('click', '#admin_del_btn', function(e) {
        var _rc = confirm('Are you sure about this action? This cannot be undone!');

        if (_rc === true) {
            showLoader("Processing...", true);
            var _form = $("#deleteForm");
            var _url = ajaxUrl + '/sales/deleteall_return';

            $.post(_url, _form.serialize(), function(res) {
                if (res.success === true) {
                    $("#ajaxMessage").removeClass('alert-danger').addClass('alert-success').html("");
                    $("#ajaxMessage").html(res.message).show();
                    $("tr.bg-danger").remove();
                    setTimeout(location.reload(), 3000);
                } else {
                    $("#ajaxMessage").removeClass('alert-success').addClass('alert-danger').html("");
                    $("#ajaxMessage").html(res.message).show();
                }
                reset_index();
                showLoader("", false);
            }, "json");
        } else {
            return false;
        }
        e.preventDefault();
    });

    $(document).on("click", ".process_order", function(e) {
        showLoader("Processing...", true);
        var _key = $(this).attr("data-info");
        var _url = ajaxUrl + '/sales/process_return';
        var _urlData = baseUrl + '/sales_return';

        $.get(_url, {id: _key}, function(res) {
            if (res.success === true) {
                $("#ajaxMessage").removeClass('alert-danger').addClass('alert-success').html("");
                $("#ajaxMessage").html(res.message).show();
                redirectTo(_urlData);
            } else {
                $("#ajaxMessage").removeClass('alert-success').addClass('alert-danger').html("");
                $("#ajaxMessage").html(res.message).show();
                setTimeout(hide_ajax_message, 3000);
            }
            showLoader("", false);
        }, "json");
        e.preventDefault();
    });
});