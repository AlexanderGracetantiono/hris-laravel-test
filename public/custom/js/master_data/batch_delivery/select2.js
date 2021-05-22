function select2() {
    $(".employee").select2({
        placeholder: "Select user"
    });
    $("#plant").select2({
        placeholder: "Select plant"
    });
    $("#pool_product").select2({
        placeholder: "Select pool product"
    });
    $("#pool_product").on("change", function () {
        var batch_production = $(this).val();
        $.ajax({
            type: "GET",
            url: "get_pool_product",
            data: {code:batch_production},
            dataType: "JSON",
            success: function (response) {
                $("#packaging_qty").val(response.POPRD_QTY_LEFT);
                $("#category").val(response.POPRD_MPRCA_TEXT);
                $("#product").val(response.POPRD_MPRDT_TEXT);
                $("#model").val(response.POPRD_MPRMO_TEXT);
                $("#version").val(response.POPRD_MPRVE_TEXT);
                $("#sku").val(response.POPRD_MPRVE_SKU);
                $("#notes").val(response.POPRD_MPRVE_NOTES);
            }
        });
    });
}

$(document).ready(function () {
    select2();
});
