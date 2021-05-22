$(document).ready(function () {
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