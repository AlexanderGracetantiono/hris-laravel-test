$(document).ready(function () {
    $("#generate").on("click", function () {
        var category = $("#category option:selected").text();
        var product = $("#product option:selected").text();
        var model = $("#model option:selected").text();
        var version = $("#version").val();

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