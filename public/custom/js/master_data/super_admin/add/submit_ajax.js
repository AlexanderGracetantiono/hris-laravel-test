$(document).ready(function () {
    $('#form').on('submit', function (e) {
        var redirect_url = $(this).data("form-success-redirect");
        var curr_this = $(this);
        Swal.fire({
            title: "Apakah Anda Yakin?",
            text: "Periksa kembali form yang telah Anda input sebelum melanjutkan",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#D94148',
            confirmButtonText: "Ya, Simpan Data",
            cancelButtonText: "Batal",
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: curr_this.attr('action'),
                    data: curr_this.serialize(),
                    type: 'POST',
                    success: function (data) {
                        Swal.fire({
                            title: 'Success!',
                            text: "Data berhasil disimpan",
                            icon: 'success',
                            confirmButtonColor: '#D94148',
                        }).then(function(result) {
                            if (result.value) {
                                window.setTimeout(function() {
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
                                err_message += "<li>"+data.message[i]+"</li>";
                            }
                            err_message += "</ol>";
                        }
                        else{
                            err_message = data.message;
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
            }
        });
        e.preventDefault();
    });
});