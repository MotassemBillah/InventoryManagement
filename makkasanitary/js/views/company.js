$(document).ready(function() {
    $(document).on("click", "#search", function(e) {
        showLoader("Processing...", true);
        var _form = $("#frmSearch");

        $.ajax({
            type: "POST",
            url: baseUrl + "/company/search",
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
            var _url = baseUrl + '/company/deleteall';

            $.post(_url, _form.serialize(), function(res) {
                if (res.success === true) {
                    $("#ajaxMessage").showAjaxMessage({html: res.message, type: 'success'});
                    $("tr.bg-danger").remove();
                    $("#search").trigger('click');
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
        var _url = ajaxUrl + '/company/view?id=' + _key;

        $("#containerForDetailInfo").load(_url, function() {
            $("#containerForDetailInfo").modal({
                backdrop: 'static',
                keyboard: false
            });
            showLoader("", false);
        });
        e.preventDefault();
    });
});