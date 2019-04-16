$(document).ready(function() {
    $(document).on("change", ".customer_toggle", function(e) {
        if ($(this).val() == "new") {
            disable("#customer_id");
            $("#new_customer_form").slideDown(150);
        } else {
            $("#new_customer_form").slideUp(150);
            enable("#customer_id");
        }
        e.preventDefault();
    });

    $(document).on("change", "#customer_id", function(e) {
        if ($(this).val() == "") {
            $("#existing_customer").prop("checked", false);
            $("#new_customer").prop("checked", true);
            $("#new_customer_form").slideDown(150);
        } else {
            $("#new_customer").prop("checked", false);
            $("#existing_customer").prop("checked", true);
            $("#new_customer_form").slideUp(150);

        }

        e.preventDefault();
    });

    $(document).on("submit", "#saveCart", function(e) {
        if ($("#new_customer_form").is(":visible")) {
            if ($("#Customer_name").val() == "") {
                showMessage("Please enter customer name");
                $("#Customer_name").focus();
                $("#Customer_name").parent().addClass('has-error');
                return false;
            }

            if ($("#Customer_phone").val() == "") {
                $("#Customer_phone").parent().addClass('has-error');
                $("#Customer_phone").focus();
                showMessage("Please enter customer phone number");
                return false;
            }
        } else {
            if ($("#customer_id").val() == "") {
                showMessage("Please select a customer");
                return false;
            }
        }
    });

    $(document).on("click", "#customerSearch", function(e) {
        if ($("#customer").val() == "") {
            $("#customer").parent().addClass('has-error');
            $("#customer").focus();
            showMessage("Please enter customer name to search");
            return false;
        } else {
            hideMessage();
        }

        showLoader("Processing...", true);
        var _form = $("#saveCart");
        var _url = ajaxUrl + '/customer/search';

        $.post(_url, _form.serialize(), function(data) {
            if (data.success === true) {
                $("#new_customer").prop("checked", false);
                $("#existing_customer").prop("checked", true);
                $("#new_customer_form").slideUp(150);
                enable("#customer_id");
                $("#customer_id").html(data.html);
                $("#customer_id").trigger("click");
            } else {
                disable("#customer_id").html('');
                $("#new_customer_form").slideDown(150);
                $("#Customer_name").val($("#customer").val());
                $("#new_customer").prop("checked", true);
                $("#existing_customer").prop("checked", false);
            }
            showLoader("", false);
        }, "json");
        e.preventDefault();
    });
});