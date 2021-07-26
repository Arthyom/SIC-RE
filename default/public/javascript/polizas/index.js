$(document).ready(function() {
    $('table.dtTablePrin').DataTable({
        "ordering": false,
        "paging": false,
        "info": false,
        buttons: [
            {
                text: 'Nuevo elemento',
                action: function ( e, dt, node, config ) {
                    window.location.href = cu+'/crear';
                }
            }
        ]

    });
} );

$(document).ready(function() {

    $('table.dtTableSec').DataTable({


        lengthMenu: [5, 10, 30, 50, 75, 100],
        pageLength: 10,
        dom: "<'row'  <'col-sm-12 col-md-6' l>  <'col-sm-12 col-md-6' f>  >\n        <'table-responsive'tr>\n        <'row align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>",
        language: {
            url:'https://cdn.datatables.net/plug-ins/a5734b29083/i18n/Spanish.json'
        },
        buttons: [
            {
                text: 'Nuevo elemento',
                action: function ( e, dt, node, config ) {
                    var cu =  window.location.pathname
                    window.location.href = cu+'/crear';
                }
            }
        ]
    });
} );

$(document).ready(function() {

    $('table.dtTableInh').DataTable({


        lengthMenu: [5, 10, 30, 50, 75, 100],
        pageLength: 10,
        dom: "<'row'  <'col-sm-12 col-md-6' l>  <'col-sm-12 col-md-6' f>  >\n        <'table-responsive'tr>\n        <'row align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>",
        language: {
            url:'https://cdn.datatables.net/plug-ins/a5734b29083/i18n/Spanish.json'
        },
        buttons: [
            {
                text: 'Nuevo elemento',
                action: function ( e, dt, node, config ) {
                    var cu =  window.location.pathname
                    window.location.href = cu+'/crear';
                }
            }
        ]
    });
} );



$(document).ready(function() {

    $('table.dtTableHug').DataTable({


        lengthMenu: [5, 10, 30, 50, 75, 100],
        pageLength: 10,
        dom: "<'row'   <'col-sm-12 col-md-12' f>  >\n        <'table-responsive'tr>\n        <'row align-items-center'<'col-sm-12 col-md-5'><'col-sm-12 col-md-7 d-flex justify-content-end'>>",
        language: {
            url:'https://cdn.datatables.net/plug-ins/a5734b29083/i18n/Spanish.json'
        },
        buttons: [
            {
                text: 'Nuevo elemento',
                action: function ( e, dt, node, config ) {
                    var cu =  window.location.pathname
                    window.location.href = cu+'/crear';
                }
            }
        ]
    });
} )
