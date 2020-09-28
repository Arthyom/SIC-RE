
function mialerta(Cadena)
{
  if (Cadena.length>0)
  {
     alert(Cadena);
  }

}

function a_moneda(e)
{
var tabkey = e.keyCode;
var keynum = window.event ? window.event.keyCode : e.which;
if (keynum == 8 || keynum == 46 || tabkey == 9 || tabkey == 37 || tabkey == 39)
return true;
return /\d/.test(String.fromCharCode(keynum));
}

function a_numero(e)
{
var tabkey = e.keyCode;
var keynum = window.event ? window.event.keyCode : e.which;
if (keynum == 8 || tabkey == 9 || tabkey == 37 || tabkey == 39)
return true;
return /\d/.test(String.fromCharCode(keynum));
}

function a_numero_intro(e)
{
var tabkey = e.keyCode;
var keynum = window.event ? window.event.keyCode : e.which;
if (keynum == 8 || tabkey == 9 || tabkey == 37 || tabkey == 39 || tabkey == 13)
return true;
return /\d/.test(String.fromCharCode(keynum));
}

function prohibir_intro(e)
{
var tabkey = e.keyCode;
var keynum = window.event ? window.event.keyCode : e.which;
if (tabkey == 13)
return false;
}

function si_focos(elemento)
{
  elemento.style.borderWidth = "2px";
  elemento.style.borderColor = "#000000";
  elemento.style.backgroundColor = "#FFFF99";
  elemento.style.borderStyle = "outset";
}
function no_focos(elemento)
{
  elemento.style.borderWidth = "2px";
  elemento.style.borderColor = "#CCCCCC";
  elemento.style.backgroundColor = "#FFFFFF";
  elemento.style.borderStyle = "outset";
}
function codifica(texto)
{
    ref='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVXYZ';
    resultado=' ';
    for (cont=0; cont<texto.length; cont++)
       {
         caracter=texto.substring(cont,cont+1);
         pos=ref.indexOf(caracter);
         codigo=ref.substring(pos+1,pos+2);
         resultado+=codigo;
       }
    return resultado;
}

// Valida el formulario de USUARIOS
function validarusuarios(formulario)
{
  if (formulario.Nombre.value.length < 6)
  {
     alert("Escriba por lo menos un nombre con 6 caracteres.");
     formulario.Nombre.focus();
     return (false);
  }
}

function validarusuariosclave(formulario)
{
   formulario.Clave.value =window.btoa(formulario.Clave.value);
   //formulario.Clave.value =codifica(formulario.Clave.value)

}

function validarusuariosclave2(formulario)
{
   
   formulario.Clave.value =window.btoa(formulario.Clave.value);
   formulario.Confirma.value =window.btoa(formulario.Confirma.value);
   
   //formulario.Clave.value =codifica(formulario.Clave.value);
   //formulario.Confirma.value =codifica(formulario.Confirma.value);
   
    return (true);
      
}

// funcion checa correo
function ValidaCorreo(Cadena)
 {
   	if(!Cadena.match(/[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/) && Cadena.length>0) 
     {               
			return false;
		 }
		 return true;
 }
// funcion checa telefono
function ValidaTelefono(Cadena)
 {
   	if(!Cadena.match(/^\(?\d{3}\)?[\s\.-]?\d{3}[\s\.-]?\d{4}$/) && Cadena.length>0) 
     {               
			return (false);
		 }
		 return (true);
 }
// funcion checa solo numeros
function ValidaNumero(Cadena)
 {
   	if(!Cadena.match(/^[0-9.-]+$/) && Cadena.length>0) 
     {
			return (false);
		 }
		 return (true);
 }
// funcion checa solo cadena alfanumerica
function ValidaAlfanumerico(Cadena)
 {
   	if(!Cadena.match(/^[a-zA-Z0-9_ .-/ñÑáéíóú]+$/) && Cadena.length>0) 
     {
			return (false);
		 }
		 return (true);
 }
// funcion checa solo cadena
function ValidaCaracteres(Cadena)
 {
   	if(!Cadena.match(/^[a-zA-Z ñÑáéíóú]+$/) && Cadena.length>0) 
     {
			return (false);
		 }
		 return (true);
 }
// funcion checa formato de rfc
function ValidaRFC(Cadena)
 {
   	if(!Cadena.match(/[A-Z]{4}[0-9]{6}/) && Cadena.length>0) 
     {
			return (false);
		 }
		 return (true);
 }

function ValidaRFCcon(Cadena)
 {
   	if(!Cadena.match(/[ A-Z]{4}[0-9]{6}[A-Z0-9]{3}/) && Cadena.length>0  ) 
     {
			return (false);
		 }
		 return (true);
 }
 
// funcion checa fecha
function ComparaCadena(Cadena1,Cadena2)
 {
    if (Cadena1<=Cadena2)
      return (true);
    else
      return (false);
}
function ValidarFecha(Cadena)
 {
	   var Fecha= new String(Cadena)	// Crea un string
	   var RealFecha= new Date()	// Para sacar la fecha de hoy
	   // extrae el año el mes y el dia de la fecha
	   var Ano= new String(Fecha.substring(Fecha.lastIndexOf("/")+1,Fecha.length))
     var Mes= new String(Fecha.substring(Fecha.indexOf("/")+1,Fecha.lastIndexOf("/")))
	   var Dia= new String(Fecha.substring(0,Fecha.indexOf("/")))

	   // valida que no tenga cero antes de un numero sino lo quita.
	   if (Dia.substring(0,1)=='0')
	   {
	      Dia=Dia.substring(1,1);
	   }
 	   // Valido el año
	   if (isNaN(Ano) || Ano.length<4 || parseFloat(Ano)<1900)
		 {
		    return false
	    }
	   // Valido el Mes
	   if (isNaN(Mes) || parseFloat(Mes)<1 || parseFloat(Mes)>12)
		 {
		   return false
	   }
 	   // Valido el Dia
	   if (isNaN(Dia) || parseInt(Dia)<1 || parseInt(Dia)>31)
		 {
		   return false
	   }
	   if (Mes==4 || Mes==6 || Mes==9 || Mes==11)
		 {
		   if (Dia>30)
			 return false
		 }
 	   if (Mes==2)
		 {
		   if ((Ano % 4 == 0) && ((Ano % 100 != 0) || (Ano % 400 == 0)))
		   {
		     if (Dia > 29)
		      { return false;}
		   }
		   else
		   {
		   if (Dia > 28)
		       return false;
		   }
		 }

   return true
}

// proceso para buscar en un catalogo externo
function jBusca(miurl)
  {
     NuevaVentana= window.open(miurl,'modal','scrollbars=yes, height=600, width=780, modal=yes'); 
     NuevaVentana.moveTo(100,100);
     NuevaVentana.focus();
  }
function jBusca2(miurl)
  {
     NuevaVentana2= window.open(miurl,'modal','scrollbars=yes, height=600, width=600, modal=yes');
     NuevaVentana2.moveTo(100,100);
     NuevaVentana2.focus();
  }

function jBusca3(miurl)
  {
     NuevaVentana3= window.open(miurl,'modal','scrollbars=yes, height=470, width=640, modal=yes');
     NuevaVentana3.moveTo(100,100);
     NuevaVentana3.focus();
  }
function jAltaSocio(miurl)
  {
     NuevaVentanap= window.open(miurl,'modal','scrollbars=yes, height=780, width=780, modal=yes');
     NuevaVentanap.moveTo(100,100);
     NuevaVentanap.focus();
  }

function jModificaSocio(miurl)
  {
     NuevaVentana= window.open(miurl,'modal','scrollbars=yes, height=480, width=1080, modal=yes');
     NuevaVentana.moveTo(200,100);
     NuevaVentana.focus();
  }

// dibujar una barra
function bar(high,val){
document.write("<td valign=bottom>",high,
   "<table bgcolor=#aa2222 border=",border," height=",high,
   " width=",width,
   " cellpadding=0 cellspacing=0>",
   "<tr><td align=center valign=bottom>",
   "<font color=#00000 size=-1 >",val,"</font>",
   "</td></tr></table></td>")
}

function IsNumeric(valor)
{
var log=valor.length; var sw="S";
for (x=0; x<log; x++)
{ v1=valor.substr(x,1);
v2 = parseInt(v1);
//Compruebo si es un valor numérico
if (isNaN(v2)) { sw= "N";}
}
if (sw=="S") {return true;} else {return false; }
}

var primerslap=false;
var segundoslap=false;
function formateafecha(fecha)
{
var long = fecha.length;
var dia;
var mes;
var ano;

if ((long>=2) && (primerslap==false)) { dia=fecha.substr(0,2);
if ((IsNumeric(dia)==true) && (dia<=31) && (dia!="00")) { fecha=fecha.substr(0,2)+"/"+fecha.substr(3,7); primerslap=true; }
else { fecha=""; primerslap=false;}
}
else
{ dia=fecha.substr(0,1);
if (IsNumeric(dia)==false)
{fecha="";}
if ((long<=2) && (primerslap=true)) {fecha=fecha.substr(0,1); primerslap=false; }
}
if ((long>=5) && (segundoslap==false))
{ mes=fecha.substr(3,2);
if ((IsNumeric(mes)==true) &&(mes<=12) && (mes!="00")) { fecha=fecha.substr(0,5)+"/"+fecha.substr(6,4); segundoslap=true; }
else { fecha=fecha.substr(0,3);; segundoslap=false;}
}
else { if ((long<=5) && (segundoslap=true)) { fecha=fecha.substr(0,4); segundoslap=false; } }
if (long>=7)
{ ano=fecha.substr(6,4);
if (IsNumeric(ano)==false) { fecha=fecha.substr(0,6); }
else { if (long==10){ if ((ano==0) || (ano<1900) || (ano>2100)) { fecha=fecha.substr(0,6); } } }
}

if (long>=10)
{
fecha=fecha.substr(0,10);
dia=fecha.substr(0,2);
mes=fecha.substr(3,2);
ano=fecha.substr(6,4);
// Año no viciesto y es febrero y el dia es mayor a 28
if ( (ano%4 != 0) && (mes ==02) && (dia > 28) ) { fecha=fecha.substr(0,2)+"/"; }
}
return (fecha);
} 

function validaformularioaltasocio()
{
 if (document.formulario.Nombre.value.length < 1)
  {
     alert("Necesito que ingrese el nombre de la persona.");
     document.formulario.Nombre.focus();
     return 0;
  }
  if (document.formulario.ApellidoPaterno.value.length < 1)
  {
     alert("Necesito que ingrese el apellido paterno de la persona.");
     document.formulario.ApellidoPaterno.focus();
     return 0;
  }
  if (document.formulario.ApellidoMaterno.value.length < 1)
  {
     alert("Necesito que ingrese el apellido materno de la persona.");
     document.formulario.ApellidoMaterno.focus();
     return 0;
  }
  if (document.formulario.IdBarrio.value.length < 1)
  {
     alert("No ha definido un barrio, seleccione uno por favor.");
     document.formulario.IdBarrio.focus();
     return 0;
  }
    
  if (document.formulario.CURP.value.length > 0)
  {
   	if(!document.formulario.CURP.value.match(/[ A-Z]{4}[0-9]{6}[H,M][A-Z]{5}[0-9]{2}/)) 
     {
     alert("la CURP no es valida revisela por favor.");
     document.formulario.CURP.focus();
   			return (false);
		   }
 	
  }
  if (document.formulario.RFC.value.length > 0)
  {
   	if(!document.formulario.RFC.value.match(/[ A-Z]{4}[0-9]{6}[A-Z0-9]{3}/)) 
     {
     alert("El RFC no es valido revisela por favor.");
     document.formulario.RFC.focus();
   			return (false);
		   }
 	
  }
}




function obtieneRFC(forma, pstRFC){
		with (forma) {
			var fecha = dsFechaNac.value;
			var sexo = idSexo.value;
			var estado = idEstadoNac.value;
			var nombre = dsNombre.value;
			var paterno = dsPaterno.value;
			var materno = dsMaterno.value;	
		}
		
		fecha = fecha.substring(6)+"-"+fecha.substring(3,5)+"-"+fecha.substring(0,2);
		
		if (sexo == 1){
			sexo = "H";
		} 
		if (sexo == 2){
			sexo = "M";
		}
		switch (estado){
			case "1":
			estado = "AS";
			break;
			case "2":
			estado = "BC";
			break;
			case "3":
			estado = "BS";
			break;
			case "4":
			estado = "CC";
			break;
			case "5":
			estado = "CS";
			break;
			case "6":
			estado = "CH";
			break;
			case "7":
			estado = "CL";
			break;
			case "8":
			estado = "CM";
			break;
			case "9":
			estado = "DF";
			break;
			case "10":
			estado = "DG";
			break;
			case "11":
			estado = "GT";
			break;
			case "12":
			estado = "GR";
			break;
			case "13":
			estado = "HG";
			break;
			case "14":
			estado = "JC";
			break;
			case "15":
			estado = "MC";
			break;
			case "16":
			estado = "MN";
			break;
			case "17":
			estado = "MS";
			break;
			case "18":
			estado = "NT";
			break;
			case "19":
			estado = "NL";
			break;
			case "20":
			estado = "OC";
			break;
			case "21":
			estado = "PL";
			break;
			case "22":
			estado = "QT";
			break;
			case "23":
			estado = "QR";
			break;
			case "24":
			estado = "SP";
			break;
			case "25":
			estado = "SL";
			break;
			case "26":
			estado = "SR";
			break;
			case "27":
			estado = "TC";
			break;
			case "28":
			estado = "TS";
			break;
			case "29":
			estado = "TL";
			break;
			case "30":
			estado = "VZ";
			break;
			
			
		}
		pstRFC.value = fnCalculaCURP( nombre, paterno, materno, fecha, sexo, estado ).substring(0,10);
	}
function obtieneCURP(forma, pstCURP){
		with (forma) {
			var fecha = dsFechaNac.value;
			var sexo = idSexo.value;
			var estado = idEstadoNac.value;
			var nombre = dsNombre.value;
			var paterno = dsPaterno.value;
			var materno = dsMaterno.value;	
		}
		
		fecha = fecha.substring(6)+"-"+fecha.substring(3,5)+"-"+fecha.substring(0,2);
		
		if (sexo == 1){
			sexo = "H";
		} 
		if (sexo == 2){
			sexo = "M";
		}
		switch (estado){
			case "1":
			estado = "AS";
			break;
			case "2":
			estado = "BC";
			break;
			case "3":
			estado = "BS";
			break;
			case "4":
			estado = "CC";
			break;
			case "5":
			estado = "CS";
			break;
			case "6":
			estado = "CH";
			break;
			case "7":
			estado = "CL";
			break;
			case "8":
			estado = "CM";
			break;
			case "9":
			estado = "DF";
			break;
			case "10":
			estado = "DG";
			break;
			case "11":
			estado = "GT";
			break;
			case "12":
			estado = "GR";
			break;
			case "13":
			estado = "HG";
			break;
			case "14":
			estado = "JC";
			break;
			case "15":
			estado = "MC";
			break;
			case "16":
			estado = "MN";
			break;
			case "17":
			estado = "MS";
			break;
			case "18":
			estado = "NT";
			break;
			case "19":
			estado = "NL";
			break;
			case "20":
			estado = "OC";
			break;
			case "21":
			estado = "PL";
			break;
			case "22":
			estado = "QT";
			break;
			case "23":
			estado = "QR";
			break;
			case "24":
			estado = "SP";
			break;
			case "25":
			estado = "SL";
			break;
			case "26":
			estado = "SR";
			break;
			case "27":
			estado = "TC";
			break;
			case "28":
			estado = "TS";
			break;
			case "29":
			estado = "TL";
			break;
			case "30":
			estado = "VZ";
			break;
			
			
		}
		pstCURP.value = fnCalculaCURP( nombre, paterno, materno, fecha, sexo, estado );
	}
	
function fnCalculaCURP( pstNombre, pstPaterno, pstMaterno, dfecha, pstSexo, pnuCveEntidad ) {  
/*
pstNombre="MARIA TERESITA DEL NIÑO JESUS";
pstPaterno="AGUERREBERE";
pstMaterno="BARROSO";
dfecha="1937-11-22";
pstSexo = "M";
pnuCveEntidad ="DF";

pstNombre="ROCIO";
pstPaterno="URIBARREN";
pstMaterno="AGUERREBERE";
dfecha="1969-02-03";
pstSexo = "M";
pnuCveEntidad ="DF";


pstNombre="AINHOA";
pstPaterno="ESTURAU";
pstMaterno="URIBARREN";
dfecha="2003-03-05";
pstSexo = "M";
pnuCveEntidad ="QR";




pstNombre="MARIO";
pstPaterno="PIÑA";
pstMaterno="FLORES";
dfecha="1968-03-30";
pstSexo = "H";
pnuCveEntidad ="DF";
*/

pstCURP   ="";
pstDigVer ="";
contador  =0;
contador1 =0;
pstCom	  ="";
numVer    =0.00;
valor     =0;
sumatoria =0;



// se declaran las varibale que se van a utilizar para ontener la CURP

NOMBRES  ="";
APATERNO ="";
AMATERNO ="";
T_NOMTOT ="";
NOMBRE1  =""; //PRIMER NOMBRE
NOMBRE2  =""; //DEMAS NOMBRES
NOMBRES_LONGITUD =0; //LONGITUD DE TODOS @NOMBRES
var NOMBRE1_LONGITUD =0; //LONGITUD DEL PRIMER NOMBRE(MAS UNO,EL QUE SOBRA ES UN ESPACIO EN BLANCO)
APATERNO1 =""; //PRIMER NOMBRE
APATERNO2 =""; //DEMAS NOMBRES
APATERNO_LONGITUD =0; //LONGITUD DE TODOS @NOMBRES
APATERNO1_LONGITUD =0; //LONGITUD DEL PRIMER NOMBRE(MAS UNO,EL QUE SOBRA ES UN ESPACIO EN BLANCO)
AMATERNO1 =""; //PRIMER NOMBRE
AMATERNO2 =""; //DEMAS NOMBRES
AMATERNO_LONGITUD =0; //LONGITUD DE TODOS @NOMBRES
AMATERNO1_LONGITUD =0; //LONGITUD DEL PRIMER NOMBRE(MAS UNO,EL QUE SOBRA ES UN ESPACIO EN BLANCO)
VARLOOPS =0; //VARIABLE PARA LOS LOOPS, SE INICIALIZA AL INICIR UN LOOP


// Se inicializan las variables para obtener la primera parte de la CURP


NOMBRES  = pstNombre.replace(/^\s+|\s+$/g,"");
APATERNO = pstPaterno.replace(/^\s+|\s+$/g,"");
AMATERNO = pstMaterno.replace(/^\s+|\s+$/g,"");

T_NOMTOT = APATERNO + ' '+ AMATERNO + ' '+ NOMBRES;



// Se procesan los nombres de pila


VARLOOPS = 0;

while (VARLOOPS != 1)
	{

		NOMBRES_LONGITUD = NOMBRES.length

		var splitNombres = NOMBRES.split(" ");
		var splitNombre1 = splitNombres[0];
		
		NOMBRE1_LONGITUD = splitNombre1.length;
//		NOMBRE1_LONGITUD = PATINDEX('% %',@NOMBRES)

		if (NOMBRE1_LONGITUD = 0)
		   {
		    NOMBRE1_LONGITUD = NOMBRES_LONGITUD;
		   }
		    NOMBRE1 =  NOMBRES.substring(0,splitNombre1.length);
		    NOMBRE2 =  NOMBRES.substring(splitNombre1.length + 1, NOMBRES.length);


// Se quitan los nombres de JOSE, MARIA,MA,MA.
/*
if (NOMBRE1 IN ('JOSE','MARIA','MA.','MA','DE','LA','LAS','MC','VON','DEL','LOS','Y','MAC','VAN') && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2
}
else
{
		VARLOOPS = 1
}
*/

if (NOMBRE1 == 'JOSE' && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2;
}
else
{
		VARLOOPS = 1;
}

if (NOMBRE1 == 'MARIA' && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2;
}
else
{
		VARLOOPS = 1;
}

if (NOMBRE1 == 'MA.' && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2;
}
else
{
		VARLOOPS = 1;
}

if (NOMBRE1 == 'MA' && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2;
}
else
{
		VARLOOPS = 1;
}

if (NOMBRE1 == 'DE' && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2;
}
else
{
		VARLOOPS = 1;
}

if (NOMBRE1 == 'LA' && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2;
}
else
{
		VARLOOPS = 1;
}

if (NOMBRE1 == 'LAS' && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2;
}
else
{
		VARLOOPS = 1;
}


if (NOMBRE1 == 'MC' && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2;
}
else
{
		VARLOOPS = 1;
}

if (NOMBRE1 == 'VON' && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2;
}
else
{
		VARLOOPS = 1;
}


if (NOMBRE1 == 'DEL' && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2;
}
else
{
		VARLOOPS = 1;
}


if (NOMBRE1 == 'LOS' && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2;
}
else
{
		VARLOOPS = 1;
}

if (NOMBRE1 == 'Y' && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2;
}
else
{
		VARLOOPS = 1;
}

if (NOMBRE1 == 'MAC' && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2;
}
else
{
		VARLOOPS = 1;
}

if (NOMBRE1 == 'VAN' && NOMBRE2 != '')
{
		NOMBRES = NOMBRE2;
}
else
{
		VARLOOPS = 1;
}

} // fin varloops <> 1


// Se procesan los APELLIDOS, PATERNO EN UN LOOP

VARLOOPS = 0;

while (VARLOOPS != 1)
{

		//SET @APATERNO_LONGITUD = LEN(@APATERNO)
		APATERNO_LONGITUD = APATERNO.length;		
		
		//SET @APATERNO1_LONGITUD = PATINDEX('% %',@APATERNO)
		var splitPaterno = APATERNO.split(" ");
		var splitPaterno1 = splitPaterno[0];
		APATERNO1_LONGITUD = splitPaterno1.length;

		if (APATERNO1_LONGITUD = 0)
		   {
		     APATERNO1_LONGITUD = APATERNO_LONGITUD;
		   }
//		APATERNO1 = RTRIM(LEFT(@APATERNO,@APATERNO1_LONGITUD))
//		APATERNO2 = LTRIM(RIGHT(@APATERNO,@APATERNO_LONGITUD - @APATERNO1_LONGITUD))

		APATERNO1 =  APATERNO.substring(0,splitPaterno1.length);
		APATERNO2 =  APATERNO.substring(splitPaterno1.length + 1, APATERNO.length);

// Se quitan los sufijos

/*
IF @APATERNO1 IN ('DE','LA','LAS','MC','VON','DEL','LOS','Y','MAC','VAN') AND @APATERNO2 <> ''
	BEGIN
		SET @APATERNO = @APATERNO2
	END 
ELSE
	BEGIN
		SET @VARLOOPS = 1
	END
}
*/

if ( APATERNO1 == 'DE' && APATERNO2 != '')
	{
		APATERNO = APATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}


if ( APATERNO1 == 'LA' && APATERNO2 != '')
	{
		APATERNO = APATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}



if ( APATERNO1 == 'LAS' && APATERNO2 != '')
	{
		APATERNO = APATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}

if ( APATERNO1 == 'MC' && APATERNO2 != '')
	{
		APATERNO = APATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}

if ( APATERNO1 == 'VON' && APATERNO2 != '')
	{
		APATERNO = APATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}

if ( APATERNO1 == 'DEL' && APATERNO2 != '')
	{
		APATERNO = APATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}


if ( APATERNO1 == 'LOS' && APATERNO2 != '')
	{
		APATERNO = APATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}

if ( APATERNO1 == 'Y' && APATERNO2 != '')
	{
		APATERNO = APATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}

if ( APATERNO1 == 'MAC' && APATERNO2 != '')
	{
		APATERNO = APATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}

if ( APATERNO1 == 'VAN' && APATERNO2 != '')
	{
		APATERNO = APATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}

} // fin varloops


// Faltan: )


// Se procesan los APELLIDOS, MATERNO EN UN LOOP

VARLOOPS = 0;

while (VARLOOPS != 1)
{

		//SET @APATERNO_LONGITUD = LEN(@APATERNO)
		AMATERNO_LONGITUD = AMATERNO.length;		
		
		//SET @APATERNO1_LONGITUD = PATINDEX('% %',@APATERNO)
		var splitMaterno = AMATERNO.split(" ");
		var splitMaterno1 = splitMaterno[0];
		AMATERNO1_LONGITUD = splitMaterno1.length;

		if (AMATERNO1_LONGITUD = 0)
		   {
		     AMATERNO1_LONGITUD = AMATERNO_LONGITUD;
		   }

		AMATERNO1 =  AMATERNO.substring(0,splitMaterno1.length);
		AMATERNO2 =  AMATERNO.substring(splitMaterno1.length + 1, AMATERNO.length);

// Se quitan los sufijos


/*
IF @APATERNO1 IN ('DE','LA','LAS','MC','VON','DEL','LOS','Y','MAC','VAN') AND @APATERNO2 <> ''
	BEGIN
		SET @APATERNO = @APATERNO2
	END 
ELSE
	BEGIN
		SET @VARLOOPS = 1
	END
}
*/

if ( AMATERNO1 == 'DE' && AMATERNO2 != '')
	{
		AMATERNO = AMATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}


if ( AMATERNO1 == 'LA' && AMATERNO2 != '')
	{
		AMATERNO = AMATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}


if ( AMATERNO1 == 'LAS' && AMATERNO2 != '')
	{
		AMATERNO = AMATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}

if ( AMATERNO1 == 'MC' && AMATERNO2 != '')
	{
		AMATERNO = AMATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}

if ( AMATERNO1 == 'VON' && AMATERNO2 != '')
	{
		AMATERNO = AMATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}

if ( AMATERNO1 == 'DEL' && AMATERNO2 != '')
	{
		AMATERNO = AMATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}

if ( AMATERNO1 == 'LOS' && AMATERNO2 != '')
	{
		AMATERNO = AMATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}

if ( AMATERNO1 == 'Y' && AMATERNO2 != '')
	{
		AMATERNO = AMATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}

if ( AMATERNO1 == 'MAC' && AMATERNO2 != '')
	{
		AMATERNO = AMATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}

if ( AMATERNO1 == 'VAN' && AMATERNO2 != '')
	{
		AMATERNO = AMATERNO2;
	}
else
	{
		VARLOOPS = 1;
	}



} // fin varloops




// Se obtiene del primer apellido la primer letra y la primer vocal interna

pstCURP = APATERNO1.substring(0,1);

APATERNO1_LONGITUD= APATERNO1.length;
VARLOOPS = 0 // EMPIEZA EN UNO POR LA PRIMERA LETRA SE LA VA A SALTAR

while (APATERNO1_LONGITUD > VARLOOPS)
	{
		VARLOOPS = VARLOOPS + 1;

		// if SUBSTRING(@APATERNO1,@VARLOOPS,1) IN ('A','E','I','O','U')
		var compara = APATERNO1.substr(parseInt(VARLOOPS),1);

		if (compara == 'A')
		   {
			pstCURP = pstCURP + compara;
			VARLOOPS = APATERNO1_LONGITUD;
		   }
		if (compara == 'E')
		   {
			pstCURP = pstCURP + compara;
			VARLOOPS = APATERNO1_LONGITUD;
		   }
		if (compara == 'I')
		   {
			pstCURP = pstCURP + compara;
			VARLOOPS = APATERNO1_LONGITUD;
		   }
		if (compara == 'O')
		   {
			pstCURP = pstCURP + compara;
			VARLOOPS = APATERNO1_LONGITUD;
		   }
		if (compara == 'U')
		   {
		   
			pstCURP = pstCURP + compara;
			VARLOOPS = APATERNO1_LONGITUD;
		   }

	}




// Se obtiene la primer letra del apellido materno 

pstCURP = pstCURP + AMATERNO1.substring(0,1);

// Se le agrega la primer letra del nombre

pstCURP = pstCURP + NOMBRES.substring(0,1);



// Se agrega la fecha de nacimiento, clave del sexo y clave de la entidad

var splitFecha = dfecha.split("-");
var splitAnio  = splitFecha[0].substr(2,2);
var splitMes   = splitFecha[1];
var splitDia   = splitFecha[2];


pstCURP = pstCURP + splitAnio + splitMes + splitDia + pstSexo + pnuCveEntidad



// Se obtiene la primera consonante interna del apellido paterno


VARLOOPS = 0;

while (splitPaterno1.length > VARLOOPS)
      {
	VARLOOPS = VARLOOPS + 1
	var compara = APATERNO1.substr(parseInt(VARLOOPS),1);

	if (compara != 'A' && compara != 'E' && compara != 'I' && compara != 'O' && compara != 'U')
	   {
	    if ( compara == 'Ñ')
		{
	    	 pstCURP = pstCURP + 'X';
		}
	    else
		{
		 pstCURP = pstCURP + compara;
		}

	    VARLOOPS = splitPaterno1.length;
	   }
      }


// Se obtiene la primera consonante interna del apellido materno

VARLOOPS = 0;

while (splitMaterno1.length > VARLOOPS)
      {

	VARLOOPS = VARLOOPS + 1;
	var compara = AMATERNO1.substr(parseInt(VARLOOPS),1);

	if (compara != 'A' && compara != 'E' && compara != 'I' && compara != 'O' && compara != 'U')
	   {
	    if ( compara == 'Ñ')
		{	    
		 pstCURP = pstCURP + 'X';
		}
            else
		{
		 pstCURP = pstCURP + compara;
		}
		
	    VARLOOPS = splitMaterno1.length;
	   }
      }


// Se obtiene la primera consonante interna del nombre

VARLOOPS = 0;

while (splitNombre1.length > VARLOOPS)
      {

	VARLOOPS = VARLOOPS + 1;
	var compara = NOMBRE1.substr(parseInt(VARLOOPS),1);

	if (compara != 'A' && compara != 'E' && compara != 'I' && compara != 'O' && compara != 'U')
	   {
	    if (compara=='Ñ')	    
		{
		 pstCURP = pstCURP + 'X';
		}
	    else
		{
		 pstCURP = pstCURP + compara;
		}

	    VARLOOPS = splitNombre1.length;
	   }
      }


// Se obtiene el digito verificador



var contador = 18;
var contador1 = 0;
var valor = 0;
var sumatoria = 0;


while (contador1 <= 15)
	{

        //pstCom = SUBSTRING(@pstCURP,@contador1,1)
	var pstCom = pstCURP.substr(parseInt(contador1),1);
     
		if (pstCom == '0') 
			{
			 valor = 0 * contador ;
			}
		if (pstCom == '1') 
			{
			 valor = 1 * contador;
			}
		if (pstCom == '2') 
			{
			 valor = 2 * contador;
			}
		if (pstCom == '3') 
			{
			 valor = 3 * contador;
			}
		if (pstCom == '4') 
			{
			 valor = 4 * contador;
			}
		if (pstCom == '5') 
			{
			 valor = 5 * contador;
			}
		if (pstCom == '6') 
			{
			 valor = 6 * contador;
			}
		if (pstCom == '7') 
			{
			 valor = 7 * contador;
			}
		if (pstCom == '8') 
			{
			 valor = 8 * contador;
			}
		if (pstCom == '9') 
			{
			 valor = 9 * contador;
			}
		if (pstCom == 'A') 
			{
			 valor = 10 * contador;
			}
		if (pstCom == 'B') 
			{
			 valor = 11 * contador;
			}
		if (pstCom == 'C') 
			{
			 valor = 12 * contador;
			}
		if (pstCom == 'D') 
			{
			 valor = 13 * contador;
			}
		if (pstCom == 'E') 
			{
			 valor = 14 * contador;
			}
		if (pstCom == 'F') 
			{
			 valor = 15 * contador;
			}
		if (pstCom == 'G') 
			{
			 valor = 16 * contador;
			}
		if (pstCom == 'H') 
			{
			 valor = 17 * contador;
			}
		if (pstCom == 'I') 
			{
			 valor = 18 * contador;
			}
		if (pstCom == 'J') 
			{
			 valor = 19 * contador;
			}
		if (pstCom == 'K') 
			{
			 valor = 20 * contador;
			}
		if (pstCom == 'L') 
			{
			 valor = 21 * contador;
			}
		if (pstCom == 'M') 
			{
			 valor = 22 * contador;
			}
		if (pstCom == 'N') 
			{
			 valor = 23 * contador;
			}
		if (pstCom == 'Ñ') 
			{
			 valor = 24 * contador;
			}
		if (pstCom == 'O') 
			{
			 valor = 25 * contador;
			}
		if (pstCom == 'P') 
			{
			 valor = 26 * contador;
			}
		if (pstCom == 'Q') 
			{
			 valor = 27 * contador;
			}
		if (pstCom == 'R') 
			{
			 valor = 28 * contador;
			}
		if (pstCom == 'S') 
			{
			 valor = 29 * contador;
			}
		if (pstCom == 'T') 
			{
			 valor = 30 * contador;
			}
		if (pstCom == 'U') 
			{
			 valor = 31 * contador;
			}
		if (pstCom == 'V') 
			{
			 valor = 32 * contador;
			}
		if (pstCom == 'W') 
			{
			 valor = 33 * contador;
			}
		if (pstCom == 'X') 
			{
			 valor = 34 * contador;
			}
		if (pstCom == 'Y') 
			{
			 valor = 35 * contador;
			}

		if (pstCom == 'Z') 
			{
 			 valor = 36 * contador;
			}

		contador  = contador - 1;
		contador1 = contador1 + 1;
		sumatoria = sumatoria + valor;

	}

numVer  = sumatoria % 10;
numVer  = Math.abs(10 - numVer);
anio = splitFecha[0];


if (numVer == 10)
	{
	 numVer = 0;
	}


if (anio < 2000)
	{
	 pstDigVer = '0' + '' + numVer;
	}
if (anio >= 2000)
	{
	 pstDigVer = 'A' + '' + numVer;
	}

pstCURP = pstCURP + pstDigVer;


	return pstCURP
} // End if
