<?php

/**
 * Controlador base para la construcción de CRUD para modelos rápidamente
 *
 * @category Kumbia
 * @package Controller
 */
abstract class ScaffoldController extends ScaffoldFileController
{
    /** @var string Carpeta en views/_shared/scaffolds/ */
    public $scaffold = '';
    /** @var string Nombre del modelo en CamelCase */
    public $model = '';

    public $configuracion = '';

    public $pk = '';

    public $maximo = 3000;

    private $method = 'POST';






    /********************************* */
 
    public function rest_foreingKeyInfo($keyName, $fieldName)
    {


      // conseguir los campos con id del modelo indicado

        try {
            View::template(null);
            View::select('json');

            $metodo = $_SERVER['REQUEST_METHOD'];
            $respuesta = false;
            $s = '';

            if (true) {
                $datos =  json_decode(file_get_contents('php://input'), true);
                $id = $datos['q'];
                $texto = explode(',', $datos['texto']);
                $dependeDe = $datos['dependeDe'];
                $dependeInfo = $datos['dependeInfo'];
                $parcial = $datos['ParcialBusqueda'];
                $dataFilter = explode(',', $datos['dataFilter']);
                $criteria = '';
                $matches = [];
                $limitar = $datos['limitado'];

                //crear una cadena de busqueda
                foreach ($dataFilter as $i => $filter) {
                    if (!$parcial) {
                        if ($texto[$i]) {
                            $criteria .= " $filter  LIKE  '$texto[$i]'";
                        }
                    } else {
                        if ($texto[$i]) {
                            $criteria .= " INSTR( $filter, '$texto[$i]') = 1";
                        }
                    }
                    if ($texto[$i+1]) {
                        $criteria .= " AND ";
                    }
                }


                $camposSelect  = (new $this->configuracion)->find_first("conditions: Name LIKE '$keyName' AND  Type='select' AND  TablaPropietaria ='$this->controller_name'");

                if (!$camposSelect) {
                    $camposSelect = (new $this->configuracion)->find_first("conditions: CampoForaneoValor LIKE '$keyName' AND  Type='select' AND  TablaPropietaria ='$this->controller_name'");
                }

                if (is_numeric($id)) {
                    if (!$dependeDe) {
                        $coincidenciasId     = (new $camposSelect->TablaForanea)->find("conditions: $keyName = $id");
                    } else {
                        $coincidenciasId     = (new $camposSelect->TablaForanea)->find("conditions: $keyName = $id AND $dependeDe = $dependeInfo ");
                    }

                    foreach ($coincidenciasId as $i => $cid) {
                        $cidfix = [ 'id' => $cid->$keyName, 'text' => $cid->Nombre ];

                        array_push($matches, $cidfix);
                    }
                }


                $coinTexto = [];
                if (!is_numeric($texto)) {
                    if (!$dependeDe) {
                        if(!$limitar)
                            $coinTexto = (new  $camposSelect->TablaForanea)->find("conditions: $criteria ");
                        else{

                            $s = "inner join {$camposSelect->TablaPropietaria} on {$camposSelect->TablaPropietaria}.{$camposSelect->CampoForaneoValor} = {$camposSelect->TablaForanea}.{$camposSelect->CampoForaneoValor}";
                            $coinTexto = (new  $camposSelect->TablaForanea)->find("conditions: $criteria ", "columns: *", "join: $s");
                        }

                          
                    } else {
                        if( !$limitar  )
                            $coinTexto = (new  $camposSelect->TablaForanea)->find("conditions: $criteria AND $dependeDe = $dependeInfo");
                        else{
                            $s = //"inner join $TablaForanea on {$camposSelect->TablaPropietaria}.{$camposSelect->CampoForaneoValor} = {$camposSelect->TablaForanea}.{$campoSelect->CampoForaneoValor}";

                            $coinTexto = (new  $camposSelect->TablaForanea)->find("conditions: $criteria AND $dependeDe = $dependeInfo", 
                            "join: $s");
                        }

                    }

                    foreach ($coinTexto as $j => $cit) {
                        $filterField = '';
                        foreach ($dataFilter as $k => $filter) {
                            $filterField .= $cit->$filter . ' ';
                        }


                        $citfix = ['id' => $cit->$keyName, 'text'=> $filterField ];
                        array_push($matches, $citfix);
                    }
                }



                //" INSTR( Nombre, 'javier') = 1  AND  INSTR( ApellidoPaterno, '') = 1  AND  INSTR( ApellidoMaterno, '') = 1 "
            }


            array_push($matches, ['id' =>'0' , 'text'=>'Ninguno' ]);


            $this->data = [ 'sqs'=> $s, 'l'=>$limitar, 'pk'=>"conditions: Name LIKE '$keyName' AND  Type='select' AND  TablaPropietaria ='$this->controller_name'", 'cs'=>$coinTexto, 'c'=>$criteria, 'items'=> $matches , 'dependent'=>[$dependeDe, $dependeInfo]];
        } catch (\Exception $e) {
            $this->data = ['err OOO'=>$e->getMessage()];
        }
    }

    /********************************* */


 
 


    public function masterdetail($page = 1)
    {
        //View::select(null);
        //View::template(null);
        $objetoMaestro = (new $this->model);
        $this->dataTablaConf = (new $this->configuracion)->find("TablaPropietaria LIKE '$this->model'");
        //   $this->dh = (new polizasheader)->find();
        $this->tablaEsclavo = $this->dataTablaConf[0]->Esclavos;
        $this->dataEsclavo = (new $this->configuracion)->find("TablaPropietaria LIKE '{$this->tablaEsclavo}'");
        //echo var_dump($this->tablaEsclavo);
        $this->data = $objetoMaestro->paginate("page: $page");
    }


    public function conseguirContenido($rutaEnServidor)
    {
        $contenido = '';
        $archivo = fopen($rutaEnServidor, 'r');

        // leer el contenido linea a liena
        while ($linea = fgets($archivo)) {
            if (strlen($linea) > 3) {
                $contenido = $contenido . $linea;
            }
        }

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


    public function buscarpor($campo, $coinicidencia)
    {
        $this->cb =  Input::hasPost('campo');
        $this->c = Input::post('campo');

        $this->data = (new $this->model)->find_all_by($campo, $coinicidencia);
    }



    public function filtrar($args='')
    {
        $get_args = $_POST;
        //View::template(null);
        //View::select(null);

        if (count($get_args) > 0) {
            $pky = (new $this->model)->primary_key[0];

            View::select('index');
            $modelo = ( new $this->model);
            $this->configuraciondata = (new $this->configuracion)->find_all_by('TablaPropietaria', $this->model);
            $sql_builder = '';
            $i = 0;

            foreach ($get_args as $key => $value) {
                if ($get_args[$key] != ''  && $this->primaryKey != $key) {
                    $sql_builder = $sql_builder . "INSTR( $key , '$value' ) > 0  AND ";
                }
            }

            $len = strlen($sql_builder);
            $sql_builder = substr($sql_builder, 0, $len - 4);

            if ($len > 0) {
                $this->data = $modelo->find_all_by_sql('SELECT * FROM ' . $this->model . ' WHERE '. $sql_builder);
            }
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

        if ($this->objeto < $this->maximo) {
            $this->data = (new $this->model)->find("order: $this->primaryKey DESC");
        } else {
            $this->bigdata = (new $this->model)->paginate("per_page: 10", "page: $page", "order: $this->primaryKey DESC");
        }
    }


    /**
     * Crea un Registro
     */
    public function crear()
    {
        try {
            $vals = (new $this->configuracion)->find_all_by('TablaPropietaria', $this->model);
            $arr = [];
            foreach ($vals as $va) {
                $arr[$va->Name] = Input::post($va->Name);
            }

            $obj = new $this->model;

            if (Input::hasPost($arr)) {
                //En caso que falle la operación de guardar
                if (!$obj->create($arr)) {
                    //    Flash::error($th->getError());
                    //se hacen persistente los datos en el formulario
                    //$this->{$this->model} = $obj;

                    // Redirect::to('err');
                    return;
                } else {
                    Flash::info('Operacion exitosa', 'success', 'Correcto');

                    Redirect::to("{$this->controller_name}");
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            Flash::error($th->getMessage());
        }
    }

    /**
     * Edita un Registro
     *
     * @param int $id  Idendificador del registro
     */
    public function editar($id)
    {
        try {
            //View::select('crear');

            $vals = (new $this->configuracion)->find_all_by('TablaPropietaria', $this->model);
            $arr = [];
            foreach ($vals as $va) {
                //  echo 'valor '. $va->TablaPropietaria === $this->model;
                $arr[$va->Name] = Input::post($va->Name);
            }

            //se verifica si se ha enviado via POST los datos
            if (Input::hasPost($arr)) {
                $obj = new $this->model;
                $primary_key = $obj->primary_key[0] ;


                $obj->$primary_key = $id;

                if (!$obj->update(Input::post($arr))) {
                    //Flash::error('Falló Operación ' . var_dump($arr) );
                    //se hacen persistente los datos en el formulario
                    // Flash::error( );
                    $this->{$this->model} = Input::post($this->model);
                } else {
                    Flash::info('Operacion exitosa', 'success', 'Correcto');

                    Redirect::to();
//          Flash::info('Correcto');
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            Flash::error($th->getMessage());
        }
    }

    /**
     * Borra un Registro
     *
     * @param int $id Identificador de registro
     */
    public function borrar($id)
    {
        try {
            //code...
            if (!(new $this->model)->delete((int) $id)) {
                Flash::error('Falló Operación');
            }
            //enrutando al index para listar los articulos
            Flash::info('Operacion exitosa', 'success', 'Correcto');
            Redirect::to();
        } catch (\Throwable $th) {
            //throw $th;
            Flash::error($th->getMessage());
        }
        //enrutando al index para listar los articulos
        Redirect::to();
    }

    /**
     * Maestro-Detalle de un Registro (elemento que necesita mejorarse)
     *
     * @param int $id Identificador de registro
     */
    public function details($id)
    {
        $this->data = (new $this->model)->find_first((int) $id);
    }

    /**
     * Ver
     * 
     * @param int $id Identificador de registro
     */
    public function ver($id)
    {
        # code...
        $this->data = (new $this->model)->find_first((int) $id);
    }
}