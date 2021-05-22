var KTFormRepeater = function() {
    $('#repeater').repeater({
        initEmpty: true,
        show: function () {
            $(this).slideDown();
            $(".select2-container").remove();
            select2();
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