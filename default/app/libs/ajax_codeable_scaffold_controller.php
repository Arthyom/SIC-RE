<?php

/**
 * Controlador base para la construcción de CRUD para modelos rápidamente
 *
 * @category Kumbia
 * @package Controller
 */
abstract class AjaxcodeableScaffoldController extends ScaffoldController
{

  public function rest_idfields()
  {
      // conseguir los campos con id del modelo indicado
      View::template(null);
      View::select('json');

      $metodo = $_SERVER['REQUEST_METHOD'];
      $respuesta = false;

      if (true) {

     // conseguir la json data de la peticion
          $datos =  json_decode(file_get_contents('php://input'), true);
          $camposSelect  = (new $this->configuracion)->find("conditions: length(TablaForanea) > 0 AND  Type='select' AND  TablaPropietaria ='$this->controller_name'");
          $datosAjustados = [];

          foreach ($camposSelect as $i => $campo) {
              $datoAjustado = [ 'id'=> $i, 'text'=> $campo->Name ];
              array_push($datosAjustados, $datoAjustado);
          }
      }
      $this->data = [ 'items'=> $datosAjustados ];
  }


/**
 * volver el archivo indicado a la fecha especificada
 */
    public function rest_back()
    {
        View::template(null);
        View::select('json');

        $metodo = $_SERVER['REQUEST_METHOD'];
        $respuesta = false;

        if ($metodo == $this->method) {

  // conseguir la json data de la peticion
            $datos =  json_decode(file_get_contents('php://input'), true);

            // tipo -> modelo, vista o controlador
            // archivo -> nombre con extension del archivo
            $contenido = [];
            $tipo      = $datos['tipo'];
            $nombre    = $datos['nombre'];
            $fecha     = $datos['fecha'];
            $fechaDir  = date('j-n-Y', strtotime($fecha));
            $utc       = date('c', strtotime($fecha));
            $usuario = 'SISTEMA'; //Auth::get('Usuario');
            $nombre = str_replace([ '_controller.php', '.php', '.phtml'], '', $nombre);
            $validos = [];
            $rutaEnServidor = APP_PATH."/temp/logs". "/" . $fechaDir . "/". "codificar/" . $usuario;
            $respuesta = false;
            $archivos = scandir($rutaEnServidor);


            foreach ($archivos as $i => $archivo) {
                if (strpos($archivo, $tipo)) {
                    if ($archivo[0] == 'A') {
                        if (strpos($archivo, $utc)) {
                            $respuesta = true;
                            array_push($validos, $archivo);
                            break;
                        }
                    }
                }
            }

            $archivo = $rutaEnServidor .'/'. $validos[0];
            $archivo = fopen($archivo, 'r') or die("no se puede abrir el archivo");

            // leer el contenido linea a liena
            $i = 0;
            while ($linea = fgets($archivo)) {
                if (0 < $i) {
                    array_push($contenido, $linea);
                }
                $i ++;
            }

            fclose($archivo);


            // responder con true para ok, dalso para error
            $this->data = [  'contenido' => $contenido, 'ok'=> $respuesta ];
        }
    }

    /**
     * Resultados paginados
     *
     * @param int $page  Página a mostrar
     */
    public function rest_history()
    {
        View::template(null);
        View::select('json');

        $metodo = $_SERVER['REQUEST_METHOD'];
        $respuesta = false;

        if ($metodo == $this->method) {

   // conseguir la json data de la peticion
            $datos =  json_decode(file_get_contents('php://input'), true);

            // tipo -> modelo, vista o controlador
            // archivo -> nombre con extension del archivo
            $tipo       = $datos['tipo'];
            $nombre     = $datos['nombre'];
            $fecha      = $datos['fecha'];
            $usuario = Auth::get('Usuario');
            $nombre = str_replace([ '_controller.php', '.php', '.phtml'], '', $nombre);
            $validos = [];
            $rutaEnServidor = APP_PATH."/temp/logs". "/" . $fecha . "/". "codificar/" . $usuario;
            $archivos = scandir($rutaEnServidor);


            foreach ($archivos as $i => $archivo) {
                if (strpos($archivo, $tipo)) {
                    if ($archivo[0] == 'A') {
                        $segmentosNombre = explode('_', $archivo);
                        $fechaCreacion = str_replace('.txt', '', $segmentosNombre[3]);
                        $fechaParaHumano = date('d/m/Y G:i:s', strtotime($fechaCreacion));
                        array_push($validos, $fechaParaHumano);
                    }
                }
            }



            // responder con true para ok, dalso para error
            $this->data = [ 'p'=>$patron, 'n'=>$nombre, 'ok'=> $rutaEnServidor  , 'd' => $archivos , 'validos' => $validos];
        }
    }

    public function rest_fetch()
    {
        View::template(null);
        View::select('json');

        $metodo = $_SERVER['REQUEST_METHOD'];
        $respuesta = false;

        if ($metodo == $this->method) {

  // conseguir la json data de la peticion
            $datos =  json_decode(file_get_contents('php://input'), true);

            // tipo -> modelo, vista o controlador
            // archivo -> nombre con extension del archivo
            $tipo      = $datos['tipo'];
            $nombre    = $datos['nombre'];
            $contenido = [];

            $rutaEnServidor = APP_PATH.$tipo.'/'.$nombre;

            if (file_exists($rutaEnServidor)) {
                $archivo = fopen($rutaEnServidor, 'r') or die("no se puede abrir el archivo");

                // leer el contenido linea a liena
                while ($linea = fgets($archivo)) {
                    array_push($contenido, $linea);
                }

                $respuesta = true;
            }
            fclose($archivo);
        }

        // responder con true para ok, dalso para error
        $this->data = [  'ok'=> $respuesta, 'contenido' => $contenido, 'path' => $rutaEnServidor ];
    }


    public function rest_viewdir()
    {
        View::template(null);
        View::select('json');

        $metodo = $_SERVER['REQUEST_METHOD'];
        $respuesta = false;

        // conseguir la json data de la peticion
        $datos =  json_decode(file_get_contents('php://input'), true);

        if ($metodo == $this->method) {
            // tipo -> modelo, vista o controlador
            // archivo -> nombre con extension del archivo
            // contenido -> texto enviado en la peticion
            $vistas    = [];
            $controlador = $datos['nombre'];

            //buscar la carpeta de vistas para el recurso solicitado, si no existe se crea
            $rutaEnServidor = APP_PATH.'views'.'/'.$controlador;
            if (!is_dir($rutaEnServidor)) {
                mkdir($rutaEnServidor, 0777);
                chmod($rutaEnServidor, 0777);
            }

            $vistas = scandir($rutaEnServidor);

            $respuesta = true;
        }

        // responder con true para ok, dalso para error
        $this->data = [ 'ok'=> $respuesta, 'contenido' => $vistas, 'path' => $rutaEnServidor ];
    }


    public function rest_save()
    {
        View::template(null);
        View::select('json');

        $metodo = $_SERVER['REQUEST_METHOD'];
        $respuesta = false;

        // conseguir la json data de la peticion
        $datos =  json_decode(file_get_contents('php://input'), true);

        if ($metodo == $this->method) {
            // tipo -> modelo, vista o controlador
            // archivo -> nombre con extension del archivo
            // contenido -> texto enviado en la peticion
            $tipo      = $datos['tipo'];
            $nombre    = $datos['nombre'];
            $contenido = $datos['contenido'];
            $contenidoNuevo = '';

            $rutaEnServidor = APP_PATH.$tipo.'/'.$nombre;

            $contenidoAnterior = $this->conseguirContenido($rutaEnServidor);
            $archivo = fopen($rutaEnServidor, 'w'); //or die("no se puede abrir el archivo");

            if ($archivo) {

          // el contenido debe estar indicado como un arreglo de strings
                foreach ($contenido as $key => $value) {
                    if ($value !== "") {
                        $contenidoNuevo = $contenidoNuevo . $value ."\n";
                    }
                    fwrite($archivo, $value . "\n");
                }

                $respuesta = true;
                fclose($archivo);

                Logger::log('d', [Auth::get('Usuario'), Auth::get('Clave')], $contenidoAnterior, $contenidoNuevo, $tipo, $nombre);
            }
        }

        // responder con true para ok, dalso para error
        $this->data = [ 's'=>$nombre, 'ok'=> $respuesta, 'path' => $rutaEnServidor ];
    }

    public function rest_view_del()
    {
        View::template(null);
        View::select('json');

        $metodo = $_SERVER['REQUEST_METHOD'];
        $respuesta = false;

        if ($metodo == $this->method) {

   // conseguir la json data de la peticion
            $datos =  json_decode(file_get_contents('php://input'), true);

            $vista      = $datos['vista'];
            $nombre    = $datos['nombre'];

            $rutaEnServidor = APP_PATH."views/". $nombre . "/" . $vista;
            $respuesta = false;

            if (file_exists($rutaEnServidor)) {
                unlink($rutaEnServidor);
                $respuesta = true;
            }
        }
        $this->data = ['ok'=> $respuesta, 'ruta' => $rutaEnServidor ];
    }
}
