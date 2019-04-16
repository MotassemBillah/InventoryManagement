$(document).ready(function() {
    disable("#type");

    $(document).on("click", ".sort", function(e) {
        showLoader("Processing...", true);
        var _column = $(this).attr('data-column');
        var _order = $(this).attr('data-info');
        var _url = ajaxUrl + "/product/list";

        $.post(_url, {column: _column, order: _order}, function(res) {
            $("#ajaxContent").html('');
            $("#ajaxContent").html(res);
            showLoader("", false);
        });
        e.preventDefault();
    });

    $(document).on("click", "#search", function(e) {
        showLoader("Processing...", true);
        var _form = $("#frmSearch");

        $.ajax({
            type: "POST",
            url: ajaxUrl + "/product/list",
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
            url: ajaxUrl + "/product/list",
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
            var _url = ajaxUrl + '/product/deleteall';

            $.post(_url, _form.serialize(), function(res) {
                if (res.success === true) {
                    $("#ajaxMessage").removeClass('alert-danger').addClass('alert-success').html("");
                    $("#ajaxMessage").html(res.message).show();
                    $("tr.bg-danger").remove();
                    setTimeout(hide_ajax_message, 3000);
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

    $(document).on("click", ".delete_size", function() {
        showLoader("Processing...", true);
        var $this = $(this);
        var elem = $this.parent();
        var sizeID = $this.attr('data-info');
        var _url = ajaxUrl + '/product/remove_size';

        $.getJSON(_url, {'size': sizeID, ajax: true}, function(data) {
            if (data.success === true) {
                $(elem).remove();
                $("#popup").html("Size remove successfull!").show();
                setTimeout(hidePopup, 4000);
            } else {
                $("#popup").html("Error! Try again.").show();
                setTimeout(hidePopup, 4000);
            }
            showLoader("", false);
        });
    });

    $(document).on("change", "#company_id", function() {
        showLoader("Processing...", true);
        var _url = ajaxUrl + "/company/findmeta";

        if ($(this).val() !== "") {
            enable("#type");
            $.post(_url, {com_id: $(this).val()}, function(response) {
                if (response.success === true) {
                    $("#type").html(response.html);
                } else {
                    $("#type").html(response.html);
                }
                showLoader("", false);
            }, "json");
        } else {
            disable("#type");
            $("#type").html("<option value=''>Company Group</option>");
            showLoader("", false);
        }
    });

    $(document).on("change", "#damaged", function() {
        showLoader("Processing...", true);
        var _form = $("#frmSearch");

        if ($(this).is(":checked") === true) {
            $.ajax({
                type: "POST",
                url: ajaxUrl + "/product/damage_list",
                data: _form.serialize(),
                success: function(res) {
                    showLoader("", false);
                    $("#ajaxContent").html('');
                    $("#ajaxContent").html(res);
                }
            });
        } else {
            $.ajax({
                type: "POST",
                url: ajaxUrl + "/product/list",
                data: _form.serialize(),
                success: function(res) {
                    showLoader("", false);
                    $("#ajaxContent").html('');
                    $("#ajaxContent").html(res);
                }
            });
        }
    });
});