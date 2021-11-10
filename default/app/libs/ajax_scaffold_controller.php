<?php

/**
 * Controlador base para la construcción de CRUD para modelos rápidamente
 *
 * @category Kumbia
 * @package Controller
 */
abstract class AjaxScaffoldController extends ScaffoldController
{
    public function rest_foreingKeyInfo($keyName, $fieldName)
    {


      // conseguir los campos con id del modelo indicado

        try {
            View::template(null);
            View::select('json');

            $metodo = $_SERVER['REQUEST_METHOD'];
            $respuesta = false;

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
                        $coincidenciasId     = (new $camposSelect->TablaForanea)->find("conditions: $keyName = $id AND $dependeDe = $dependeInfo");
                    }

                    foreach ($coincidenciasId as $i => $cid) {
                        $cidfix = [ 'id' => $cid->$keyName, 'text' => $cid->Nombre ];

                        array_push($matches, $cidfix);
                    }
                }


                $coinTexto = [];
                if (!is_numeric($texto)) {
                    if (!$dependeDe) {
                        $coinTexto = (new  $camposSelect->TablaForanea)->find("conditions: $criteria ");
                    } else {
                        $coinTexto = (new  $camposSelect->TablaForanea)->find("conditions: $criteria AND $dependeDe = $dependeInfo");
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


            $this->data = [ 'pk'=>"conditions: Name LIKE '$keyName' AND  Type='select' AND  TablaPropietaria ='$this->controller_name'", 'cs'=>$coinTexto, 'c'=>$criteria, 'items'=> $matches , 'dependent'=>[$dependeDe, $dependeInfo]];
        } catch (\Exception $e) {
            $this->data = ['err OOO'=>$e->getMessage()];
        }
    }
}
