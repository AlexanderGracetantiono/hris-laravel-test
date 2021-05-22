$(document).ready(function () {
    $("#role_select2").select2({
        placeholder: "Select role",
        width: "100%"
    });
    $(".country_code").select2({
        placeholder: "Code"
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
});