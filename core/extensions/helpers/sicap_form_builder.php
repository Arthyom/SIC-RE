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
class SicapFormBuilder
{
    /**
     * Genera un form de un modelo (objeto) automáticamente.
     *
     * @param object $model
     * @param string $action
     */

     private static $maxSelect = 100;

    public static function FromConfig($table, $model, $action = '', $skipRequireds = false)
    {
        $modelForm = new $table();
        $pk = (new $model())->primary_key[0];

         $idEditar = '';
        $elementoEditado  = '';
        $controlador = Router::get('controller');
        if (Router::get('action') == 'editar') {
            $idEditar = implode(Router::get('parameters'));
            $elementoEditado = (new $controlador())->find($idEditar);
            $action  = $controlador .'/'. $action .'editar/'.$idEditar;
        } else {
            $action =  Router::get('controller') .'/'. Router::get('action'). '/'. implode(Router::get('parameters'));
        }

        echo '<form action="', PUBLIC_PATH.$action, '" method="post" >' , PHP_EOL;


        $test = (new $table())->find(
            "conditions:  TablaPropietaria ='$model'",
            "order: Orden ASC",
            "group: Type"
        );

        foreach ($test as $i => $t) {
            # code...
            $grouped_type = $t->Type;
           
            echo '<hr>';
        


        $fields = (new $table())->find("conditions:  TablaPropietaria ='$model' AND Type = '$grouped_type'", "order: Orden ASC");
       

        echo '<div class="form-row ">' , PHP_EOL;

        //  View::content()
        foreach ($fields as $field) {
            if ($field->TablaPropietaria === $model) {
                if ($pk !== $field->Name) {
                    echo '<div class="col-6 pt-2 pb-2">', PHP_EOL;
                } else {
                    echo '<div>', PHP_EOL;
                }


                $tipo = $field->Type;
                $alias = $field->Name;
                $nombreCampo = $field->Name;
                $valorCampo = $elementoEditado->$nombreCampo;


                if ($skipRequireds) {
                    $field->Extras = str_replace('required', '', $field->Extras);
                    $field->Extras = str_replace('required = "false"', '', $field->Extras);
                }


                if ($field->VisibleEnForm && $pk != $field->Name) {
                    //  echo "<label >$field->Label</label>" , PHP_EOL;


                    switch ($tipo) {
                  case 'select':
                              $sql = ''; $enum = '';
                              if (strlen($field->TablaForanea) <= 0) {
                                  $sql = "SELECT COLUMN_TYPE FROM information_schema.`COLUMNS`WHERE TABLE_NAME = '$field->TablaPropietaria' AND COLUMN_NAME = '$field->Name'";
                                  $sd = $modelForm->find_all_by_sql($sql);

                                  preg_match("/^enum\(\'(.*)\'\)$/", $sd[0]->COLUMN_TYPE, $matches);
                                  $enum = explode("','", $matches[1]);

                                  if (substr_count($field->Extras, 'required') > 0) {
                                      echo "<label >$field->Label 
                                        <span class='badge badge-danger'>Requerido</span>
                                    </label>" , PHP_EOL;
                                  } else {
                                      echo "<label >$field->Label</label>" , PHP_EOL;
                                  }


                                  echo "<select id=\"$field->Name\"    value=\"$valorCampo\"   name=\"$field->Name\" $field->Extras >", PHP_EOL;

                                  echo "<option value=0>Seleccione</option>";
                                  foreach ($enum as $value) {
                                      if ($valorCampo == $value) {
                                          echo "<option selected value=\"$value\">$value</option>", PHP_EOL;
                                      } else {
                                          echo "<option value=\"$value\">$value</option>", PHP_EOL;
                                      }
                                  }
                                  echo '<option value=0>Ninguno</option>';
                                  echo '</select>', PHP_EOL;
                              } else {
                                  $registros = (new $field->TablaForanea())->count();


                                  $campo = $field->BusquedaSelect;
                                  $campoValue = $field->CampoForaneo;

                                  if (self::$maxSelect > $registros) {
                                      if (substr_count($field->Extras, 'required') > 0) {
                                          echo "<label >$field->Label 
                                          <span class='badge badge-danger'>Requerido</span>
                                    </label>" , PHP_EOL;
                                      } else {
                                          echo "<label >$field->Label</label>" , PHP_EOL;
                                      }
                                      $tablaForanea  = (new $field->TablaForanea())->find_all_by_sql($field->Sentencias);


                                      if ($field->Name && $field->Name != $field->CampoForaneoValor) {
                                          echo "<select id=\"$field->CampoForaneoValor\"  value=\"$valorCampo\" $field->Extras  name=\"$field->Name\" >", PHP_EOL;
                                      } else {
                                          echo "<select  id=\"$field->Name\"  value=\"$valorCampo\" $field->Extras  name=\"$field->Name\" >", PHP_EOL;
                                      }

                                      echo "<option value=0>Seleccione</option>";

                                      foreach ($tablaForanea as $value) {
                                          if ($valorCampo == $value->$campoValue) {
                                              echo "<option selected value=\"".$value->$campoValue."\">" . $value->$campo ."</option>", PHP_EOL;
                                          } else {
                                              echo "<option value=\"".$value->$campoValue."\">" . $value->$campo ."</option>", PHP_EOL;
                                          }
                                      }
                                      echo '<option value=0>Ninguno</option>';
                                      echo '</select>', PHP_EOL;
                                  } else {
                                      echo '
                                    <div >';
                                      if (substr_count($field->Extras, 'required') > 0) {
                                          echo "<label class='mr-4' style='margin-bottom: -20px;'>$field->Label 
                                        <span class='badge badge-danger'>Requerido</span>
                                    </label>" , PHP_EOL;
                                      } else {
                                          echo "<label class='mr-4' style='margin-bottom: -20px;'>$field->Label</label>" , PHP_EOL;
                                      }
                                      echo ' <span class="mr-2" >Parcial</span>';
                                      echo'
                                        <label class="switcher-control switcher-control-lg mt-1 mb-1">
                                            <input id="ParcialBusqueda'. $field->Name .'" type="checkbox" class="switcher-input" >
                                            <span class="switcher-indicator"></span>
                                            <span class="switcher-label-on">
                                                <i class="fas fa-check"></i></span> <span class="switcher-label-off">
                                                <i class="fas fa-times"></i>
                                            </span>
                                        </label>
                                  </div>', PHP_EOL;

                                      $nombre=$field->Nombre;
                                      if ($field->CampoForaneoValor&&$field->CampoForaneoValor != $field->Name) {
                                          $nombre = $field->CampoForaneoValor;
                                      }

                                      if (Router::get('action') == 'editar') {
                                          $extractor = $field->BusquedaSelect;
                                          $buscador = (new $field->TablaForanea())->find_first("conditions: $field->CampoForaneo = $valorCampo ");
                                          echo '<select data-key-replace="'.$nombre.'"   value="'.$valorCampo.'" data-depend="'.$field->DependeDe.'" data-filter="'.$field->BusquedaSelect.'" id="'.$field->Name.'" name="'. $field->Name .'"  class="remoteinfo form-control">' , PHP_EOL;
                                          echo "<option value='$valorCampo'> ". $buscador->$extractor ."</oprtion>";
                                          echo '</select>' , PHP_EOL;
                                          ;
                                      } else {
                                          echo '
                                      <select data-key-replace="'.$nombre.'" value="'.$valorCampo.'" data-depend="'.$field->DependeDe.'" data-filter="'.$field->BusquedaSelect.'" id="'.$field->Name.'" name="'. $field->Name .'"  class="remoteinfo form-control"></select>
                                      ' , PHP_EOL;
                                      }
                                  }
                              }
                  break;






                  case 'textarea':
                       if (substr_count($field->Extras, 'required') > 0) {
                           echo "<label >$field->Label                                         <span class='badge badge-danger'>Requerido</span>
                 </label>" , PHP_EOL;
                       } else {
                                      echo "<label >$field->Label</label>" , PHP_EOL;
                                  }

                    echo "<textarea  $field->Extras id=\"$field->Name\" value=\"$valorCampo\" name=\"$field->Name\" >$valorCampo</textarea>" , PHP_EOL;

                  break;

                  default:
                       if (substr_count($field->Extras, 'required') > 0) {
                           echo "<label >$field->Label                                         <span class='badge badge-danger'>Requerido</span></label>" , PHP_EOL;
                       } else {
                            echo "<label >$field->Label</label>" , PHP_EOL;
                        }

                    if (!($field->DateFormat)) {
                        if ($field->Type === 'int') {
                            echo "<input type= \"text\"  data-allow-decimal='false'   data-mask='currency'  $field->Extras id=\"$field->Name\" name=\"$field->Name\" value=\"$valorCampo\" >" , PHP_EOL;
                        } elseif ($field->Type == 'number' || $field->Type == 'decimal') {
                            echo "<input type= \"text\" autocomplete='off' data-allow-decimal='true' data-decimal-limit='2' data-mask='currency'  $field->Extras id=\"$field->Name\" name=\"$field->Name\" value=\"$valorCampo\" >" , PHP_EOL;
                        } else {
                            echo "<input type= \"$tipo\"   $field->Extras id=\"$field->Name\" name=\"$field->Name\" value=\"$valorCampo\" >" , PHP_EOL;
                        }
                    } else {
                        echo "

                        <div id=\"flatpickr9\"  class=\"input-group input-group-alt flatpickr\"  data-wrap=\"true\" data-alt-input=\"true\" data-alt-format=\"$field->DateFormat\" data-date-format=\"yy-m-d\"  data-toggle=\"flatpickr\">
                          <input data-input=\"\" type= \"text\"  id=\"flatpickr-wrap\"   $field->Extras  name=\"$field->Name\" value=\"$valorCampo\" >
                              <div class=\"input-group-append\">
                              <button type='button' class=\"btn btn-secondary\" data-toggle=\"\" >
                                <i class=\"far fa-calendar\"></i>
                              </button>
                              <button type=\"button\" class=\"btn btn-secondary\" data-clear=\"\">
                                <i class=\"fa fa-times\"></i>
                              </button>
                            </div>
                        </div>

                        " , PHP_EOL;
                    }


                break;

              }
                } else {
                    if ($pk == $field->Name) {
                        echo "<input type= \"hidden\" $field->Extras id=\"$field->Name\"   name=\"$field->Name\" value=\"$valorCampo\" >" , PHP_EOL;
                    }
                }

                echo '</div>';
            }
        }
        echo '</div>';
    }

        echo '<div class="row">';
        echo '<div class="col">  <input class="btn btn-primary btn-block" type="submit" value="Enviar" /> </div>';
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

                $tipo = $field->Type;
                $alias = $field->Name;
                $nombreCampo = $field->Name;
                $valorCampo = $elementoEditado->$nombreCampo;


                if (true) {
                    $field->Extras = str_replace('required', '', $field->Extras);
                    $field->Extras = str_replace('required = "false"', '', $field->Extras);
                }


                if ($field->VisibleEnForm && $pk != $field->Name) {
                  //  echo "<label >$field->Label</label>" , PHP_EOL;


                    switch ($tipo) {
                  case 'select':
                              $sql = ''; $enum = '';
                              if (strlen($field->TablaForanea) <= 0) {
                                  $sql = "SELECT COLUMN_TYPE FROM information_schema.`COLUMNS`WHERE TABLE_NAME = '$field->TablaPropietaria' AND COLUMN_NAME = '$field->Name'";
                                  $sd = $modelForm->find_all_by_sql($sql);

                                  preg_match("/^enum\(\'(.*)\'\)$/", $sd[0]->COLUMN_TYPE, $matches);
                                  $enum = explode("','", $matches[1]);

                                  echo "<label >$field->Label</label>" , PHP_EOL;
                                  echo "editando un select" , PHP_EOL;


                                  echo "<select id=\"$field->Name\"    value=\"$valorCampo\"   name=\"$field->Name\" $field->Extras >", PHP_EOL;

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
                                    echo "<label >$field->Label</label>" , PHP_EOL;
                                    $tablaForanea  = (new $field->TablaForanea)->find_all_by_sql($field->Sentencias);


                                      echo "<select id=\"$field->Name\"  value=\"$valorCampo\" $field->Extras  name=\"$field->Name\" >", PHP_EOL;
                                      echo "<option selected value=\"\"> </option>", PHP_EOL;
                                      foreach ($tablaForanea as $value) {
                                          echo "<option value=\"".$value->$campoValue."\">" . $value->$campo ."</option>", PHP_EOL;
                                      }
                                      echo '</select>', PHP_EOL;

                                  }
                                  else{

                                    echo '
                                    <div >
                                        <label class="mr-5" >'.$field->Label.'</label>
                                        <span class="mr-2" >Busqueda Parcial</span>
                                        <label class="switcher-control switcher-control-lg pt-1 pb-1">
                                            <input id="ParcialBusqueda'. $field->Name .'" type="checkbox" class="switcher-input" >
                                            <span class="switcher-indicator"></span>
                                            <span class="switcher-label-on">
                                                <i class="fas fa-check"></i></span> <span class="switcher-label-off">
                                                <i class="fas fa-times"></i>
                                            </span>
                                        </label>
                                  </div>', PHP_EOL;



                                    if( Router::get('action') == 'editar' ){
                                      $extractor = $field->BusquedaSelect;
                                      $buscador = (new $field->TablaForanea)->find_first("conditions: $field->CampoForaneo = $valorCampo ");

                                      echo '<select   value="'.$valorCampo.'" data-depend="'.$field->DependeDe.'" data-filter="'.$field->BusquedaSelect.'" id="'.$field->Name.'" name="'. $field->Name .'"   class="remoteinfo form-control">' , PHP_EOL;
                                          echo "<option value='$valorCampo'> ". $buscador->$extractor ."</oprtion>";
                                      echo '</select>' , PHP_EOL;;
                                    }
                                    else{
                                      echo '
                                      <select value="'.$valorCampo.'" data-depend="'.$field->DependeDe.'" data-filter="'.$field->BusquedaSelect.'" id="'.$field->Name.'" name="'. $field->Name .'"  class="remoteinfo form-control"></select>
                                      ' , PHP_EOL;
                                    }




                                  }

                              }
                  break;

                  case 'textarea':
                    echo "<label >$field->Label</label>" , PHP_EOL;
                    echo "<textarea  $field->Extras id=\"$field->Name\" value=\"$valorCampo\" name=\"$field->Name\" >$valorCampo</textarea>" , PHP_EOL;

                  break;

                  default:
                    echo "<label >$field->Label</label>" , PHP_EOL;

                    if( !($field->DateFormat) ){
                      if( $field->Type == 'number' )
                        echo "<input type= \"$tipo\"   autocomplete='off' data-allow-decimal='true' data-decimal-limit='2' data-mask='currency'  $field->Extras id=\"$field->Name\" name=\"$field->Name\" value=\"$valorCampo\" >" , PHP_EOL;
                      else
                        echo "<input type= \"$tipo\"   $field->Extras id=\"$field->Name\" name=\"$field->Name\" value=\"$valorCampo\" >" , PHP_EOL;

                    }
                    else
                    {
                        echo "

                        <div id=\"flatpickr9\"  class=\"input-group input-group-alt flatpickr\"  data-wrap=\"true\" data-alt-input=\"true\" data-alt-format=\"$field->DateFormat\" data-date-format=\"yy-m-d\"  data-toggle=\"flatpickr\">
                          <input data-input=\"\" type= \"text\"  id=\"flatpickr-wrap\"   $field->Extras  name=\"$field->Name\" value=\"$valorCampo\" >
                              <div class=\"input-group-append\">
                              <button type='button' class=\"btn btn-secondary\" data-toggle=\"\" >
                                <i class=\"far fa-calendar\"></i>
                              </button>
                              <button type=\"button\" class=\"btn btn-secondary\" data-clear=\"\">
                                <i class=\"fa fa-times\"></i>
                              </button>
                            </div>
                        </div>

                        " , PHP_EOL;
                    }



                break;

              }
                } else {
                    echo "<input type= \"hidden\" $field->Extras id=\"$field->Name\"   name=\"$field->Name\" value=\"$valorCampo\" >" , PHP_EOL;
                }

                echo '</div>';
            }
        }

        echo '<div class="row">';
        echo '<div class="col">  <input class="btn btn-primary btn-block" type="submit" value="Enviar" /> </div>';
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