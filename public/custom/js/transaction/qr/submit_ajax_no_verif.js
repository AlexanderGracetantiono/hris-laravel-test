$(document).ready(function () {
    $("#submit_btn").click(function (e) {
        var formData = new FormData($("#form")[0]);
        var QR_SIZE = $("#QR_SIZE")[0].value;
        var QR_CODE = $("#QR_CODE")[0].value;
        var Orientation_select2 = $("#orientation_select2")[0].value;
        var submit = {
            action: $("#form").attr("action"),
            redirect_url: $("#form").data("form-success-redirect"),
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
        Swal.fire({
            title: submit.modal_title,
            text: submit.modal_text,
            icon: submit.modal_icon_type,
            showCancelButton: true,
            confirmButtonColor: '#D94148',
            confirmButtonText: submit.modal_confirmation_text,
            cancelButtonText: submit.modal_cancel_text,
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
            // preConfirm: function () {
            //     return new Promise(function (resolve, reject) {
            //         $.ajax({
            //             url: submit.action,
            //             data: submit.data,
            //             type: submit.method,
            //             processData: false,
            //             contentType: false,
            //             success: function (data) {
            //                 resolve(data);
            //             },
            //             error: function (data) {
            //                 if (submit.modal_res_error_text != "") {
            //                     err_message = submit.modal_res_error_text;
            //                 }
            //                 else{
            //                     var data = data.responseJSON;
            //                     if (Array.isArray(data.message)) {
            //                         var err_message = "<ol>";
            //                         for (let i = 0; i < data.message.length; i++) {
            //                             err_message += "<li>"+data.message[i]+"</li>";
            //                         }
            //                         err_message += "</ol>";
            //                     }
            //                     else{
            //                         err_message = data.message;
            //                     }
            //                 }
            //                 Swal.fire({
            //                     title: 'Error!',
            //                     width: "35%",
            //                     html: err_message,
            //                     icon: 'error',
            //                     confirmButtonColor: '#D94148',
            //                 })
            //             }
            //         });
            //     })
            // },
        }).then(function (result) {
            if (result.value) {
                err_message=""
                if (Orientation_select2==""||Orientation_select2==null) {
                    err_message = "QR orientation is not selected"
                    Swal.fire({
                        title: 'Error!',
                        width: "35%",
                        html: err_message,
                        icon: 'error',
                        confirmButtonColor: '#D94148',
                    })
                }else if ((QR_SIZE < 12 || QR_SIZE > 24)&&Orientation_select2=="landscape") {
                    err_message = "QR size is not within the specified range"
                    Swal.fire({
                        title: 'Error!',
                        width: "35%",
                        html: err_message,
                        icon: 'error',
                        confirmButtonColor: '#D94148',
                    })
                }else if((QR_SIZE < 12 || QR_SIZE > 24)&&Orientation_select2=="portrait") {
                    err_message = "QR size is not within the specified range"
                    Swal.fire({
                        title: 'Error!',
                        width: "35%",
                        html: err_message,
                        icon: 'error',
                        confirmButtonColor: '#D94148',
                    })
                } else {
                    var url_link = submit.action + "?code=" + QR_CODE + "&QR_SIZE=" + QR_SIZE + "&orientation=" + Orientation_select2
                    window.open(url_link);

                    Swal.fire({
                        title: 'Success!',
                        text: submit.modal_res_success_text,
                        icon: 'success',
                        confirmButtonColor: '#D94148',
                    }).then(function (result) {
                        if (result.value) {
                            // window.setTimeout(function() {
                            //     window.location.href = submit.redirect_url;
                            // }, 1000);
                        }
                    });
                }

            }
        });
        e.preventDefault();
    });
});
