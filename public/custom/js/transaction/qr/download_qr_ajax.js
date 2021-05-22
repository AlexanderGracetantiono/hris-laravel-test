$(document).ready(function () {
    $(".download_qr_btn").click(function (e) {
        var curr_this = $(this);

        var code = curr_this.data("code");
        var action = curr_this.data("action");
        var qr_type = curr_this.data("qr-type");

        $.ajax({
            url: action,
            data: {code:code,qr_type:qr_type},
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                window.open(data.file, '_blank');
            },
            error: function (data) {
                Swal.fire({
                    title: 'Error!',
                    width: "25%",
                    html: data.responseJSON.message,
                    icon: 'error',
                    confirmButtonColor: '#D94148',
                })
            }
        });

        e.preventDefault();
    });
});