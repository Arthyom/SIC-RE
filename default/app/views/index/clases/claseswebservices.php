<?php  
 class Rest
 {  
   public $tipo = "application/json";  
   public $datosPeticion = array();  
   private $_codEstado = 200;  
   public function __construct()
   {  
     $this->tratarEntrada();  
   }  
   public function mostrarRespuesta($data, $estado)
   {  
     $this->_codEstado = ($estado) ? $estado : 200;//si no se envía $estado por defecto será 200  
     $this->setCabecera();  
     echo $data;
     exit;  
   }  
   private function setCabecera()
   {  
     header("HTTP/1.1 " . $this->_codEstado . " " . $this->getCodEstado());  
     header("Content-Type:" . $this->tipo . ';charset=utf-8');  
   }  
   private function limpiarEntrada($data)
   {  
     $entrada = array();  
     if (is_array($data))
     {  
       foreach ($data as $key => $value)
       {  
         $entrada[$key] = $this->limpiarEntrada($value);  
       }  
     } else
     {  
       if (get_magic_quotes_gpc())
       {  
         //Quitamos las barras de un string con comillas escapadas  
         //Aunque actualmente se desaconseja su uso, muchos servidores tienen activada la extensión magic_quotes_gpc.   
         //Cuando esta extensión está activada, PHP añade automáticamente caracteres de escape (\) delante de las comillas que se escriban en un campo de formulario.   
         $data = trim(stripslashes($data));  
       }  
       //eliminamos etiquetas html y php  
       $data = strip_tags($data);  
       //Conviertimos todos los caracteres aplicables a entidades HTML  
       $data = htmlentities($data);  
       $entrada = trim($data);  
     }
     return $entrada;  
   }  
   private function tratarEntrada()
   {  
     $metodo = $_SERVER['REQUEST_METHOD'];  
     switch ($metodo)
     {  
       case "GET":  
         $this->datosPeticion = $this->limpiarEntrada($_GET);  
         break;  
       case "POST":  
         $this->datosPeticion = $this->limpiarEntrada($_POST);  
         break;  
       case "DELETE":// Se ejecutará el case siguiente  
       case "PUT":  
         //php no tiene un método propiamente dicho para leer una petición PUT o DELETE por lo que se usa un "truco":  
         //leer el stream de entrada file_get_contents("php://input") que transfiere un fichero a una cadena.  
         //Con ello obtenemos una cadena de pares clave valor de variables (variable1=dato1&variable2=data2...)
         //que evidentemente tendremos que transformarla a un array asociativo.  
         //Con parse_str meteremos la cadena en un array donde cada par de elementos es un componente del array.  
         parse_str(file_get_contents("php://input"), $this->datosPeticion);  
         $this->datosPeticion = $this->limpiarEntrada($this->datosPeticion);  
         break;  
       default:  
         $this->response('', 404);  
         break;  
     }  
   }  
   private function getCodEstado()
   {  
     $estado = array(  
       200 => 'OK',  
       201 => 'Creado',  
       202 => 'Aceptado',  
       204 => 'No hay contenido',  
       301 => 'Movido permanentemente',  
       302 => 'Encontrado',  
       303 => 'Ver Otros',  
       304 => 'No modificado',  
       400 => 'sin dato requerido',  
       401 => 'No autorizado',  
       403 => 'Forbidden',  
       404 => 'No existe',  
       405 => 'Método no permitido',  
       500 => 'Error interno');  
     $respuesta = ($estado[$this->_codEstado]) ? $estado[$this->_codEstado] : $estado[500];  
     return $respuesta;  
   }  
 }  
 ?>  