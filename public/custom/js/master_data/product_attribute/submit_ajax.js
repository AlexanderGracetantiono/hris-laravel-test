$(document).ready(function () {
    $("#submit_general_btn").click(function (e) {
        var formData = new FormData($("#form_general")[0]);
        var submit = {
            action: $("#form_general").attr("action"),
            redirect_url: $("#form_general").data("form-success-redirect"),
            data: formData,
            method: "POST",
            modal_title: "Are you sure ?",
            modal_text: "Please double check data before submitting",
            modal_icon_type: "warning",
            modal_confirmation_text: "Yes",
            modal_cancel_text: "Cancel",
            modal_res_success_text: "Data successfully submitted",
            modal_res_error_text: ""
        };
        sweet_alert_submit(submit);
        e.preventDefault();
    });
    
    $("#submit_custom_btn").click(function (e) {
        var formData = new FormData($("#form_custom")[0]);
        var submit = {
            action: $("#form_custom").attr("action"),
            redirect_url: $("#form_custom").data("form-success-redirect"),
            data: formData,
            method: "POST",
            modal_title: "Are you sure ?",
            modal_text: "Please double check data before submitting",
            modal_icon_type: "warning",
            modal_confirmation_text: "Yes",
            modal_cancel_text: "Cancel",
            modal_res_success_text: "Data successfully submitted",
            modal_res_error_text: ""
        };
        sweet_alert_submit(submit);
        e.preventDefault();
    });
});
