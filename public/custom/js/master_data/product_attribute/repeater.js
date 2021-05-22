var KTFormRepeater = function() {
    $('#repeater').repeater({
        initEmpty: true,
        show: function () {
            $(this).slideDown();
        },
        hide: function (deleteElement) {
            $(this).slideUp(deleteElement);
        }
    });
}();

jQuery(document).ready(function() {
    KTFormRepeater.init();
});