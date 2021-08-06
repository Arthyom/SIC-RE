$(buscar_datos)

function buscar_datos(consulta){
    $.ajax({
        url: 'app/config/buscar.php',
        type: 'POST',
        dataType: 'phtml',
        data: {consulta: consulta},
    })
    .done(function(respuesta){
        $("#datos").phtml(respuesta);
        
    })
    .fail(function(){
        console.log("error");
    })
}