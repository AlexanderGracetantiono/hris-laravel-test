$(document).ready(function () {
    var target_qty = $("#target_qty").val();
    var paired_qr = $("#paired_qr").val();
    var unpaired_qr = target_qty - paired_qr;
    $("#unpaired").val(unpaired_qr);

    var start_discrepancy_product = $("#MABPR_DISCREPANCY_PRODUCT").val();
    var start_discrepancy_qr = $("#MABPR_DISCREPANCY_TRQRA").val();
    var start_discrepancy_bridge = $("#MABPR_DISCREPANCY_MASCO").val();

    $("#MABPR_RETURNED_PRODUCT").val(unpaired_qr - start_discrepancy_product);
    $("#MABPR_RETURNED_TRQRA").val(unpaired_qr - start_discrepancy_qr);
    $("#MABPR_RETURNED_MASCO").val(unpaired_qr - start_discrepancy_bridge);

    $("#MABPR_DISCREPANCY_PRODUCT").on("change", function () {
        var discrepancy_product = parseInt($(this).val());
        if (discrepancy_product >= unpaired_qr) {
            discrepancy_product = unpaired_qr;
            $(this).val(unpaired_qr);
        }
        var total_product = 0;
        if (discrepancy_product >= 0) {
            total_product = unpaired_qr - discrepancy_product;
            $("#MABPR_RETURNED_PRODUCT").val(total_product);
        } else {
            $("#MABPR_RETURNED_PRODUCT").val(unpaired_qr);
        }
    });

    $("#MABPR_DISCREPANCY_TRQRA").on("change", function () {
        var discrepancy_product = parseInt($(this).val());
        if (discrepancy_product >= unpaired_qr) {
            discrepancy_product = unpaired_qr;
            $(this).val(unpaired_qr);
        }
        var total_product = 0;
        if (discrepancy_product >= 0) {
            total_product = unpaired_qr - discrepancy_product;
            $("#MABPR_RETURNED_TRQRA").val(total_product);
        } else {
            $("#MABPR_RETURNED_TRQRA").val(unpaired_qr);
        }
    });

    $("#MABPR_DISCREPANCY_MASCO").on("change", function () {
        var discrepancy_product = parseInt($(this).val());
        if (discrepancy_product >= unpaired_qr) {
            discrepancy_product = unpaired_qr;
            $(this).val(unpaired_qr);
        }
        var total_product = 0;
        if (discrepancy_product >= 0) {
            total_product = unpaired_qr - discrepancy_product;
            $("#MABPR_RETURNED_MASCO").val(total_product);
        } else {
            $("#MABPR_RETURNED_MASCO").val(unpaired_qr);
        }
    });
});