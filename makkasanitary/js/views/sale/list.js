$(document).ready(function() {
    $(document).on("change", "#frmSale input[type='checkbox']", function() {
        if ($(this).is(":checked")) {
            $(this).closest('tr').css('background-color', '#faebcc');
        } else {
            $(this).closest('tr').removeAttr('style');
        }
    });

    $(document).on("click", "#btnSale", function(e) {
        showLoader("Processing...", true);
        var _form = $("#frmSale");
        var _url = ajaxUrl + '/sales/create';
        var _saleUrl = baseUrl + '/sales';

        $.post(_url, _form.serialize(), function(res) {
            if (res.success === true) {
                $("#ajaxMessage").removeClass('alert-danger').addClass('alert-success').html("");
                $("#ajaxMessage").html(res.message).show();
                setTimeout(redirectTo(_saleUrl), 3000);
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
            url: ajaxUrl + "/sales",
            data: _form.serialize(),
            success: function(res) {
                showLoader("", false);
                $("#ajaxContent").html('');
                $("#ajaxContent").html(res);
            }
        });
        e.preventDefault();
    });

    $(document).on("click", "#printMemo", function(e) {
        var _form = $("#frmSearch");

        if ($("#from_date").val() == "") {
            $("#ajaxMessage").showAjaxMessage({html: "You must select date range from value.", type: 'error'});
            $("#from_date").css('border-color', 'red');
            $("#from_date").focus();
            return false;
        } else {
            showLoader("Processing...", true);
            $("#from_date").removeAttr('style');
            $.ajax({
                type: "POST",
                url: ajaxUrl + "/sales/export",
                data: _form.serialize(),
                success: function(res) {
                    showLoader("", false);
                    $("#ajaxContent").html('');
                    $("#ajaxContent").html(res);
                },
                error: function(res) {
                    $("#ajaxMessage").showAjaxMessage({html: res.message, type: 'error'});
                }
            });
        }
        e.preventDefault();
    });

    $(document).on('click', '#admin_del_btn', function(e) {
        var _rc = confirm('Are you sure about this action? This cannot be undone!');

        if (_rc === true) {
            showLoader("Processing...", true);
            var _form = $("#deleteForm");
            var _url = ajaxUrl + '/sales/deleteall';

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

    $(document).on('click', '#admin_reset_btn', function(e) {
        var _rc = confirm('Are You Sure About Resetting Invoice Data??? This Process Cannot Be Undone!');

        if (_rc === true) {
            showLoader("Processing...", true);
            var _form = $("#deleteForm");
            var _url = ajaxUrl + '/sales/resetall';

            $.post(_url, _form.serialize(), function(res) {
                if (res.success === true) {
                    $("#ajaxHandler").show();
                    $("#ajaxMessage").removeClass('alert-danger').addClass('alert-success').html("");
                    $("#ajaxMessage").html(res.message).show();
                    redirectTo(baseUrl + '/sales');
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
        var _url = ajaxUrl + '/sales/process';

        $.get(_url, {id: _key}, function(res) {
            if (res.success === true) {
                $("#ajaxMessage").removeClass('alert-danger').addClass('alert-success').html("");
                $("#ajaxMessage").html(res.message).show();
                $.ajax({
                    type: "POST",
                    url: ajaxUrl + "/sales",
                    data: _form.serialize(),
                    success: function(res) {
                        showLoader("", false);
                        $("#ajaxContent").html('');
                        $("#ajaxContent").html(res);
                    }
                });
                setTimeout(hide_ajax_message, 3000);
            } else {
                $("#ajaxMessage").removeClass('alert-success').addClass('alert-danger').html("");
                $("#ajaxMessage").html(res.message).show();
            }
            showLoader("", false);
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