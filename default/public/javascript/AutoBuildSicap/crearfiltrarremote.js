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

      function setCurrency (currency) {
        if (!currency.id) { return currency.text; }
        var $currency = $('<span class="glyphicon glyphicon-' + currency.element.value + '">' + currency.text + '</span>');
        return $currency;
      };

      var url = window.location.href.split('/');
      url[url.length-1] = 'rest_idfields';
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
      
      var elems =  $('.remoteinfo')
      for (var i = 0; i < elems.length; i++) {
        var el = elems[i];
        var url2 = window.location.href.split('/');
        url2[url2.length-1] = 'rest_foreingKeyInfo/'+el.name+'/'+el.getAttribute('data-filter');
        var targetUrl2 = url2.join('/');
        targetUrl2 = targetUrl2.replace('/editar','')


        $(el).select2({
          ajax: {
            url: function url(param) { 
              var id = this[0].name
              var da = 'rest_foreingKeyInfo/'+id+'/'+document.getElementById(id).getAttribute('data-filter');
              return da;
             },
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function data(params) {


              var id = this[0].name
              var dependeInfo = '';
              console.log('elemento', document.getElementById('ParcialBusqueda'+el.id));
              var dependeDe = document.getElementById(id).getAttribute('data-depend');
              var dataFilter = document.getElementById(id).getAttribute('data-filter');
              var ParcialBusqueda = document.getElementById('ParcialBusqueda'+id).checked;
              
              if(dependeDe)
                 dependeInfo = document.getElementById(id).value;
           
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
            return $( '<b>'+ param.text + ' : '+  param.id +' </b>' );
          },
          templateSelection: function templateSelection(param) {
            return $( '<b>'+ param.text + ' : [Id] '+  param.id +' </b>' );

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

