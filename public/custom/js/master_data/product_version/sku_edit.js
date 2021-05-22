$(document).ready(function () {
    $("#generate").on("click", function () {
        var category = $("#category_edit").val();
        var product = $("#product_edit").val();
        var model = $("#model_edit").val();
        var version = $("#version_edit").val();

        if (category && product && model && version) {
            $.ajax({
                type: "GET",
                url: "generate_sku",
                data: {category:category,product:product,version:version,model:model},
                dataType: "JSON",
                success: function (response) {
                    $("#sku").val(response);
                }
            });
        }
    });
});