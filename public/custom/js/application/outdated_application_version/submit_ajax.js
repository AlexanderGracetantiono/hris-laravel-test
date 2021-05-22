$(document).ready(function () {
    $("#submit_btn").click(function (e) { 
        var formData = new FormData($("#form")[0]);
        var submit = {
            action: $("#form").attr("action"),
            redirect_url: $("#form").data("form-success-redirect"),
            data: formData,
            method: "POST",
            modal_title: "Are you sure ?",
            modal_text: "Please double check data before submiting",
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