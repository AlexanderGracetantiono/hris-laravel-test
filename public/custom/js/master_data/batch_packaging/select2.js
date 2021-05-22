function select2() {
    $("#plant").select2({
        placeholder: "Select production / packaging center type",
        width:"100%"
    });
    $("#batch_production").select2({
        placeholder: "Select batch production",
        width:"100%"
    });
    $("#batch_production").on("change", function () {
        var batch_production = $(this).val();
        $.ajax({
            type: "GET",
            url: "/get_batch_packaging",
            data: {code:batch_production},
            dataType: "JSON",
            success: function (response) {
                $("#packaging_qty").val(response.MABPA_QTY);
            }
        });
    });
    $(".country_code").select2({
        placeholder: "Code",
        width:"100%"
    });
    $(".phone_number").keyup(function(){
        var value = $(this).val();
        value = value.replace(/^(0*)/,"");
        $(this).val(value);
    });

    $(".area_number").keyup(function(){
        var value = $(this).val();
        value = value.replace(/^(0*)/,"");
        $(this).val(value);
    });
}

$(document).ready(function () {
    select2();
});
