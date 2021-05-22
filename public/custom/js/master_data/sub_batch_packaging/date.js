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

    $('#SUBPA_DATE_START,#SUBPA_DATE_END').datepicker({
        rtl: KTUtil.isRTL(),
        todayHighlight: true,
        orientation: "top left",
        templates: arrows
    });

    $('#SUBPA_TIME_START, #SUBPA_TIME_END').timepicker({
        defaultTime: null,
        minuteStep: 1,
        disableFocus: true,
        template: 'dropdown',
        showMeridian:false
    });
});