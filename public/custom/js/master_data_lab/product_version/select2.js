$(document).ready(function () {
    $("#category").select2({
        placeholder: "Select Product Category"
    });
    $("#product").select2({
        placeholder: "Select Product"
    });
    $("#model").select2({
        placeholder: "Select Product Model"
    });

    $("#category").on("change", function () {
        var brand = $("#brand").val();
        var category = $(this).val();
        $("#product").empty().trigger("change");
        $("#model").empty().trigger("change");

        $.ajax({
            type: "GET",
            url: "product",
            data: {brand:brand,category:category},
            dataType: "JSON",
            success: function (response) {
                var new_option = [];
                for (let i = 0; i < response.length; i++) {
                    new_option[i] = new Option(response[i].text, response[i].id, false, false);
                }
                $('#product').append(new_option).trigger('change');
            }
        });
    });

    $("#product").on("change", function () {
        var brand = $("#brand").val();
        var category = $("#category").val();
        var product = $(this).val();
        $("#model").empty().trigger("change");

        $.ajax({
            type: "GET",
            url: "model",
            data: {brand:brand,category:category,product:product},
            dataType: "JSON",
            success: function (response) {
                var new_option = [];
                for (let i = 0; i < response.length; i++) {
                    new_option[i] = new Option(response[i].text, response[i].id, false, false);
                }
                $('#model').append(new_option).trigger('change');
            }
        });
    });
});