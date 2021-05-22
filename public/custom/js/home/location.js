$(document).ready(function () {
    var map = new GMaps({
        div: '#kt_gmap_1',
        lat: -6.1602999,
        lng: 106.9040397,
        zoom: 11
    });

    var category = $("#product_category_select2").val();
    var product = $("#product_select2").val();
    var model = $("#product_model_select2").val();
    var version = $("#product_version_select2").val();

    // if (category && product && model && version) {
        $.ajax({
            type: "GET",
            url: "dashboard/location",
            data: {category:category,product:product,model:model,version:version},
            dataType: "JSON",
            success: function (response) {
                if (response != null) {
                    for (let i = 0; i < response.length; i++) {
                        map.addMarker({
                            lat: response[i].lat,
                            lng: response[i].lng,
                            infoWindow: {
                              content: '<p> Customer : '+response[i].user+'</p><p> Scan Time : '+response[i].scan_time+'</p><p> Category : '+response[i].category+'</p><p> Product : '+response[i].product+'</p><p> Model : '+response[i].model+'</p><p> Version : '+response[i].version+'</p><p> SKU : '+response[i].sku+'</p>'
                            }
                        });
                    }
                }
            }
        });
    // }
});