$(document).ready(function () {
    $('#MBRAN_MCOMP_CODE').select2({
        placeholder: "Choose Company"
    });
    $('#MBRAN_TYPE').select2({
        placeholder: "Choose Brand Type"
    });
    $('#MBRAN_TRPAT_TYPE').select2({
        placeholder: "Level Attribute Product"
    });

    $('#MBRAN_TYPE').on("change", function () {
        $('#MBRAN_TRPAT_TYPE').empty().trigger('change');
        var brand_type = $(this).val();

        if (brand_type == 1) {
            var data = [
                {
                    id: 1,
                    text: 'Category'
                },
                {
                    id: 2,
                    text: 'Product'
                },
                {
                    id: 3,
                    text: 'Model'
                },
                {
                    id: 4,
                    text: 'Version'
                },
            ];
        } else if (brand_type == 2) {
            var data = [
                {
                    id: 1,
                    text: 'Test Lab Type'
                },
            ];
        }

        var new_option = [];
        for (let i = 0; i < data.length; i++) {
            new_option[i] = new Option(data[i].text, data[i].id, false, false);
        }
        
        $('#MBRAN_TRPAT_TYPE').append(new_option).trigger('change');
    });
});