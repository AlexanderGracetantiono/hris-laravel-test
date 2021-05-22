function select2() {
    $("#company_select2").select2({
        placeholder: "Select company"
    });
    $("#brand_select2").select2({
        placeholder: "Select brand"
    });
    $("#product_category_select2").select2({
        placeholder: "Select state"
    });
    $("#product_select2").select2({
        placeholder: "Select state"
    });
    $("#product_model_select2").select2({
        placeholder: "Select state"
    });
    $("#product_version_select2").select2({
        placeholder: "Select state"
    });

    $("#company_select2").on("change", function () {
        var company = $(this).val();
        $("#company").val(company);
        $("#brand_select2").empty().trigger("change");

        if (company) {
            $.ajax({
                type: "GET",
                url: "dashboard_admin/brand",
                data: {company:company},
                dataType: "JSON",
                success: function (response) {
                    var new_option = [];
                    for (let i = 0; i < response.length; i++) {
                        new_option[i] = new Option(response[i].text, response[i].id, false, false);
                    }
                    $('#brand_select2').append(new_option).trigger("change");
                }
            });
        }
    });

    $("#brand_select2").on("change", function () {
        var brand = $(this).val();
        $("#brand").val(brand);
        $("#product_category_select2").empty().trigger("change");

        if (brand) {
            $.ajax({
                type: "GET",
                url: "dashboard_admin/category",
                data: {brand:brand},
                dataType: "JSON",
                success: function (response) {
                    var new_option = [];
                    for (let i = 0; i < response.length; i++) {
                        new_option[i] = new Option(response[i].text, response[i].id, false, false);
                    }
                    $('#product_category_select2').append(new_option).trigger("change");
                }
            });
        }
    });

    $("#product_category_select2").on("change", function () {
        var brand = $("#brand_select2").val();
        var category = $(this).val();
        $("#product_select2").empty().trigger("change");

        if (category && brand) {
            $.ajax({
                type: "GET",
                url: "dashboard_admin/product",
                data: {brand:brand,category:category},
                dataType: "JSON",
                success: function (response) {
                    var new_option = [];
                    for (let i = 0; i < response.length; i++) {
                        new_option[i] = new Option(response[i].text, response[i].id, false, false);
                    }
                    $('#product_select2').append(new_option).trigger("change");
                }
            });
        }
    });

    $("#product_select2").on("change", function () {
        var brand = $("#brand_select2").val();
        var category = $("#product_category_select2").val();
        var product = $(this).val();
        $("#product_model_select2").empty().trigger("change");
        if (brand && category && product) {
            $.ajax({
                type: "GET",
                url: "dashboard_admin/model",
                data: {brand:brand,category:category,product:product},
                dataType: "JSON",
                success: function (response) {
                    var new_option = [];
                    for (let i = 0; i < response.length; i++) {
                        new_option[i] = new Option(response[i].text, response[i].id, false, false);
                    }
                    $('#product_model_select2').append(new_option).trigger("change");
                }
            });
        }
    });

    $("#product_model_select2").on("change", function () {
        var brand = $("#brand_select2").val();
        var category = $("#product_category_select2").val();
        var product = $("#product_select2").val();
        var model = $(this).val();
        $("#product_version_select2").empty().trigger("change");

        if (brand && category && product && model) {
            $.ajax({
                type: "GET",
                url: "dashboard_admin/version",
                data: {brand:brand,category:category,product:product,model:model},
                dataType: "JSON",
                success: function (response) {
                    var new_option = [];
                    for (let i = 0; i < response.length; i++) {
                        new_option[i] = new Option(response[i].text, response[i].id, false, false);
                    }
                    $('#product_version_select2').append(new_option).trigger("change");
                }
            });
        }
    });
}

$(document).ready(function () {
    select2();
});
