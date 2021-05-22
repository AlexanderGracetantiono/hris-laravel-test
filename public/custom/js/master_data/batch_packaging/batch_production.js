$(document).ready(function () {
    $("#batch_production").on("change", function () {
        var batch_code = $(this).val();
        $.ajax({
            type: "GET",
            url: "get_batch_production",
            data: {code:batch_code},
            dataType: "JSON",
            success: function (response) {
                $("#brand_product").val(response.MABPR_MBRAN_TEXT);
                $("#category_product").val(response.MABPR_MPRCA_TEXT);
                $("#product").val(response.MABPR_MPRDT_TEXT);
                $("#model_product").val(response.MABPR_MPRMO_TEXT);
                $("#version_product").val(response.MABPR_MPRVE_TEXT);
                $("#paired_quantity_product").val(response.MABPR_PAIRED_QTY);
                $("#plant_product").val(response.MABPR_MAPLA_TEXT);
            }
        });
    });
});