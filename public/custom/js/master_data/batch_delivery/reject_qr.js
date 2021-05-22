$(document).ready(function () {
    $(".reject_qr_btn").click(function (e) {
        var curr_this = $(this);
        var settings = {
            code: $(this).data("code"), 
            action: $(this).data("action"),
            method: "POST",
            modal_title: "Are you sure ?",
            modal_text: "QR will be rejected",
            modal_icon_type: "warning",
            modal_confirmation_text: "Yes",
            modal_cancel_text: "Cancel",
            modal_res_success_text: "QR successfully rejected",
            modal_res_error_text: ""
        };
        sweet_alert_delete(settings);
        e.preventDefault();
    });
});