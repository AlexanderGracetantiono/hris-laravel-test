$(document).ready(function () {
    $("#brand").select2({
        placeholder: "Select Product Brand"
       });
    $("#category").select2({
        placeholder: "Select Product Category"
    });

    $("#brand").on("change", function () {
        var brand = $(this).val();
        $("#category").empty().trigger("change");
        $("#product").empty().trigger("change");
        $("#model").empty().trigger("change");

        $.ajax({
            type: "GET",
            url: "category",
            data: {brand:brand},
            dataType: "JSON",
            success: function (response) {
                var new_option = [];
                for (let i = 0; i < response.length; i++) {
                    new_option[i] = new Option(response[i].text, response[i].id, false, false);
                }
                $('#category').append(new_option).trigger('change');
            }
        });
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
});