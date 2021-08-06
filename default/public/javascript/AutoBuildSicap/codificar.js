var gtipo = '';
var gdestino = '';

function modalCarga( mostrar = true, funcion ) { 
  

        if( mostrar )
            $('#modalLoad').modal('show');
        else{
            setTimeout( function(){
                $('#modalLoad').modal('toggle');
                funcion();
            }, 1500 );
        }
        
 }

function back(tipo = 'controllers', fecha ) { 
  
    gtipo = tipo;

    // trabajar la url para realizar la peticion 
    var url = window.location.href.split('/');
    var nombre = url[url.length-2];
    switch (tipo) {
        case 'controllers': nombre += '_controller.php'; break;
        case 'models': nombre += '.php'; break;
        case 'views': nombre = nombre + '/' + gdestino ; break;
    }
    
    url[url.length-1] = 'rest_back';
    var g = {fecha, nombre, tipo : gtipo};
    var targetUrl = url.join('/');

    settitle(nombre);
    console.log('......', g)
    modalCarga();

    $.ajax({
        type: 'POST',
        url: targetUrl,
        data:   JSON.stringify( {fecha, nombre, tipo : gtipo} ),
        success: function (data) {
            console.log('datos en fetch', data)
        
       
                modalCarga(false, function () { 
                    if(data.ok){
                    var editor = ace.edit('aceEditor');
                    editor.session.setMode('ace/mode/php');
                    $("#lenW").val('php');
                    editor.session.setValue(data.contenido.join(''));
                    $('#toastAbrir').toast('show');
                   // save();
                    }
                    else
                    $('#toastError').toast('show');
                 });
        }
    });
}

function show(){

    $('#sec').val(null) 


    console.log('sdsds ', gtipo.length)
    if(  gtipo.length < 5  ){
        $('#sec').prop('disabled', true);
        $("#exampleModalScrollableLabelDate").text('Seleccione un Modelo, Vista o Controlador');
    }
    else{
        $('#sec').prop('disabled', false);
        $("#exampleModalScrollableLabelDate").text('Seleccionar Fecha');
    }
}

// conseguir el historial del archivo seleccionado
function history(  ) { 



     // trabajar la url para realizar la peticion 
     var url = window.location.href.split('/');
     var nombre = url[url.length-2];
     var editor = ace.edit('aceEditor');
     switch (gtipo) {
         case 'controllers': nombre += '_controller.php'; break;
         case 'models': nombre += '.php'; break;
         case 'views': nombre = nombre + '/' + gdestino ; break;
     }
     
     url[url.length-1] = 'rest_history';
     var targetUrl = url.join('/');
     fecha = new Date( $('#sec').val() );
     
     dia = fecha.getDate()+1;
     mes  = fecha.getUTCMonth()+1;
     anio = fecha.getFullYear();
     fechaCompleta = dia + '-'+ mes +'-'+ anio;
     modalCarga();


     $.ajax({
        type: 'POST',
        url: targetUrl,
        data:   JSON.stringify( {fecha:fechaCompleta, nombre, tipo:gtipo} ),
        success: function (data) {

            console.log('nombre a enviar al historial', data);

  
            
          

                    modalCarga(false, function () { 

                        $('#exampleModalScrollableDate').modal('hide')
                        $('#listback').html('');
                        if( data.validos.length > 0 ){
            

                        for (let i = 0; i < data.validos.length; i++) {
                            const fi = data.validos[i];
                            const fii = fi.split(' ')
                            const fiii = fii[0].split('/')
                            const d = fiii[1]; const m = fiii[0]; const y = fiii[2];
                            const fechaDir = d + '/' + m +'/'+y + ' ' + fii[1];
                            console.log('fecha extraida ',fechaDir)
                            $('#listback').append( `<li data-dismiss="modal" onclick= "back( '${gtipo}', '${fechaDir}' )" class="list-group-item list-group-item-action">` +  fi + '</li>' );
                            
                        }
                       
                    }
                    else{
                        let fi = 'No hay ningún histórico asociado con este archivo, intente con otro';
                        $('#listback').append( `<li data-dismiss="modal" class="list-group-item list-group-item-action">` +  fi + '</li>' );
                    
                    }
                    $('#exampleModalScrollableBack').modal('show');

                     });

              
            
        }
    }); 
 

}

function eliminarvista( vista ) {
    

      // trabajar la url para realizar la peticion 
      var url = window.location.href.split('/');
      var nombre = url[url.length-2];    
      url[url.length-1] = 'rest_view_del';
      var targetUrl = url.join('/');

      console.log('eliminar vista ', vista);

      modalCarga();
      viewdirs();
      
 
    $.ajax({

        type: 'POST',
        url: 'rest_view_del',
        data:   JSON.stringify( {nombre, vista} ),
        success: function(data) {

            modalCarga(false, function () {
                viewdirs();
                if(data.ok){
                    $('#toastEliminar').toast('show');
                    $('#exampleModalScrollable').modal('show');
                }
                    else{
                    $('#toastError').toast('show');
                    }
                console.log('.-.-.-', data);
            
            })

    
        }


    });
}

//consigue el contenido del archivo solicitado
function viewdirs() { 
    // trabajar la url para realizar la peticion 
    var url = window.location.href.split('/');
    var nombre = url[url.length-2];    
    url[url.length-1] = 'rest_viewdir';
    var targetUrl = url.join('/');

    $.ajax({
        type: 'POST',
        url: targetUrl,
        data:   JSON.stringify( {nombre} ),
        success: function (data) {

            console.log('-----', targetUrl)
            
            console.log('-----',data)


            $('#listviewdir').html('');

            if(data.ok){

                for (let i = 2; i < data.contenido.length; i++) {
                    const fi = data.contenido[i];
                    var li = ` <a     class="list-group-item list-group-item-action">
                    <div class="list-group-item-figure" >
                      <div class="tile bg-danger" data-dismiss="modal"  onclick="fetch( 'views', '${fi}' )">
                        <span class="oi oi-data-transfer-upload"></span>
                      </div>
                    </div>
                    <div class="list-group-item-body" data-dismiss="modal"  onclick="fetch( 'views', '${fi}' )"> ${fi} </div>
                    <button  data-dismiss="modal"  onclick="eliminarvista('${fi}')" class="btn btn-sm btn-icon btn-danger">
                    <i class="fas fa-trash"></i></button>
                    </div>
                  </a> `
                   // var k =  '<li data-dismiss="modal" onclick="fetch(\'views\', \''+ fi +'\'  )" class="list-group-item list-group-item-body">' +  fi + '</li>' ;
                    $('#listviewdir').append( li );
                    
                }

            }
         
            /*
            var editor = ace.edit('aceEditor');
            editor.session.setMode('ace/mode/php');
            $("#lenW").val('php');
            editor.session.setValue(data.contenido.join(''));
            if(data.ok)
                $('#toastAbrir').toast('show');
            else
                $('#toastError').toast('show');*/
        }
    });
}


 //consigue el contenido del archivo solicitado
function fetch(tipo = 'controllers', destino ='', show = true) { 
  
    gtipo = tipo;
    gdestino = destino;

    // trabajar la url para realizar la peticion 
    var url = window.location.href.split('/');
    var nombre = url[url.length-2];
    switch (tipo) {
        case 'controllers': nombre += '_controller.php'; break;
        case 'models': nombre += '.php'; break;
        case 'views': nombre = nombre + '/' + destino ; break;
    }
    
    url[url.length-1] = 'rest_fetch';
    var targetUrl = url.join('/');

    settitle(nombre);
    console.log('......', destino)
    console.log('...... show', show);

    if (show) 
        modalCarga();
    


    $.ajax({
        type: 'POST',
        url: targetUrl,
        data:   JSON.stringify( {nombre, tipo} ),
        success: function (data) {
            if(show){
            modalCarga(false, function () {
                
                console.log('datos en fetch', data)
                var editor = ace.edit('aceEditor');
                editor.session.setMode('ace/mode/php');
                $("#lenW").val('php');
                editor.session.setValue(data.contenido.join(''));

                if(data.ok)
                    $('#toastAbrir').toast('show');
                else
                    $('#toastError').toast('show');
            })}; 
        }
    });
}

function settitle(newtitle) {
     const titles = newtitle.split('/')
     $('#codeTitle').text(titles[ titles.length - 1 ]);
}

function gettitle() {
    
    return $('#codeTitle').text();
}

 //guardar el contenido del archivo
function save() { 
  
    // trabajar la url para realizar la peticion 
    var url = window.location.href.split('/');
    var nombre = url[url.length-2];
    var editor = ace.edit('aceEditor');
    var contenido =  editor.getValue().split("\n");
    switch (gtipo) {
        case 'controllers': nombre += '_controller.php'; break;
        case 'models': nombre += '.php'; break;
        case 'views': nombre = nombre + '/' + gdestino ; break;
    }
    
    url[url.length-1] = 'rest_save';
    var targetUrl = url.join('/');
    modalCarga();
    console.log('nombre a guardar', nombre)

    $.ajax({
        type: 'POST',
        url: targetUrl,
        data:   JSON.stringify( {nombre, tipo:gtipo, contenido} ),
        success: function (data) {
           
            

                modalCarga(false, function () { 

                    if(data.ok)
                        $('#toastGuardar').toast('show');
                    else
                        $('#toastError').toast('show');
                });
               
       

            console.log(data);
        }
    }); 
}

function newview() {
    const valor = this.val
    const item = '<input id="inputnew" type="text" class="list-group-item" placeholder="nuevaVista.phtml"> '
    $('#listviewdir').append( item  );
}

function createview() {
    const nuevavista = $('#inputnew').val();
    gdestino = nuevavista;
    gtipo = 'views';
    save();
    fetch('views', nuevavista, false);
    console.log('vista nueva', nuevavista);
}



function    seleccionarLenguaje(selectObject){
    var selectedLenguage = selectObject.value;

    var editor = ace.edit('aceEditor');
    editor.session.setMode('ace/mode/'+selectedLenguage);


}


function openCode(files){
    var editor = ace.edit('aceEditor');
    var file = files[0]
    name = file.name
    settitle(name);
    if (!file) return;
    var modelist = ace.require("ace/ext/modelist")
    var modeName = modelist.getModeForPath(file.name).mode 
    var mode = modeName.split('/')
    editor.session.setMode(modeName)
    reader = new FileReader();
    reader.onload = function() {
        editor.session.setValue(reader.result)
    }  
    reader.readAsText(file)
    $("#lenW").val(mode[2]);

}

function guardar() { 

    var editor = ace.edit('aceEditor');

   var ev =  editor.getValue();

   var HTMLhiddenElement = document.createElement("a");

   HTMLhiddenElement.href =  URL.createObjectURL( new Blob( [ev] , { type: "text/plain" } ) );
   HTMLhiddenElement.target = '_blank';
   HTMLhiddenElement.download = gettitle();
   HTMLhiddenElement.click();

}

 function tema(  ) {
    
     obscuro = $('#tema').is(':checked');
     console.log('bbbbb', obscuro );
     var editor = ace.edit('aceEditor');
     if( obscuro ){
        editor.setOptions({ theme: 'ace/theme/dracula' });
     }
    else{
        editor.setOptions({ theme: 'ace/theme/chrome' });
    }


 }