$(document).ready(function () {
    $("#sub_batch_packaging").on("change", function () {
        var batch_code = $(this).val();
        $.ajax({
            type: "GET",
            url: "sub_batch_packaging",
            data: {code:batch_code},
            dataType: "JSON",
            success: function (response) {
                $("#brand_product").val(response.batch_production.MABPR_MBRAN_TEXT);
                $("#category_product").val(response.batch_production.MABPR_MPRCA_TEXT);
                $("#product").val(response.batch_production.MABPR_MPRDT_TEXT);
                $("#model_product").val(response.batch_production.MABPR_MPRMO_TEXT);
                $("#version_product").val(response.batch_production.MABPR_MPRVE_TEXT);
                $("#paired_quantity_product").val(response.sub_batch_packaging.SUBPA_PAIRED_QTY);
                $("#plant").val(response.sub_batch_packaging.SUBPA_MAPLA_TEXT);
                $("#batch_production_notes").val(response.batch_production.MABPR_NOTES);
                $("#batch_acceptance_notes").val(response.batch_acceptance.MABPA_NOTES);
                $("#sub_batch_packaging_notes").val(response.sub_batch_packaging.SUBPA_NOTES);
            }
        });
    });
});