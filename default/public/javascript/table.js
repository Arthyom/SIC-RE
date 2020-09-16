"use strict";

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

// Advance DataTables Demo
// =============================================================


//console.log(miJSON);
//console.log(JSON.parse(miJSON));

var miDataTables = /*#__PURE__*/function () {
  function miDataTables() {

    _classCallCheck(this, miDataTables);

    this.init();

  }

  _createClass(miDataTables, [{
    key: "init",
    value: function init() {
      // event handlers
      this.table = this.table();
      this.table.buttons().container().appendTo('#dt-buttons').unwrap();


    }
  }, {
    key: "table",
    value: function table() {

      return $('#responsivetable').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>\n        <'table-responsive'tr>\n        <'row align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>",
        buttons: [
          {
            extend:'copyHtml5',
            text:'Copiar',
            title: ""// document.getElementById('miTitle').value
          },{
            extend: 'print',
            //autoPrint: false,
            //messageBottom: null,
            text: 'Imprimir',
            title: ""//document.getElementById('miTitle').value,
            //messageTop: 'The information in this table is copyright to Sirius Cybernetics Corp.'

        }],
        lengthMenu: [1, 5, 10, 25, 50, 75, 100],
        pageLength: 10,
        language: {
          //url:'https://cdn.datatables.net/plug-ins/a5734b29083/i18n/Spanish.json',
          "sProcessing":     "Procesando...",
          "sLengthMenu":     "Mostrar _MENU_ registros",
          "sZeroRecords":    "No se encontraron resultados",
          "sEmptyTable":     "Ningún dato disponible en esta tabla",
          "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
          "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
          "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
          "sInfoPostFix":    "",
          "sSearch":         "Buscar:",
          "sUrl":            "",
          "sInfoThousands":  ",",
          "sLoadingRecords": "Cargando...",
          paginate: {
            previous: '<i class="fa fa-lg fa-angle-left"></i>',
            next: '<i class="fa fa-lg fa-angle-right"></i>'
          },
          "oAria": {
            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
          },
          buttons: {
            copy: 'Copy',
            copyTitle: 'Data copied',
            copyKeys: 'Use your keyboard or menu to select the copy command',
            copyText: 'Use your keyboard or menu to select the copy command',
            copySuccess: {
              1: "Copié una fila al portapapeles",
              _: "Copié %d filas al portapapeles"
            }
          }
        }

      });
    }
  }]);

  return miDataTables;
}();



$(document).on('theme:init', function () {
  new miDataTables();
});
