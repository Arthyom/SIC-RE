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
        $this->dataTablaConf = (new $this->configuracion)->find("TablaPropietaria LIKE '$this->model'");
        //   $this->dh = (new polizasheader)->find();
        $this->tablaEsclavo = $this->dataTablaConf[0]->Esclavos;
        $this->data = (new $this->model)->paginate("page: $page");
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
            $modelObject = (new $this->model)->$pky;

            View::select('index');
            $modelo = ( new $this->model);
            $this->configuraciondata = (new $this->configuracion)->find_all_by('TablaPropietaria', $this->model);
            $sql_builder = '';
            $i = 0;

            foreach ($get_args as $key => $value) {
                if ($get_args[$key] != ''  && $this->primaryKey != $key && $value != '0') {
                    $configPersonal = (new $this->configuracion)->find_first("conditions: TablaPropietaria = '$this->model' && Name = '$key'");
                    if ($configPersonal->TablaForanea) {
                        $foreigTableMatch = (new $configPersonal->TablaForanea)->find_first("conditions: $configPersonal->CampoForaneoValor = '$value'");
                        $sql_builder = $sql_builder . "INSTR( $key , {$foreigTableMatch->$key} ) > 0  AND ";
                    } else {
                        $sql_builder = $sql_builder . "INSTR( $key , '$value' ) > 0  AND ";
                    }
                }
            }


            $len = strlen($sql_builder);
            $sql_builder = substr($sql_builder, 0, $len - 4);

            $this->objeto = (new $this->model)->count();

            if ($this->objeto < $this->maximo) {
                $this->data = (new $this->model)->find('conditions: '. $sql_builder);
            } else {
                $this->bigdata = (new $this->model)->paginate("per_page: 10", "page: 1", 'conditions: ' . $sql_builder);
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
     * Ver un Registro
     *
     * @param int $id Identificador de registro
     */
    public function ver($id)
    {
        $this->data = (new $this->model)->find_first((int) $id);
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
}
