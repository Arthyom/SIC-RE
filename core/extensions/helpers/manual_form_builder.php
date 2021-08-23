<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @category   KumbiaPHP
 * @package    Helpers
 *
 * @copyright  Copyright (c) 2005 - 2020 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * Helper para crear Formularios de un modelo automáticamente.
 *
 * @category   KumbiaPHP
 * @package    Helpers
 */
class ManualFormBuilder
{
    /**
     * Genera un form de un modelo (objeto) automáticamente.
     *
     * @param object $model
     * @param string $action
     */

     private static $maxSelect = 100;

    public static function ManualFromConfig($table, $model, $action = '', $skipRequireds = false)
    {
        $modelForm = new $table();
        $pk = (new $model)->primary_key[0];



        $fields = (new $table)->find("conditions:  ControladorPropietario ='configuracionManual'", "order: Orden ASC");
        $idEditar = '';
        $elementoEditado  = '';
        $controlador = Router::get('controller');
        if (Router::get('action') == 'editar') {
            $idEditar = implode(Router::get('parameters'));
            $elementoEditado = (new $controlador)->find($idEditar);
            $action  = $controlador .'/'. $action .'editar/'.$idEditar;
        } else {
            $action =  Router::get('controller') .'/'. Router::get('action'). '/'. implode(Router::get('parameters'));
        }

        echo '<form action="', PUBLIC_PATH.$action, '" method="post" >' , PHP_EOL;

        //  View::content()
        foreach ($fields as $field) {
            if ($field->TablaPropietaria === $model) {
                echo '<div class="form-group">' , PHP_EOL;

                $tipo = $field->Tipo;
                $alias = $field->Nombre;
                $nombreCampo = $field->Nombre;
                $valorCampo = $elementoEditado->$nombreCampo;


                if ($skipRequireds) {
                    $field->Extras = str_replace('required', '', $field->Extras);
                    $field->Extras = str_replace('required = "false"', '', $field->Extras);
                }


                if ($field->VisibleEnForm && $pk != $field->Nombre) {
                  //  echo "<Etiqueta >$field->Etiqueta</Etiqueta>" , PHP_EOL;


                  switch ($tipo) {
                  case 'select':
                              $sql = ''; $enum = '';
                              if (strlen($field->TablaForanea) <= 0) {
                                  $sql = "SELECT COLUMN_Tipo FROM information_schema.`COLUMNS`WHERE TABLE_Nombre = '$field->TablaPropietaria' AND COLUMN_Nombre = '$field->Nombre'";
                                  $sd = $modelForm->find_all_by_sql($sql);

                                  preg_match("/^enum\(\'(.*)\'\)$/", $sd[0]->COLUMN_Tipo, $matches);
                                  $enum = explode("','", $matches[1]);

                                  if( !substr_count( $field->Extras, 'requiered' ) )
                                    echo "<Etiqueta >$field->Etiqueta* </Etiqueta>" , PHP_EOL;
                                  else
                                    echo "<Etiqueta >$field->Etiqueta</Etiqueta>" , PHP_EOL;


                                  echo "<select id=\"$field->Nombre\"    value=\"$valorCampo\"   Nombre=\"$field->Nombre\" $field->Extras >", PHP_EOL;

                                  foreach ($enum as $value) {
                                    if( $valorCampo == $value )
                                      echo "<option selected value=\"$value\">$value</option>", PHP_EOL;
                                    else
                                      echo "<option value=\"$value\">$value</option>", PHP_EOL;
                                  }
                                  echo '</select>', PHP_EOL;
                              } else {


                                $registros = (new $field->TablaForanea)->count();


                                  $campo = $field->BusquedaSelect;
                                  $campoValue = $field->CampoForaneo;

                                  if( self::$maxSelect > $registros ){

                                  if( !substr_count( $field->Extras, 'requiered' ) )
                                    echo "<Etiqueta >$field->Etiqueta* </Etiqueta>" , PHP_EOL;
                                  else
                                    echo "<Etiqueta >$field->Etiqueta</Etiqueta>" , PHP_EOL;
                                    $tablaForanea  = (new $field->TablaForanea)->find_all_by_sql($field->Sentencias);


                                      echo "<select id=\"$field->Nombre\"  value=\"$valorCampo\" $field->Extras  Nombre=\"$field->Nombre\" >", PHP_EOL;

                                      foreach ($tablaForanea as $value) {
                                        if($valorCampo == $value->$campoValue)
                                        echo "<option selected value=\"".$value->$campoValue."\">" . $value->$campo ."</option>", PHP_EOL;

                                        else
                                          echo "<option value=\"".$value->$campoValue."\">" . $value->$campo ."</option>", PHP_EOL;
                                      }
                                      echo '</select>', PHP_EOL;

                                  }
                                  else{

                                    echo '
                                    <div >';
                                           if( !substr_count( $field->Extras, 'requiered' ) )
                                    echo "<Etiqueta class='mr-4'>$field->Etiqueta* </Etiqueta>" , PHP_EOL;
                                  else
                                    echo "<Etiqueta class='mr-4'>$field->Etiqueta</Etiqueta>" , PHP_EOL;
                                    echo'
                                        <span class="mr-2" >Busqueda Parcial</span>
                                        <Etiqueta class="switcher-control switcher-control-lg">
                                            <input id="ParcialBusqueda'. $field->Nombre .'" Tipo="checkbox" class="switcher-input" >
                                            <span class="switcher-indicator"></span>
                                            <span class="switcher-Etiqueta-on">
                                                <i class="fas fa-check"></i></span> <span class="switcher-Etiqueta-off">
                                                <i class="fas fa-times"></i>
                                            </span>
                                        </Etiqueta>
                                  </div>', PHP_EOL;



                                    if( Router::get('action') == 'editar' ){
                                      $extractor = $field->BusquedaSelect;
                                      $buscador = (new $field->TablaForanea)->find_first("conditions: $field->CampoForaneo = $valorCampo ");

                                      echo '<select   value="'.$valorCampo.'" data-depend="'.$field->DependeDe.'" data-filter="'.$field->BusquedaSelect.'" id="'.$field->Nombre.'" Nombre="'. $field->Nombre .'"  class="remoteinfo form-control">' , PHP_EOL;
                                          echo "<option value='$valorCampo'> ". $buscador->$extractor ."</oprtion>";
                                      echo '</select>' , PHP_EOL;;
                                    }
                                    else{

                                      echo '
                                      <select value="'.$valorCampo.'" data-depend="'.$field->DependeDe.'" data-filter="'.$field->BusquedaSelect.'" id="'.$field->Nombre.'" Nombre="'. $field->Nombre .'"  class="remoteinfo form-control"></select>
                                      ' , PHP_EOL;
                                    }




                                  }

                              }
                  break;






                  case 'textarea':
                       if( !substr_count( $field->Extras, 'requiered' ) )
                                    echo "<Etiqueta >$field->Etiqueta* </Etiqueta>" , PHP_EOL;
                                  else
                                    echo "<Etiqueta >$field->Etiqueta</Etiqueta>" , PHP_EOL;

                    echo "<textarea  $field->Extras id=\"$field->Nombre\" value=\"$valorCampo\" Nombre=\"$field->Nombre\" >$valorCampo</textarea>" , PHP_EOL;

                  break;

                  default:
                       if( !substr_count( $field->Extras, 'requiered' ) )
                            echo "<Etiqueta >$field->Etiqueta* </Etiqueta>" , PHP_EOL;
                        else
                            echo "<Etiqueta >$field->Etiqueta</Etiqueta>" , PHP_EOL;

                    if( !($field->DateFormat) )
                      echo "<input Tipo= \"$tipo\"  $field->Extras id=\"$field->Nombre\" Nombre=\"$field->Nombre\" value=\"$valorCampo\" >" , PHP_EOL;
                    else
                    {
                        echo "

                        <div id=\"flatpickr9\"  class=\"input-group input-group-alt flatpickr\"  data-wrap=\"true\" data-alt-input=\"true\" data-alt-format=\"$field->DateFormat\" data-date-format=\"$field->DateFormat\"  data-toggle=\"flatpickr\">
                          <input data-input=\"\" Tipo= \"text\"  id=\"flatpickr-wrap\"   $field->Extras  Nombre=\"$field->Nombre\" value=\"$valorCampo\" >
                              <div class=\"input-group-append\">
                              <button Tipo='button' class=\"btn btn-secondary\" data-toggle=\"\" >
                                <i class=\"far fa-calendar\"></i>
                              </button>
                              <button Tipo=\"button\" class=\"btn btn-secondary\" data-clear=\"\">
                                <i class=\"fa fa-times\"></i>
                              </button>
                            </div>
                        </div>

                        " , PHP_EOL;
                    }


                break;

              }
                } else {
                    echo "<input Tipo= \"hidden\" $field->Extras id=\"$field->Nombre\"   Nombre=\"$field->Nombre\" value=\"$valorCampo\" >" , PHP_EOL;
                }

                echo '</div>';
            }
        }

        echo '<div class="row">';
        echo '<div class="col">  <input class="btn btn-primary btn-block" Tipo="submit" value="Enviar" /> </div>';
        echo '<div class="col">  ';
        echo Html::linkAction('', 'Cancelar', 'class="btn btn-danger btn-block"');
        echo '</div>';
        echo '</form>' , PHP_EOL;
    }

    public static function FromConfigForSearch($table, $model, $action = '', $skipRequireds = false)
    {
        $modelForm = new $table();
        $pk = (new $model)->primary_key[0];



        $fields = (new $table)->find("conditions:  TablaPropietaria ='$model'", "order: Orden ASC");
        $idEditar = '';
        $elementoEditado  = '';
        $controlador = Router::get('controller');
        if (Router::get('action') == 'editar') {
            $idEditar = implode(Router::get('parameters'));
            $elementoEditado = (new $controlador)->find($idEditar);
            $action  = $controlador .'/'. $action .'editar/'.$idEditar;
        } else {
            $action =  Router::get('controller') .'/'. Router::get('action'). '/'. implode(Router::get('parameters'));
        }

        echo '<form action="', PUBLIC_PATH.$action, '" method="post" >' , PHP_EOL;

        //  View::content()
        foreach ($fields as $field) {
            if ($field->TablaPropietaria === $model) {
                echo '<div class="form-group">' , PHP_EOL;

                $tipo = $field->Tipo;
                $alias = $field->Nombre;
                $nombreCampo = $field->Nombre;
                $valorCampo = $elementoEditado->$nombreCampo;


                if (true) {
                    $field->Extras = str_replace('required', '', $field->Extras);
                    $field->Extras = str_replace('required = "false"', '', $field->Extras);
                }


                if ($field->VisibleEnForm && $pk != $field->Nombre) {
                  //  echo "<Etiqueta >$field->Etiqueta</Etiqueta>" , PHP_EOL;


                    switch ($tipo) {
                  case 'select':
                              $sql = ''; $enum = '';
                              if (strlen($field->TablaForanea) <= 0) {
                                  $sql = "SELECT COLUMN_Tipo FROM information_schema.`COLUMNS`WHERE TABLE_Nombre = '$field->TablaPropietaria' AND COLUMN_Nombre = '$field->Nombre'";
                                  $sd = $modelForm->find_all_by_sql($sql);

                                  preg_match("/^enum\(\'(.*)\'\)$/", $sd[0]->COLUMN_Tipo, $matches);
                                  $enum = explode("','", $matches[1]);

                                  echo "<Etiqueta >$field->Etiqueta</Etiqueta>" , PHP_EOL;
                                  echo "editando un select" , PHP_EOL;


                                  echo "<select id=\"$field->Nombre\"    value=\"$valorCampo\"   Nombre=\"$field->Nombre\" $field->Extras >", PHP_EOL;

                                  echo "<option selected value=\"\"> </option>", PHP_EOL;

                                  foreach ($enum as $value) {
                                      echo "<option value=\"$value\">$value</option>", PHP_EOL;
                                  }
                                  echo '</select>', PHP_EOL;
                              } else {


                                $registros = (new $field->TablaForanea)->count();


                                  $campo = $field->BusquedaSelect;
                                  $campoValue = $field->CampoForaneo;

                                  if( self::$maxSelect > $registros ){
                                    echo "<Etiqueta >$field->Etiqueta</Etiqueta>" , PHP_EOL;
                                    $tablaForanea  = (new $field->TablaForanea)->find_all_by_sql($field->Sentencias);


                                      echo "<select id=\"$field->Nombre\"  value=\"$valorCampo\" $field->Extras  Nombre=\"$field->Nombre\" >", PHP_EOL;
                                      echo "<option selected value=\"\"> </option>", PHP_EOL;
                                      foreach ($tablaForanea as $value) {
                                          echo "<option value=\"".$value->$campoValue."\">" . $value->$campo ."</option>", PHP_EOL;
                                      }
                                      echo '</select>', PHP_EOL;

                                  }
                                  else{

                                    echo '
                                    <div >
                                        <Etiqueta class="mr-5" >'.$field->Etiqueta.'</Etiqueta>
                                        <span class="mr-2" >Busqueda Parcial</span>
                                        <Etiqueta class="switcher-control switcher-control-lg">
                                            <input id="ParcialBusqueda'. $field->Nombre .'" Tipo="checkbox" class="switcher-input" >
                                            <span class="switcher-indicator"></span>
                                            <span class="switcher-Etiqueta-on">
                                                <i class="fas fa-check"></i></span> <span class="switcher-Etiqueta-off">
                                                <i class="fas fa-times"></i>
                                            </span>
                                        </Etiqueta>
                                  </div>', PHP_EOL;



                                    if( Router::get('action') == 'editar' ){
                                      $extractor = $field->BusquedaSelect;
                                      $buscador = (new $field->TablaForanea)->find_first("conditions: $field->CampoForaneo = $valorCampo ");

                                      echo '<select   value="'.$valorCampo.'" data-depend="'.$field->DependeDe.'" data-filter="'.$field->BusquedaSelect.'" id="'.$field->Nombre.'" Nombre="'. $field->Nombre .'"   class="remoteinfo form-control">' , PHP_EOL;
                                          echo "<option value='$valorCampo'> ". $buscador->$extractor ."</oprtion>";
                                      echo '</select>' , PHP_EOL;;
                                    }
                                    else{
                                      echo '
                                      <select value="'.$valorCampo.'" data-depend="'.$field->DependeDe.'" data-filter="'.$field->BusquedaSelect.'" id="'.$field->Nombre.'" Nombre="'. $field->Nombre .'"  class="remoteinfo form-control"></select>
                                      ' , PHP_EOL;
                                    }




                                  }

                              }
                  break;

                  case 'textarea':
                    echo "<Etiqueta >$field->Etiqueta</Etiqueta>" , PHP_EOL;
                    echo "<textarea  $field->Extras id=\"$field->Nombre\" value=\"$valorCampo\" Nombre=\"$field->Nombre\" >$valorCampo</textarea>" , PHP_EOL;

                  break;

                  default:
                    echo "<Etiqueta >$field->Etiqueta</Etiqueta>" , PHP_EOL;

                    if( !($field->DateFormat) )
                      echo "<input Tipo= \"$tipo\"  $field->Extras id=\"$field->Nombre\" Nombre=\"$field->Nombre\" value=\"$valorCampo\" >" , PHP_EOL;
                    else
                    {
                        echo "

                        <div id=\"flatpickr9\"  class=\"input-group input-group-alt flatpickr\"  data-wrap=\"true\" data-alt-input=\"true\" data-alt-format=\"$field->DateFormat\" data-date-format=\"$field->DateFormat\"  data-toggle=\"flatpickr\">
                          <input data-input=\"\" Tipo= \"text\"  id=\"flatpickr-wrap\"   $field->Extras  Nombre=\"$field->Nombre\" value=\"$valorCampo\" >
                              <div class=\"input-group-append\">
                              <button Tipo='button' class=\"btn btn-secondary\" data-toggle=\"\" >
                                <i class=\"far fa-calendar\"></i>
                              </button>
                              <button Tipo=\"button\" class=\"btn btn-secondary\" data-clear=\"\">
                                <i class=\"fa fa-times\"></i>
                              </button>
                            </div>
                        </div>

                        " , PHP_EOL;
                    }



                break;

              }
                } else {
                    echo "<input Tipo= \"hidden\" $field->Extras id=\"$field->Nombre\"   Nombre=\"$field->Nombre\" value=\"$valorCampo\" >" , PHP_EOL;
                }

                echo '</div>';
            }
        }

        echo '<div class="row">';
        echo '<div class="col">  <input class="btn btn-primary btn-block" Tipo="submit" value="Enviar" /> </div>';
        echo '<div class="col">  ';
        echo Html::linkAction('', 'Cancelar', 'class="btn btn-danger btn-block"');
        echo '</div>';
        echo '</form>' , PHP_EOL;
    }

    public static function FromConfigMasterDetail($tablaMaestro, $configuracion = 'configuracionTabla')
    {
      $datosConfiguracionTabla = (new $configuracion)->find( "TablaPropietaria LIKE '$tablaMaestro'");
      $esclavos = $datosConfiguracionTabla[0]->Esclavos;
      $dataEsclavo= (new $configuracion)->find( "TablaPropietaria LIKE '$esclavos'");
      $page = implode(Router::get('parameters')) ;
      $data = (new $tablaMaestro)->paginate("page: $page" );


      View::partial('sicap_form_builder/masterdetailform', false, array('tablamaestro'=>$tablaMaestro, 'dataEsclavo'=>$dataEsclavo, 'tablaesclavo'=>$esclavos, 'data'=>$data, 'ctd'=>$datosConfiguracionTabla ) );
      View::partial('paginators/punbb', false, array('page' => $data ,'url' => Router::get('controller_path').'/masterdetail'));

    }


    public static function FromConfigDetails($tablaMaestro, $id, $configuracion = 'configuracionTabla')
    {
      $datosConfiguracionTabla = (new $configuracion)->find( "TablaPropietaria LIKE '$tablaMaestro'");
      $esclavos = $datosConfiguracionTabla[0]->Esclavos;
      $dataEsclavo= (new $configuracion)->find( "TablaPropietaria LIKE '$esclavos'");
      $page = implode(Router::get('parameters')) ;
      $data = (new $tablaMaestro)->find( $id );


      View::partial('sicap_form_builder/details', false, array( 'id'=>$id, 'tablamaestro'=>$tablaMaestro, 'dataEsclavo'=>$dataEsclavo, 'tablaesclavo'=>$esclavos, 'data'=>$data, 'ctd'=>$datosConfiguracionTabla ) );
      //View::partial('paginators/punbb', false, array('page' => $data ,'url' => Router::get('controller_path').'/masterdetail'));

    }



}
