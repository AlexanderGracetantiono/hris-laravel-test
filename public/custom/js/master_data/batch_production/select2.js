function select2() {
    $(".employee").select2({
        placeholder: "Select user"
    });
    $("#product_category_select2").select2({
        placeholder: "Select product category"
    });
    $("#product_select2").select2({
        placeholder: "Select product"
    });
    $("#product_model_select2").select2({
        placeholder: "Select product model"
    });
    $("#product_version_select2").select2({
        placeholder: "Select product version"
    });
    $("#plant_select2").select2({
        placeholder: "Select production center"
    });
    $("#plant_packaging_select2").select2({
        placeholder: "Select packaging center"
    });

    $("#product_category_select2").on("change", function () {
        var brand = $("#brand_select2").val();
        var category = $(this).val();
        $("#product_select2").empty().trigger("change");
        $("#product_model_select2").empty().trigger("change");
        $("#product_version_select2").empty().trigger("change");
        $("#sku").val("");
        $("#description").val("");

        if (brand && category) {
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
                    $('#product_select2').append(new_option).trigger('change');
                }
            });
        }
    });
    $("#product_select2").on("change", function () {
        var brand = $("#brand_select2").val();
        var category = $("#product_category_select2").val();
        var product = $(this).val();
        $("#product_model_select2").empty().trigger("change");
        $("#product_version_select2").empty().trigger("change");
        $("#sku").val("");
        $("#description").val("");

        if (brand && category && product) {
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
                    $('#product_model_select2').append(new_option).trigger('change');
                }
            });
        }
    });
    $("#product_model_select2").on("change", function () {
        var brand = $("#brand_select2").val();
        var category = $("#product_category_select2").val();
        var product = $("#product_select2").val();
        var model = $(this).val();
        $("#sku").val("");
        $("#description").val("");

        if (brand && category && product && model) {
            $.ajax({
                type: "GET",
                url: "version",
                data: {brand:brand,category:category,product:product,model:model},
                dataType: "JSON",
                success: function (response) {
                    var new_option = [];
                    for (let i = 0; i < response.length; i++) {
                        new_option[i] = new Option(response[i].text, response[i].id, false, false);
                    }
                    $('#product_version_select2').append(new_option).trigger('change');
                }
            });
        }
    });
    $("#product_version_select2").on("change", function () {
        var brand = $("#brand_select2").val();
        var category = $("#product_category_select2").val();
        var product = $("#product_select2").val();
        var model = $("#product_model_select2").val();
        var version = $(this).val();
        $("#sku").val("");
        $("#description").val("");

        if (brand && category && product && model && version) {
            $.ajax({
                type: "GET",
                url: "description",
                data: {version:version},
                dataType: "JSON",
                success: function (response) {
                    $("#sku").val(response.MPRVE_SKU);
                    $("#description").val(response.MPRVE_NOTES);
                }
            });
        }
    });
}

$(document).ready(function () {
    select2();
});
