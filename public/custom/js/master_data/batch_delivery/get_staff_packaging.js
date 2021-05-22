function get_staff_packaging_function(date,start_time,end_time) {
    var temp_start = start_time.replace(':', '');
    var temp_end = end_time.replace(':', '');
    if (parseInt(temp_start) > parseInt(temp_end)) {
        $("#err_message").show();
        console.log("End time must be greater than start time");
    } 
    else {
        $("#err_message").hide();
        $(".employee").each(function(){
            var current = $(this);
            var current_val = current.val();
            if (current_val == null || current_val == "") {
                if (date && start_time && end_time) {
                    $.ajax({
                        type: "GET",
                        url: "get_staff_packaging",
                        data: {date:date,start_time:start_time,end_time:end_time},
                        dataType: "JSON",
                        success: function (response) {
                            if (response != null) {
                                current.empty().trigger("change");
                                var newOption = [];
                                for (let i = 0; i < response.length; i++) {
                                    newOption[i] = new Option(response[i].text, response[i].id, true, true);
                                }
                                current.append(newOption).trigger("change");
                            }
                        }
                    });
                }
            }
        });
    }
}

$(document).ready(function () {
    $("#err_message").hide();
    $("#SUBPA_DATE_START").on("change", function () {
        var date = $(this).val();
        var start_time = $("#SUBPA_TIME_START").val();
        var end_time = $("#SUBPA_TIME_END").val();
        $(".employee").empty().trigger("change");

        if (date && start_time && end_time) {
            get_staff_packaging_function(date,start_time,end_time);
        }
    });

    $("#SUBPA_TIME_START").on("change", function () {
        var date = $("#SUBPA_DATE_START").val();
        var start_time = $(this).val();
        var end_time = $("#SUBPA_TIME_END").val();
        $(".employee").empty().trigger("change");

        if (date && start_time && end_time) {
            get_staff_packaging_function(date,start_time,end_time);
        }
    });
    
    $("#SUBPA_TIME_END").on("change", function () {
        var date = $("#SUBPA_DATE_START").val();
        var start_time = $("#SUBPA_TIME_START").val();
        var end_time = $(this).val();
        $(".employee").empty().trigger("change");
        
        if (date && start_time && end_time) {
            get_staff_packaging_function(date,start_time,end_time);
        }
    });
});