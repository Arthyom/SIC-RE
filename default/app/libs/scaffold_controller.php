<?php

/**
 * Controlador base para la construcción de CRUD para modelos rápidamente
 *
 * @category Kumbia
 * @package Controller
 */
abstract class ScaffoldController extends AdminController
{
    /** @var string Carpeta en views/_shared/scaffolds/ */
    public $scaffold = '';
    /** @var string Nombre del modelo en CamelCase */
    public $model = '';

    public $configuracion = '';

    public $pk = '';

    public $maximo = 3000;

    private $method = 'POST';

    public function masterdetail($page = 1)
    {
        $this->ctds = (new $this->configuracion)->find( "TablaPropietaria LIKE '$this->model'");
     //   $this->dh = (new polizasheader)->find();
        $this->data = (new $this->model)->paginate("page: $page" );
    }

    public function rest_foreingKeyInfo( $keyName, $fieldName )
    {
        // conseguir los campos con id del modelo indicado
        View::template(null);
        View::select('json');

        $metodo = $_SERVER['REQUEST_METHOD'];
        $respuesta = false;

        if (true) {
 
            $datos =  json_decode(file_get_contents('php://input'), true);
            $id = $datos['q'];
            $texto = explode( ',', $datos['texto'] );
            $dependeDe = $datos['dependeDe'];
            $dependeInfo = $datos['dependeInfo'];
            $parcial = $datos['ParcialBusqueda'];
            $dataFilter = explode( ',', $datos['dataFilter'] );
            $criteria = '';
            $matches = [];

            //crear una cadena de busqueda
            foreach ($dataFilter as $i => $filter) {
                
                    if (!$parcial) {
                        if( $texto[$i] )
                            $criteria .= " $filter  LIKE  '$texto[$i]'";
                    }
                    else{
                        if( $texto[$i])
                            $criteria .= " INSTR( $filter, '$texto[$i]') = 1";
                    }
                    if( $texto[$i+1]  )
                        $criteria .= " AND ";
               
            }
            
            
            $camposSelect  = (new $this->configuracion)->find_first( "conditions: Name LIKE '$keyName' AND  Type='select' AND  TablaPropietaria ='$this->controller_name'");


            if( is_numeric( $id ) ){
                if( !$dependeDe )
                    $coincidenciasId     = (new $camposSelect->TablaForanea)->find("conditions: $keyName = $id");
                else
                    $coincidenciasId     = (new $camposSelect->TablaForanea)->find("conditions: $keyName = $id AND $dependeDe = $dependeInfo");

                foreach ($coincidenciasId as $i => $cid) {
                    $cidfix = [ 'id' => $cid->$keyName, 'text' => $cid->Nombre ];
                    array_push( $matches, $cidfix );
                }
            }
            

            $coinTexto = [];
            if( !is_numeric($texto) ){
              if( !$dependeDe )
                $coinTexto = (new  $camposSelect->TablaForanea)->find("conditions: $criteria ");
              else
                $coinTexto = (new  $camposSelect->TablaForanea)->find("conditions: $criteria AND $dependeDe = $dependeInfo");
        
                foreach ($coinTexto as $j => $cit) {
                    $filterField = '';
                    foreach ($dataFilter as $k => $filter) 
                        $filterField .= $cit->$filter . ' ';
                    
                    
                    $citfix = ['id' => $cit->$keyName, 'text'=> $filterField ];
                    array_push( $matches, $citfix );
                }
            }



            //" INSTR( Nombre, 'javier') = 1  AND  INSTR( ApellidoPaterno, '') = 1  AND  INSTR( ApellidoMaterno, '') = 1 "
        }

        $this->data = [ 'cs'=>$coinTexto, 'c'=>$criteria, 'items'=> $matches , 'dependent'=>[$dependeDe, $dependeInfo]];

    }


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
            $camposSelect  = (new $this->configuracion)->find( "conditions: length(TablaForanea) > 0 AND  Type='select' AND  TablaPropietaria ='$this->controller_name'" );
            $datosAjustados = [];

            foreach ($camposSelect as $i => $campo) {
                $datoAjustado = [ 'id'=> $i, 'text'=> $campo->Name ];
                array_push( $datosAjustados, $datoAjustado );
            }
        }
        $this->data = [ 'items'=> $datosAjustados ];

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

    /**
     * volver el archivo indicado a la fecha especificada
     */
    public function rest_back()
    {
       View::template(null);
       View::select('json');

       $metodo = $_SERVER['REQUEST_METHOD'];
       $respuesta = false;

       if( $metodo == $this->method ){

        // conseguir la json data de la peticion
        $datos =  json_decode(file_get_contents('php://input'), true);

        // tipo -> modelo, vista o controlador
        // archivo -> nombre con extension del archivo
        $contenido = [];
        $tipo      = $datos['tipo'];
        $nombre    = $datos['nombre'];
        $fecha     = $datos['fecha'];
        $fechaDir  = date( 'j-n-Y' , strtotime($fecha) );
        $utc       = date( 'c' , strtotime($fecha) );
        $usuario = 'SISTEMA'; //Auth::get('Usuario');
        $nombre = str_replace( [ '_controller.php', '.php', '.phtml'], '', $nombre );
        $validos = [];
        $rutaEnServidor = APP_PATH."/temp/logs". "/" . $fechaDir . "/". "codificar/" . $usuario;
        $respuesta = false;
        $archivos = scandir($rutaEnServidor);


        foreach ($archivos as $i => $archivo) {
            if(  strpos($archivo, $tipo)   ){
                if( $archivo[0] == 'A'){
                   if( strpos($archivo, $utc) ){
                        $respuesta = true;
                        array_push( $validos, $archivo);
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
            if( 0 < $i )
                array_push( $contenido, $linea );
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

        if( $metodo == $this->method ){

         // conseguir la json data de la peticion
         $datos =  json_decode(file_get_contents('php://input'), true);

         // tipo -> modelo, vista o controlador
         // archivo -> nombre con extension del archivo
         $tipo       = $datos['tipo'];
         $nombre     = $datos['nombre'];
         $fecha      = $datos['fecha'];
        $usuario = Auth::get('Usuario');
        $nombre = str_replace( [ '_controller.php', '.php', '.phtml'], '', $nombre );
        $validos = [];
        $rutaEnServidor = APP_PATH."/temp/logs". "/" . $fecha . "/". "codificar/" . $usuario;
        $archivos = scandir($rutaEnServidor);


        foreach ($archivos as $i => $archivo) {
            if(  strpos($archivo, $tipo)   ){
                if( $archivo[0] == 'A'){
                    $segmentosNombre = explode('_', $archivo);
                    $fechaCreacion = str_replace('.txt', '', $segmentosNombre[3]);
                    $fechaParaHumano = date( 'd/m/Y G:i:s' , strtotime($fechaCreacion) );
                    array_push($validos, $fechaParaHumano );
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

       if( $metodo == $this->method ){

        // conseguir la json data de la peticion
        $datos =  json_decode(file_get_contents('php://input'), true);

        // tipo -> modelo, vista o controlador
        // archivo -> nombre con extension del archivo
        $tipo      = $datos['tipo'];
        $nombre    = $datos['nombre'];
        $contenido = [];

        $rutaEnServidor = APP_PATH.$tipo.'/'.$nombre;

        if( file_exists($rutaEnServidor)  ){
            $archivo = fopen($rutaEnServidor, 'r') or die("no se puede abrir el archivo");

            // leer el contenido linea a liena
            while ($linea = fgets($archivo))
                array_push( $contenido, $linea );

            $respuesta = true;
        }
        fclose($archivo);
        }

        // responder con true para ok, dalso para error
        $this->data = [  'ok'=> $respuesta, 'contenido' => $contenido, 'path' => $rutaEnServidor ];
    }


    public function rest_viewdir(){

        View::template(null);
        View::select('json');

        $metodo = $_SERVER['REQUEST_METHOD'];
        $respuesta = false;

       // conseguir la json data de la peticion
       $datos =  json_decode(file_get_contents('php://input'), true);

       if( $metodo == $this->method ){
            // tipo -> modelo, vista o controlador
            // archivo -> nombre con extension del archivo
            // contenido -> texto enviado en la peticion
            $vistas    = [];
            $controlador = $datos['nombre'];

            //buscar la carpeta de vistas para el recurso solicitado, si no existe se crea
            $rutaEnServidor = APP_PATH.'views'.'/'.$controlador;
            if( !is_dir($rutaEnServidor) ){
                mkdir($rutaEnServidor, 0777);
                chmod($rutaEnServidor,0777);
            }

            $vistas = scandir( $rutaEnServidor );

           $respuesta = true;
        }

        // responder con true para ok, dalso para error
        $this->data = [ 'ok'=> $respuesta, 'contenido' => $vistas, 'path' => $rutaEnServidor ];
    }


     public function rest_save(){

        View::template(null);
        View::select('json');

        $metodo = $_SERVER['REQUEST_METHOD'];
        $respuesta = false;

       // conseguir la json data de la peticion
       $datos =  json_decode(file_get_contents('php://input'), true);

       if( $metodo == $this->method ){
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

                if( $archivo ){

                // el contenido debe estar indicado como un arreglo de strings
                foreach ($contenido as $key => $value) {
                    if( $value !== ""  )
                    $contenidoNuevo = $contenidoNuevo . $value ."\n";
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

     public function conseguirContenido($rutaEnServidor)
     {
        $contenido = '';
        $archivo = fopen($rutaEnServidor, 'r');

         // leer el contenido linea a liena
         while ($linea = fgets($archivo))
            if( strlen($linea) > 3 )
                $contenido = $contenido . $linea;

        fclose($archivo);

        return $contenido;
     }

     public function pdf($alcance='externo')
     {

        View::template('pdf');
        $this->data = (new $this->model)->find('order: id desc');

        switch ($alcance) {
            case 'interno':
                View::select('interno');
            break;
        }
     }


     public function buscarpor( $campo,  $coinicidencia)
    {

        $this->cb =  Input::hasPost('campo');
        $this->c = Input::post('campo');

        $this->data = (new $this->model)->find_all_by($campo, $coinicidencia);
    }

    public function json($page = 1)
    {

        View::template(NULL);
        View::response('json');
        $this->primaryKey = (new $this->model)->primary_key[0];
        $this->data = (new $this->model)->paginate("page: $page", "per_page: 5", "order: $this->primaryKey DESC");
       // $this->all = (new $this->model)->find();

        $this->json = [
            "draw" => 1,
            "recordsTotal" => count($this->data->items),
            "recordsFiltered" => count($this->data->items),
            "data" => $this->data->items
        ];
    }

    public function filtrar( $args='' )
    {
        $get_args = $_POST;
         //View::template(null);
         //View::select(null);

        if( count($get_args) > 0 ){
            $pky = (new $this->model)->primary_key[0];

            View::select('index');
            $modelo = ( new $this->model);
            $this->configuraciondata = (new $this->configuracion)->find_all_by('TablaPropietaria', $this->model);
            $sql_builder = '';
            $i = 0;

            foreach ($get_args as $key => $value)
                if( $get_args[$key] != ''  && $this->primaryKey != $key )
                    $sql_builder = $sql_builder . "INSTR( $key , '$value' ) > 0  AND ";

            $len = strlen($sql_builder);
            $sql_builder = substr( $sql_builder, 0, $len - 4 );

            if( $len > 0 )
                $this->data = $modelo->find_all_by_sql ( 'SELECT * FROM ' . $this->model . ' WHERE '. $sql_builder );

        }

    }

    public function codificar(Type $var = null)
    {

    }

    public function index($page = 1)
    {

        //View::temaplate(null);
        //View::select(null);

        $this->primaryKey = (new $this->model)->primary_key[0];
        $this->configuraciondata = (new $this->configuracion)->find("conditions:  TablaPropietaria ='$this->model'", "order: Orden");
        $this->objeto = (new $this->model)->count();

        if( $this->objeto < $this->maximo )
            $this->data = (new $this->model)->find( "order: $this->primaryKey DESC");
        else
            $this->bigdata = (new $this->model)->paginate( "per_page: 10", "page: $page" , "order: $this->primaryKey DESC");


    }


    /**
     * Crea un Registro
     */
    public function crear()
    {
      $vals = (new $this->configuracion)->find_all_by('TablaPropietaria', $this->model);
      $arr = [];
      foreach ($vals as $va) {
          $arr[$va->Name] = Input::post($va->Name);
      }

      $obj = new $this->model;

      //En caso que falle la operación de guardar
      if ( !$obj->save($arr) ) {
        //  Flash::error('Falló Operación' . var_dump($arr));
          //se hacen persistente los datos en el formulario
          //$this->{$this->model} = $obj;

         // Redirect::to('err');
          return;
      }
  //    Flash::info('la operacion tuvoexito' . var_dump($arr));

      return Redirect::to('');
    }

    /**
     * Edita un Registro
     *
     * @param int $id  Idendificador del registro
     */
    public function editar($id)
    {
      View::select('crear');

      $vals = (new $this->configuracion)->find_all_by('TablaPropietaria', $this->model);
      $arr = [];
      foreach ($vals as $va) {
        //  echo 'valor '. $va->TablaPropietaria === $this->model;
          if( $va->tablaPropietaria === $this->model )
              $arr[$va->Name] = Input::post($va->Name);
      }

      //se verifica si se ha enviado via POST los datos
      if (true) {
          $obj = new $this->model;
          if (!$obj->update(Input::post($arr))) {
              //Flash::error('Falló Operación ' . var_dump($arr) );
              //se hacen persistente los datos en el formulario
              $this->{$this->model} = Input::post($this->model);
          } else {
              return Redirect::to();
          }
      }
    }

    /**
     * Borra un Registro
     *
     * @param int $id Identificador de registro
     */
    public function borrar($id)
    {
        if (!(new $this->model)->delete((int) $id)) {
            Flash::error('Falló Operación');
        }
        //enrutando al index para listar los articulos
        Redirect::to();
    }

    /**
     * Ver un Registro
     *
     * @param int $id Identificador de registro
     */
    public function ver($id)
    {
        $this->data = (new $this->model)->find_first((int) $id);
    }
}
