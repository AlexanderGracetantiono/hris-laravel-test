"use strict";
jQuery(document).ready(function () {
    var table = $('#kt_datatable1').DataTable({
        aaSorting: [],
        responsive: true,
        "scrollX": true,
        dom: `<'row'<'col-sm-6 text-left'f><'col-sm-6 text-right'B>>
        <'row'<'col-sm-12'tr>>
        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
        language: {
            "lengthMenu": "Displays _MENU_ data per page",
            "zeroRecords": "No data available",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "Data not available",
            "infoFiltered": "(Filtered from _MAX_ total data)"
        },
        buttons: [
            'print',
            'copyHtml5',
            {
                text: 'Excel',
                action: function (e, dt, node, config) {
                    // let redirect_url= document.getElementById("redirect_url");
                    // route("master_data_company_save")
                    let category_param = document.getElementById("product_category_select2").value;
                    let product_param = document.getElementById("product_select2").value;
                    let model_param = document.getElementById("product_model_select2").value;
                    let version_param = document.getElementById("product_version_select2").value;
                    // console.log(document.getElementById("product_category_select2"));
                    // alert( category_param );
                    let url_param = "category="+category_param+"&product="+product_param+"&model="+model_param+"&version="+version_param;
                    window.setTimeout(function () {
                        window.location.href = "dashboard/download?" + url_param+"&type=XLX";
                    }, 500);
                }
            },
            {
                text: 'CSV',
                action: function (e, dt, node, config) {
                    // let redirect_url= document.getElementById("redirect_url");
                    // route("master_data_company_save")
                    let category_param = document.getElementById("product_category_select2").value;
                    let product_param = document.getElementById("product_select2").value;
                    let model_param = document.getElementById("product_model_select2").value;
                    let version_param = document.getElementById("product_version_select2").value;
                    // console.log(document.getElementById("product_category_select2"));
                    // alert( category_param );
                    let url_param = "category="+category_param+"&product="+product_param+"&model="+model_param+"&version="+version_param;
                    window.setTimeout(function () {
                        window.location.href = "dashboard/download?" + url_param+"&type=CSV";
                    }, 500);
                }
            }
            // 'excelHtml5',
            // 'csvHtml5',
            // 'pdfHtml5',
        ],
        // columnDefs: [
        //     {
        //         "orderable": false,
        //         "searchable": false,
        //         "targets": 0
        //     }
        // ]
    });
    // table.buttons().remove();
});
