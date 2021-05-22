$(document).ready(function () {
    var minute = 2;
    var timeleft_second = 60;
    var timeleft =3*60;
    document.getElementById("btn_countdown").disabled  =true;
    var downloadTimer = setInterval(function () {
        if (timeleft <= 0) {
            clearInterval(downloadTimer);
            document.getElementById("btn_countdown").disabled  =false;
            document.getElementById("btn_countdown").innerHTML = "Resend Verification Code";
        } else {
            if(minute<1){
                document.getElementById("btn_countdown").innerHTML ="Resend in "+timeleft + " seconds";
            }else{
                document.getElementById("btn_countdown").innerHTML ="Resend in "+minute + " minute, "+timeleft_second + " seconds";
            }
        }
        timeleft -= 1;
        if(timeleft_second<=0){
            timeleft_second=60;
            minute-=1;
        }else{
            timeleft_second -= 1;
        }
        
    }, 1000);
    // FORGOT PASSWORD FOR SEND EMAIL
    $('#kt_sent_otp_form').on('submit', function (e) {
        var curr_this = $(this);
        var redirect_url = $(this).data("form-success-redirect");
        $.ajax({
            url: curr_this.attr('action'),
            data: curr_this.serialize(),
            type: 'POST',
            beforeSend: function () {
                KTApp.block('#kt_login', {
                    overlayColor: '#000000',
                    type: 'v2',
                    state: 'primary',
                    message: 'Loading...'
                });
            },
            success: function (data) {
                Swal.fire({
                    title: 'Success!',
                    text: "Verification success",
                    icon: 'success',
                    confirmButtonColor: '#D94148',
                }).then(function (result) {
                    if (result.value) {
                        window.setTimeout(function () {
                            window.location.href = redirect_url;
                        }, 1000);
                    }
                });
            },
            error: function (data) {
                var data = data.responseJSON;
                if (Array.isArray(data.message)) {
                    var err_message = "<ol>";
                    for (let i = 0; i < data.message.length; i++) {
                        err_message += "<li>" + data.message[i] + "</li>";
                    }
                    err_message += "</ol>";
                }
                else {
                    err_message = data.message;
                }
                Swal.fire({
                    title: 'Error!',
                    width: "35%",
                    html: err_message,
                    icon: 'error',
                    confirmButtonColor: '#D94148',
                }).then(function (result) {
                    if (result.value) {
                        window.setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                });
            },
            complete: function (data) {
                KTApp.unblock('#kt_login');
            }
        });
        e.preventDefault();
    });
    $('#kt_resent_otp_form').on('submit', function (e) {
        var curr_this = $(this);
        var redirect_url = $(this).data("form-success-redirect");
        // document.getElementById("btn_countdown").disabled  =true;
        // var downloadTimer = setInterval(function () {
        //     if (timeleft <= 0) {
        //         clearInterval(downloadTimer);
        //         document.getElementById("btn_countdown").disabled  =false;
        //         document.getElementById("btn_countdown").innerHTML = "Resend Verification Code";
        //     } else {
        //         document.getElementById("btn_countdown").innerHTML ="Resend in "+timeleft + " seconds";
        //     }
        //     timeleft -= 1;
        // }, 1000);
        $.ajax({
            url: curr_this.attr('action'),
            data: curr_this.serialize(),
            type: 'POST',
            beforeSend: function () {
                KTApp.block('#kt_login', {
                    overlayColor: '#000000',
                    type: 'v2',
                    state: 'primary',
                    message: 'Loading...'
                });
            },
            success: function (data) {
                Swal.fire({
                    title: 'Success!',
                    text: "Please check your e-mail for verification code.",
                    icon: 'success',
                    confirmButtonColor: '#D94148',
                }).then(function (result) {
                    if (result.value) {
                        window.setTimeout(function () {
                            window.location.href = redirect_url;
                        }, 1000);
                    }
                });
            },
            error: function (data) {
                var data = data.responseJSON;
                if (Array.isArray(data.message)) {
                    var err_message = "<ol>";
                    for (let i = 0; i < data.message.length; i++) {
                        err_message += "<li>" + data.message[i] + "</li>";
                    }
                    err_message += "</ol>";
                }
                else {
                    err_message = data.message;
                }
                Swal.fire({
                    title: 'Error!',
                    width: "35%",
                    html: err_message,
                    icon: 'error',
                    confirmButtonColor: '#D94148',
                }).then(function (result) {
                    if (result.value) {
                        window.setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                });
            },
            complete: function (data) {
                KTApp.unblock('#kt_login');
            }
        });
        e.preventDefault();
    });


});