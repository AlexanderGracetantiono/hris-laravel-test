var KTFormRepeater = function() {
    $('#repeater').repeater({
        initEmpty: false,
        show: function () {
            $(this).slideDown();
            var date = $("#MABPR_DATE_START").val();
            var start_time = $("#MABPR_TIME_START").val();
            var end_time = $("#MABPR_TIME_END").val();
            $(".select2-container").remove();
            select2();
            get_staff_production_function(date,start_time,end_time);
            $(".select2-container").css('width','100%');
        },
        hide: function (deleteElement) {
            $(this).slideUp(deleteElement);
        }
    });
}();

jQuery(document).ready(function() {
    KTFormRepeater.init();
});