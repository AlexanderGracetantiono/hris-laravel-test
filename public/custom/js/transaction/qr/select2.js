$(document).ready(function () {
    $("#approval_status").select2({
        placeholder: "Select Approval Status"
    });
    $("#orientation_select2").select2({
        placeholder: "Select QR orientation",
        width: "100%"
    });
    $("#orientation_select2").on("change", function () {
        var orientation = $(this).val();
        console.log("AAA",orientation,$('#maximum_size_text'))
        $('#minimum_size_text')[0]. innerHTML="* Minimum size is 12mm";
        $('#maximum_size_text')[0].innerHTML="* Maximum size is 24mm";
        // if (orientation=="landscape") {
        // }else{
        //     // $('#minimum_size_text')[0].innerHTML="* Minimum size is 12mm";
        //     $('#maximum_size_text')[0].innerHTML="* Maximum size is 24mm";
        // }
    });
});