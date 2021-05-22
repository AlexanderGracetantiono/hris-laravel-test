function select2() {
    $("#plant").select2({
        placeholder: "Select testing Center / laboratorium type",
        width:"100%"
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
