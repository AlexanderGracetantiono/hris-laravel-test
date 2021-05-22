"use strict";
jQuery(document).ready(function() {
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
            'excelHtml5',
            'csvHtml5',
            // 'pdfHtml5',
        ],
        // columnDefs: [
        //     {
        //         "orderable": false,
        //         "searchable": false,
        //         "targets": -1
        //     }
        // ]
    });
    // table.buttons().remove();
});
