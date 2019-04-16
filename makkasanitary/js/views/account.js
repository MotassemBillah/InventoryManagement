$(document).ready(function() {
    $(document).on("click", "#search", function(e) {
        showLoader("Processing...", true);
        var _form = $("#frmSearch");

        $.ajax({
            type: "POST",
            url: baseUrl + "/account/search",
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
        var _form = $("#frmSearch");

        $.ajax({
            type: "POST",
            url: baseUrl + "/account/search",
            data: _form.serialize(),
            success: function(res) {
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
            var _url = baseUrl + '/account/deleteall';

            $.post(_url, _form.serialize(), function(res) {
                if (res.success === true) {
                    $("#ajaxMessage").showAjaxMessage({html: res.message, type: 'success'});
                    $("tr.bg-danger").remove();
                    $("search").trigger('click');
                } else {
                    $("#ajaxMessage").showAjaxMessage({html: res.message, type: 'error'});
                }
                reset_index();
                showLoader("", false);
            }, "json");
        } else {
            return false;
        }
        e.preventDefault();
    });

    $(document).on("click", ".detail", function(e) {
        showLoader("Processing...", true);
        var _key = $(this).attr("data-info");
        var _url = baseUrl + '/account/add_balance?id=' + _key;

        $("#containerForDetailInfo").load(_url, function() {
            $("#containerForDetailInfo").modal({
                backdrop: 'static',
                keyboard: false
            });
            showLoader("", false);
        });
        e.preventDefault();
    });

    $(document).on("submit", "#frmAccountBalance", function(e) {
        showLoader("Processing...", true);
        var _form = $(this);
        var _url = baseUrl + '/account/save_balance';

        $.post(_url, _form.serialize(), function(res) {
            if (res.success === true) {
                $("#ajaxModalMessage").removeClass('alert-danger').addClass('alert-success').html("");
                $("#ajaxModalMessage").html(res.message).show();
                redirectTo(baseUrl + '/account');
            } else {
                $("#ajaxModalMessage").removeClass('alert-success').addClass('alert-danger').html("");
                $("#ajaxModalMessage").html(res.message).show();
            }
            showLoader("", false);
        }, "json");
        e.preventDefault();
        return false;
    });
});