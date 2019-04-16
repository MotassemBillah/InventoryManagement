$(document).ready(function() {
    disable("#type");

    $(document).on("click", "#search", function(e) {
        showLoader("Processing...", true);
        var _form = $("#frmSearch");

        $.ajax({
            type: "POST",
            url: ajaxUrl + "/product",
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
            url: ajaxUrl + "/product",
            data: _form.serialize(),
            success: function(res) {
                showLoader("", false);
                $("#ajaxContent").html('');
                $("#ajaxContent").html(res);
            }
        });
        e.preventDefault();
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
                url: ajaxUrl + "/product/damage_stock",
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
                url: ajaxUrl + "/product",
                data: _form.serialize(),
                success: function(res) {
                    showLoader("", false);
                    $("#ajaxContent").html('');
                    $("#ajaxContent").html(res);
                }
            });
        }
    });

    $(document).on("change", "#deleteForm input[type='checkbox']", function(e) {
        $("#checkAll").prop("checked", false);
        $("#deleteForm input[type='checkbox']").prop("checked", false);
        $("tr").removeClass("bg-danger");
        $(this).prop("checked", true);
        $(this).closest("tr").addClass("bg-danger");
        disable("#checkAll");
        enable("#admin_del_btn");
        e.preventDefault();
    });

    $(document).on('click', '#admin_del_btn', function(e) {
        if ($("input.check:checked").length == 0) {
            $("#ajaxMessage").removeClass('alert-danger').addClass('alert-warning').html("");
            $("#ajaxMessage").html("No row selected. Please select 1 row.").show();
            showLoader("", false);
            return false;
        } else if ($("input.check:checked").length > 1) {
            $("#ajaxMessage").removeClass('alert-danger').addClass('alert-warning').html("");
            $("#ajaxMessage").html("You cannot select more than 1 row.").show();
            showLoader("", false);
            return false;
        } else {
            hide_ajax_message();
            showLoader("Processing...", true);
            var _productID = $("input.check:checked").val();
            var _url = ajaxUrl + '/product/history?productID=' + _productID;
            $("#containerForHistory").load(_url, function() {
                $("#containerForHistory").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                showLoader("", false);
            });
        }
        e.preventDefault();
    });
});