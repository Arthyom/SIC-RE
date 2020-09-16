var gdata1 = {
  labels: ['gmc', 'volsvaguen','honda', 'mazda', 'ford', 'nissan', 
  'dodge', 'tesla', 'kia', 'akura', 'chevrolet', 'catera'],
  datasets: []
}

var gdata2 = {
  labels: ['PRI', 'PAN','PRD', 'MORENA', 'PT', 'PES', 
  'VERDE', 'PRIAN', 'EL BRONCO', 'PSD', 'CONVERGENCIA', 'NO VOTO'],
  datasets: [
    {
      backgroundColor: [ 'cyan', 'brown', 'azure', 'red', 'green', 'blue', 'yellow', 'pink', 'purple', 'orange', 'black'],
      label: 'votos',
      data: []
    }
  ]
}

var host = 'http://192.168.1.65'

function bar() {
  
  $.ajax({
    dataType: "json",
    url:  host +'/florencioK/default/sicaprest/chart_get_data',
    type: 'POST',
  
  })
  .done( function (datas) { 
    console.log('elementos ', datas);
  
  
  try{
  
    var canvas = $('#canvas-bar-wfls')[0].getContext('2d');
    var chart = new Chart(canvas, {
      type: 'bar',
      data: datas.chart_data,
      options: {
        title: {
          display: true,
          text: 'Grafico de barras personalizado'
        },
        scales: {
          xAxes: [{
            ticks: {
              maxRotation: 0,
              maxTicksLimit: 5
            }
          }]
        }
      }
    }); // randomize data
  
  }
  catch( exs) {
      console.log('error de busqueda');
  }
   
   })
  .fail( function(params) {
    console.log(' fallo al obtener datos ', );
  })
}



function line() {
  
  $.ajax({
    dataType: "json",
    url:  host +'/florencioK/default/sicaprest/chart_get_dataset',
    type: 'POST',
  
  })
  .done( function (datas) { 
    console.log('elementos line ', datas);
   
  
  try{
    gdata1.datasets.push(datas.dataset);
  
    var canvas = $('#canvas-line-wfls')[0].getContext('2d');
    var chart = new Chart(canvas, {
      type: 'line',
      data: gdata1,
      options: {
        title: {
          display: true,
          text: 'Grafico de lineas personalizado'
        },
        scales: {
          xAxes: [{
            ticks: {
              maxRotation: 0,
              maxTicksLimit: 5
            }
          }]
        }
      }
    }); // randomize data
  
  }
  catch( exs) {
      console.log('error de busqueda');
  }
   
  
  
   })
  .fail( function(params) {
    console.log(' fallo al obtener datos ', );
  })

}  



function pie() { 
 
  $.ajax({
    dataType: "json",
    url:  host +'/florencioK/default/sicaprest/chart_get_information',
    type: 'POST',
  
  })
  .done( function (datas) { 
    console.log('elementos pie', datas);
    
  
  try{
  
    gdata2.datasets[0].data = datas.data;
    var canvas = $('#canvas-pie-wfls')[0].getContext('2d');
    var chart = new Chart(canvas, {
      type: 'pie',
      data: gdata2,
      options: {
        legend:{
          display: false
        },
        title: {
          display: true,
          text: 'Grafico de pastel'
        }
      }
    }); // randomize data
  
  }
  catch( exs) {
      console.log('error de busqueda');
  }
   
  
  
   })
  .fail( function(params) {
    console.log(' fallo al obtener datos ', );
  })
  


 }  


 function per() {

  var d = { 
    query: "SELECT COUNT(IdPersona) FROM abogados Where IdPersona LIKE ",	
    resource: "abogados",	
    title: "Ventas por empleado",
    colors : ["red", "green", "yellow"], 
    labels: ["0", "11", "1"], 
    label: "ID Empleados", 
    type: "line"
  }

  $.ajax({
    dataType: "json",
    url:  host +'/florencioK/default/sicaprest/chart_create_from',
    type: 'POST',
    data: JSON.stringify( d ),
  
  })
  .done( function (params) { 

    console.log('parametros ', params );


    var canvas = $('#canvas-per-wfls')[0].getContext('2d');
    var chart = new Chart(canvas, {
      type: params.type,
      data: params.vals,
      options: {
        legend:{
          display: false
        },
        title: {
          display: true,
          text: params.title
        }
    }
  }); // randomize data
  


  });   
 }


 bar()
 line()
 pie()
 per()