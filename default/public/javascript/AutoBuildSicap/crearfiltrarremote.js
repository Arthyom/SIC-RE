"use strict";


var field;

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

// Select2 Demo
// =============================================================
var Select2Demo = /*#__PURE__*/function () {
  function Select2Demo() {
    _classCallCheck(this, Select2Demo);

    this.init();
  }

  _createClass(Select2Demo, [{
    key: "init",
    value: function init() {
      // event handlers
      this.fillSelectFromStates();
      this.remoteData();
    }
  }, {
    key: "getStates",
    value: function getStates() {
      return $('#select2-source-states').html();
    }
  }, {
    key: "fillSelectFromStates",
    value: function fillSelectFromStates() {
      $('#select2-single, #select2-multiple').append(this.getStates());
    }
  }, {
    key: "remoteData",
    value: function remoteData() {

      function setCurrency(currency) {
        if (!currency.id) { return currency.text; }
        var $currency = $('<span class="glyphicon glyphicon-' + currency.element.value + '">' + currency.text + '</span>');
        return $currency;
      };

      var url = window.location.href.replace('/editar/', '').split('/');
      url[url.length - 1] = 'rest_idfields';
      var targetUrl = url.join('/');






      $('#remotefield').select2({
        ajax: {
          url: targetUrl,
          dataType: 'json',
          delay: 250,
          data: function data(params) {
            return {
              q: params.term,
              // search term
              page: params.page
            };
          },
          processResults: function processResults(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used

            params.page = params.page || 1;
            return {
              results: data.items,

            };
          },
          cache: true
        },
        escapeMarkup: function escapeMarkup(markup) {
          return markup;
        },
        minimumInputLength: 1,
        language: 'es',
        multiple: false,
      });


      // empieza definicion de control especial de filtrado 'select'

      var elems = $('.remoteinfo')
      for (var i = 0; i < elems.length; i++) {
        var el = elems[i];
        var url2 = window.location.href.split('/');
        var name = el.name
        if(el.getAttribute('data-key-replace') )
        name = el.getAttribute('data-key-replace')

        url2[url2.length - 1] = 'rest_foreingKeyInfo/' + name + '/' + el.getAttribute('data-filter');
        var targetUrl2 = url2.join('/');
        targetUrl2 = targetUrl2.replace('/editar', '')
        targetUrl2 = targetUrl2.replace('/crear', '')
        targetUrl2 = targetUrl2.replace('/filtrar', '')




        $(el).select2({
          ajax: {
            error: function error(param) {
              console.log('atrapando un error', param);
              if(param.statusText != 'abort')
              $('#modalError').modal('show');
             },
            url: function url(param) {
              var id = this[0].name
              if(this[0].dataset.keyReplace)
              id = this[0].dataset.keyReplace

              var k = window.location.href.split('/');
              if( window.location.href.search('editar') > 0)
                k = k.slice(0, k.length - 2).join('/');
              else
                k = k.slice(0, k.length -1 ).join('/');

              var da =  k +'/rest_foreingKeyInfo/' + id + '/' + this[0].dataset.filter;
              return da;
            },
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function data(params) {


              var id = this[0].name
              var dependeInfo = '';
              var dependeDe = this[0].dataset.depend;
              var dataFilter = this[0].dataset.filter;
              var ParcialBusqueda = document.getElementById('ParcialBusqueda' + id).checked;
              console.log('elemento', ParcialBusqueda);

              if (dependeDe)
                var dependiente = document.getElementById(dependeDe)
                if( dependiente )
                  dependeInfo = dependiente.value;

              return JSON.stringify({


                q: params.term,
                dependeDe,
                dependeInfo,
                ParcialBusqueda,
                dataFilter,
                texto: params.term,
                // search term
                page: params.page
              });
            },
            processResults: function processResults(data, params) {
              // parse the results into the format expected by Select2
              // since we are using custom formatting functions we do not need to
              // alter the remote JSON data, except to indicate that infinite
              // scrolling can be used
              console.log('elementoss info', data, params);
              params.page = params.page || 1;
              return {
                results: data.items,
                pagination: {
                  more: params.page * 30 < data.total_count
                }
              };
            },
            cache: true
          },
          templateResult: function templateResult(param) {
            return $('<b>' + param.text + ' : ' + param.id + ' </b>');
          },
          templateSelection: function templateSelection(param) {
            var id = el.name;
            if( el.dataset.keyReplace )
              id = el.dataset.keyReplace;

            var k = $('<b>' + param.text + ' : ['+ id +'] ' + param.id + ' </b>');
            return k;

          },
          minimumInputLength: 1,
          language: 'es',
          placeholder: el.getAttribute('data-filter'),
          multiple: false,
        });
      }








    }
  }]);


  return Select2Demo;
}();


$(document).on('theme:init', function () {
  new Select2Demo();
});
