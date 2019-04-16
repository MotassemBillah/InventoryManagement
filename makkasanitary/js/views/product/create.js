$(document).ready(function() {
    $("#Product_type").html("<option data-class='error'>Select Company !</option>").addClass('error');
    $(".type_dropdown").html("<option data-class='error'>Select Company !</option>").addClass('error');
    disable("#Product_type");
    disable(".type_dropdown");

    $(document).on("change", "#Product_company_id", function() {
        showLoader("Fetching data...", true);
        var _url = ajaxUrl + "/company/findmeta";

        if ($(this).val() !== "") {
            $("#Product_type").removeClass('error');
            $(".type_dropdown").removeClass('error');
            $.post(_url, {com_id: $(this).val()}, function(response) {
                if (response.success === true) {
                    $("#Product_type").html(response.html);
                    $(".type_dropdown").html(response.html);
                    enable("#Product_type");
                    enable(".type_dropdown");
                } else {
                    $("#Product_type").html(response.html);
                    $(".type_dropdown").html(response.html);
                    disable(".pro_type_dropdown");
                    disable(".type_dropdown");
                }
                showLoader("", false);
            }, "json");
        } else {
            $("#Product_type").html("<option data-class='error'>Select Company !</option>").addClass('error');
            $(".type_dropdown").html("<option data-class='error'>Select Company !</option>").addClass('error');
            disable("#Product_type");
            disable(".type_dropdown");
            showLoader("", false);
        }
    });

    $(document).on("change", "#Product_unit", function() {
        if ($(this).val() == "") {
            $("#Product_unitsize").val("");
            disable("#Product_unitsize");
        } else {
            enable("#Product_unitsize");
        }
    });
});