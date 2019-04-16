$(document).ready(function() {
    $(document).on("change", "#frmCreatePurchase input[type='checkbox']", function() {
        if ($(this).is(":checked")) {
            $(this).closest('tr').css('background-color', '#faebcc');
        } else {
            $(this).closest('tr').removeAttr('style');
        }
    });

    $(document).on("click", "#btnPurchase", function(e) {
        showLoader("Processing...", true);
        var _form = $("#frmCreatePurchase");
        var _url = ajaxUrl + '/purchase/create';
        var _purchaseUrl = baseUrl + '/purchase';

        $.post(_url, _form.serialize(), function(res) {
            if (res.success === true) {
                $("#ajaxMessage").removeClass('alert-danger').addClass('alert-success').html("");
                $("#ajaxMessage").html(res.message).show();
                setTimeout(redirectTo(_purchaseUrl), 2000);
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
            url: ajaxUrl + "/purchase",
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
            var _url = ajaxUrl + '/purchase/deleteall';

            $.post(_url, _form.serialize(), function(res) {
                if (res.success === true) {
                    $("#ajaxHandler").show().removeClass('alert-danger').addClass('alert-success');
                    $("#ajaxMessage").html("");
                    $("#ajaxMessage").html(res.message).show();
                    $("tr.bg-danger").remove();
                    setTimeout(location.reload(), 3000);
                } else {
                    $("#ajaxHandler").show().removeClass('alert-success').addClass('alert-danger');
                    $("#ajaxMessage").html("");
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

    $(document).on('click', '#admin_reset_btn', function(e) {
        var _rc = confirm('Are You Sure About Resetting Invoice Data??? This Process Cannot Be Undone!');

        if (_rc === true) {
            showLoader("Processing...", true);
            var _form = $("#deleteForm");
            var _url = ajaxUrl + '/purchase/resetall';

            $.post(_url, _form.serialize(), function(res) {
                if (res.success === true) {
                    $("#ajaxHandler").show();
                    $("#ajaxMessage").removeClass('alert-danger').addClass('alert-success').html("");
                    $("#ajaxMessage").html(res.message).show();
                    redirectTo(baseUrl + '/purchase');
                } else {
                    $("#ajaxHandler").show();
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
        var _form = $("#frmSearch");
        var _key = $(this).attr("data-info");
        var _url = ajaxUrl + '/purchase/process';

        $.get(_url, {id: _key}, function(res) {
            if (res.success === true) {
                $("#ajaxMessage").html("").removeClass('alert-danger').addClass('alert-success');
                $("#ajaxMessage").html(res.message).show();
                $.ajax({
                    type: "POST",
                    url: ajaxUrl + "/purchase",
                    data: _form.serialize(),
                    success: function(res) {
                        showLoader("", false);
                        $("#ajaxContent").html('');
                        $("#ajaxContent").html(res);
                    }
                });
                setTimeout(hide_ajax_message, 3000);
            } else {
                $("#ajaxMessage").html("").removeClass('alert-success').addClass('alert-danger');
                $("#ajaxMessage").html(res.message).show();
            }
            showLoader("", false);
        }, "json");
        e.preventDefault();
    });
});