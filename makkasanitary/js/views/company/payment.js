$(document).ready(function() {
    $("#from_date, #to_date").datepicker({
        format: 'dd-mm-yyyy'
    });

    $(document).on("click", "#search", function(e) {
        showLoader("Processing...", true);
        var _form = $("#frmSearch");

        $.ajax({
            type: "POST",
            url: ajaxUrl + "/company/payment",
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
            var _url = ajaxUrl + '/company/deleteall_payment';

            $.post(_url, _form.serialize(), function(res) {
                if (res.success === true) {
                    $("#ajaxHandler").show().removeClass('alert-danger').addClass('alert-success');
                    $("#ajaxMessage").html("");
                    $("#ajaxMessage").html(res.message).show();
                    $("tr.bg-danger").remove();
                    setTimeout(location.reload(), 4000);
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
});