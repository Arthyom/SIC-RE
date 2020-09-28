$(function () {
	//console.log('Cargo');
	Cfdi = {
		//urlFac : "sdkfactura/ejemplos/cfdi33/facturanomina.php",
		urlFac : "facturanomina.php",
		urlFunc : "cfdinom/controller.php",
		
		init : function () {
		
		},
		
		facturar : function () {
			//console.log('Entro');
			var This = this;
			var idEmpleado = $("#idEmpleado").html();
			var idNomina = $("#idNomina").html();
			var dataString;
			//console.log(idEmpleado);
			$(".loader").show();
			$.post(This.urlFac, {idEmpleado: idEmpleado, idNomina: idNomina}, function (data, status) {			 	
			 	$(".loader").fadeOut("slow");
			 	dataString = JSON.parse(data);
			 	//console.log(dataString); 
			 	//console.log(JSON.stringify(data));			 	
			 	alert(dataString.message);
			 	if (dataString.status == "success") {			 		
			 		window.open("archivos/nomina/"+dataString.id+".pdf", '_blank');
			 		window.open("archivos/nomina/"+dataString.id+".xml", '_blank');
			 		window.location.href = 'cfdinominaejecutar.php';
			 	}
			});
			
		},

		refacturar : function(){
			var This = this;
			var idEmpleado = $("#idEmpleado").html();
			var idNomina = $("#idNomina").html();
			var idFactura = $("#idFactura").html();
			var dataString;

			//console.log(idFactura);

			$(".loader").show();
			$.post(This.urlFac, {idEmpleado: idEmpleado, idNomina: idNomina, idFactura: idFactura}, function (data, status) {			 	
			 	$(".loader").fadeOut("slow");
			 	dataString = JSON.parse(data);
			 	//console.log(dataString); 
			 	//console.log(JSON.stringify(data));			 	
			 	alert(dataString.message);
			 	if (dataString.status == "success") {			 		
			 		window.open("archivos/nomina/"+dataString.id+".pdf", '_blank');
			 		window.open("archivos/nomina/"+dataString.id+".xml", '_blank');
			 		window.location.href = 'cfdinominaejecutar.php';
			 	}
			});
		},

		enviarMail: function(){
			var This = this;
			var funcion = "mail";
			var idFolio = $("#txtIdFolio").val(); 
			//console.log(idFolio);
			/*if(idFolio > 0){
				$(".loader").show();
				$.post(This.urlFunc,{funcion: funcion, idFolio: idFolio},function(data,status){
					$(".loader").fadeOut("slow");
					dataString = JSON.parse(data);
					console.log(JSON.stringify(data));
				});
			}*/
			
		}


	};
	
	
	$('#buttonFacturar').click(function (e) {
		e.preventDefault();	
		   //console.log('Clic');
      	Cfdi.facturar();	 
    });

    $('#buttonEnviarMail').click(function (e) {
		e.preventDefault();	
      	Cfdi.enviarMail();	 
    }
   	);

   	$('#buttonCancelarRefacturar').click(function(e){
    	e.preventDefault();	
    	Cfdi.refacturar();
    });
		
});

