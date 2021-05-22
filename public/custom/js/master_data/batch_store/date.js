$(document).ready(function () {
    var arrows;
    if (KTUtil.isRTL()) {
        arrows = {
            leftArrow: '<i class="la la-angle-right"></i>',
            rightArrow: '<i class="la la-angle-left"></i>'
        }
    } else {
        arrows = {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        }
    }

    $('#MABPA_DATE').datepicker({
        rtl: KTUtil.isRTL(),
        todayHighlight: true,
        orientation: "top left",
        templates: arrows
    });
});