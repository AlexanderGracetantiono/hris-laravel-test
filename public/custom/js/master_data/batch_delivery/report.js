$(document).ready(function () {
    var target_qty = $("#target_qty").val();
    var paired_qr = $("#paired_qr").val();
    var unpaired_qr = target_qty - paired_qr;
    $("#unpaired").val(unpaired_qr);

    var start_discrepancy_product = $("#SUBPA_DISCREPANCY_PRODUCT").val();
    var start_discrepancy_qr = $("#SUBPA_DISCREPANCY_TRQRZ").val();
    var start_discrepancy_bridge = $("#SUBPA_DISCREPANCY_MASCO").val();

    $("#SUBPA_RETURNED_PRODUCT").val(unpaired_qr - start_discrepancy_product);
    $("#SUBPA_RETURNED_TRQRZ").val(unpaired_qr - start_discrepancy_qr);
    $("#SUBPA_RETURNED_MASCO").val(unpaired_qr - start_discrepancy_bridge);

    $("#SUBPA_DISCREPANCY_PRODUCT").on("change", function () {
        var discrepancy_product = parseInt($(this).val());
        if (discrepancy_product >= unpaired_qr) {
            discrepancy_product = unpaired_qr;
            $(this).val(unpaired_qr);
        }
        var total_product = 0;
        if (discrepancy_product >= 0) {
            total_product = unpaired_qr - discrepancy_product;
            $("#SUBPA_RETURNED_PRODUCT").val(total_product);
        } else {
            $("#SUBPA_RETURNED_PRODUCT").val(unpaired_qr);
        }
    });

    $("#SUBPA_DISCREPANCY_TRQRZ").on("change", function () {
        var discrepancy_product = parseInt($(this).val());
        if (discrepancy_product >= unpaired_qr) {
            discrepancy_product = unpaired_qr;
            $(this).val(unpaired_qr);
        }
        var total_product = 0;
        if (discrepancy_product >= 0) {
            total_product = unpaired_qr - discrepancy_product;
            $("#SUBPA_RETURNED_TRQRZ").val(total_product);
        } else {
            $("#SUBPA_RETURNED_TRQRZ").val(unpaired_qr);
        }
    });

    $("#SUBPA_DISCREPANCY_MASCO").on("change", function () {
        var discrepancy_product = parseInt($(this).val());
        if (discrepancy_product >= unpaired_qr) {
            discrepancy_product = unpaired_qr;
            $(this).val(unpaired_qr);
        }
        var total_product = 0;
        if (discrepancy_product >= 0) {
            total_product = unpaired_qr - discrepancy_product;
            $("#SUBPA_RETURNED_MASCO").val(total_product);
        } else {
            $("#SUBPA_RETURNED_MASCO").val(unpaired_qr);
        }
    });
});