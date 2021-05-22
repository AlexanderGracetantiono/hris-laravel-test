function sweet_alert_submit (settings) { 
    Swal.fire({
        title: settings.modal_title,
        text: settings.modal_text,
        icon: settings.modal_icon_type,
        showCancelButton: true,
        confirmButtonColor: '#D94148',
        confirmButtonText: settings.modal_confirmation_text,
        cancelButtonText: settings.modal_cancel_text,
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        preConfirm: function () {
            return new Promise(function (resolve, reject) {
                $.ajax({
                    url: settings.action,
                    data: settings.data,
                    type: settings.method,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        resolve(data);
                    },
                    error: function (data) {
                        if (settings.modal_res_error_text != "") {
                            err_message = settings.modal_res_error_text;
                        }
                        else{
                            var data = data.responseJSON;
                            if (Array.isArray(data.message)) {
                                var err_message = "<ol>";
                                for (let i = 0; i < data.message.length; i++) {
                                    err_message += "<li>"+data.message[i]+"</li>";
                                }
                                err_message += "</ol>";
                            }
                            else{
                                err_message = data.message;
                            }
                        }
                        Swal.fire({
                            title: 'Error!',
                            width: "35%",
                            html: err_message,
                            icon: 'error',
                            confirmButtonColor: '#D94148',
                        })
                    }
                });
            })
        },
    }).then(function(result) {
        if (result.value) {
            Swal.fire({
                title: 'Success!',
                text: settings.modal_res_success_text,
                icon: 'success',
                confirmButtonColor: '#D94148',
            }).then(function(result) {
                if (result.value) {
                    window.setTimeout(function() {
                        window.location.href = settings.redirect_url;
                    }, 1000);
                }
            });
        }
    });
}