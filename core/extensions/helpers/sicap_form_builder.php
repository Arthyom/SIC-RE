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

    public static function FromModelToEditForm($conf_table, $raw_model)
    {
        // set globals scoped variables
        $object_raw_model  = new $raw_model();
        $not_null_fields   = $object_raw_model->not_null;
        $types_raw_model   = $object_raw_model->_data_type;
        $model_primary_key = $object_raw_model->primary_key[0];
        $object_conf_table = new $conf_table();
        $id_to_edit = implode(Router::get('parameters'));
        $object_raw_model_to_edit = $object_raw_model->find($id_to_edit);
        $max_common_select_values = 15;



        $conf_table_grouped_by_type = $object_conf_table->find(
            "conditions:  TablaPropietaria ='$raw_model'",
            "order: Orden ASC",
            "group: Type"
        );

        // open form for creation
        echo "<form action='' method='post' >" , PHP_EOL;
        // get grouped controls
        foreach ($conf_table_grouped_by_type  as $group_key => $group_value) {
            echo "<hr>" , PHP_EOL;

            $ordering_asc = "order: Orden ASC";
            $grouped_conditions = "conditions:  TablaPropietaria ='$raw_model' AND Type = '$group_value->Type'";
            $grouped_fields = $object_conf_table->find($grouped_conditions, $ordering_asc);

            echo "<div class='form-row'>" , PHP_EOL;

            foreach ($grouped_fields  as $grouped_field_key => $grouped_field) {
                $raw_model_property_to_replace = $grouped_field->Name;

                try {
                    if ($grouped_field->VisibleEnForm && $grouped_field->Name !== $model_primary_key) {
                        $required = '';
                        echo "<div class='col-6 pt-2 pb-2'>", PHP_EOL;

                        echo "<label>$grouped_field->Label";
                        if (in_array($grouped_field->Name, $not_null_fields)) {
                            $required = 'required';
                            echo "<span class='badge badge-danger'>Requerido</span></label>", PHP_EOL;
                        } else {
                            echo "</label>", PHP_EOL;
                        }

                        $type_from_model = $types_raw_model[ $grouped_field->Name ];

                        if (strpos($type_from_model, 'enum') !== false) {
                            $enum_options;
                            preg_match("/^enum\(\'(.*)\'\)$/", $type_from_model, $enum_options);
                            $enum_options = explode("','", $enum_options[1]);

                            echo "<select id='$grouped_field->Name' $required name='$grouped_field->Name' $grouped_field->Extras >", PHP_EOL;
                            echo "<option value=0>Seleccione</option>", PHP_EOL;
                            foreach ($enum_options as $option) {
                                $selected = '';

                                $data_from_raw_model = $object_raw_model_to_edit->$raw_model_property_to_replace ;
                                if (mb_strtolower($option) === mb_strtolower($data_from_raw_model)) {
                                    $selected = 'selected';
                                }
                                echo "<option $selected  value=\"$option\">$option</option>", PHP_EOL;
                            }
                            echo '<option value=0>Ninguno</option>';
                            echo "</select>", PHP_EOL;
                        }

                        if (strpos($type_from_model, 'date') !== false) {
                            echo "<input value='{$object_raw_model_to_edit->$raw_model_property_to_replace}' type='date' $grouped_field->Extras  id='$grouped_field->Name' name='$grouped_field->Extras' >" , PHP_EOL;
                        }

                        if (strpos($type_from_model, 'varchar') !== false) {
                            $max_varchar_len = 30;
                            $varchar_len = 0;
                            preg_match("/^varchar\((.*)\)$/", $type_from_model, $varchar_len);
                            $varchar_len = intval($varchar_len[1]);
                            if ($varchar_len <= 30) {
                                echo "<input type='text'   value='{$object_raw_model_to_edit->$raw_model_property_to_replace}' maxlength='$varchar_len' $required $grouped_field->Extras id='$grouped_field->Name' name='$grouped_field->Name' />" , PHP_EOL;
                            } else {
                                echo "<textarea  maxlength='$varchar_len' $grouped_field->Extras $required id='$grouped_field->Name' name='$grouped_field->Name' >{$object_raw_model_to_edit->$raw_model_property_to_replace}
                                </textarea>" , PHP_EOL;
                            }
                        }

                        if (strpos($type_from_model, 'int') !== false) {
                            if ($grouped_field->Type === 'select') {
                                $object_foreing_table = (new $grouped_field->TablaForanea);
                                $related_options = $object_foreing_table->find();
                                if (count($related_options) > $max_common_select_values) {
                                    echo "<select id='$grouped_field->Name' $required name='$grouped_field->Name' $grouped_field->Extras >", PHP_EOL;
                                    echo "<option value=0>Seleccione</option>", PHP_EOL;
                                    $from_config_field_to_replace_value = $grouped_field->BusquedaSelect;
                                    $from_config_field_to_replace_key = $grouped_field->CampoForaneoValor;
                                    $value_to_display = $related_option_value->$from_config_field_to_replace_key;
                                    $text_to_display = $related_option_value->$from_config_field_to_replace_value;
                                    $text_to_compare = $object_raw_model_to_edit->$raw_model_property_to_replace ;

                                    foreach ($related_options as $related_option_key => $related_option_value) {
                                        $selected = '';

                                        if ($value_to_display === $text_to_compare) {
                                            $selected = 'selected';
                                        }
                                        //echo "<option>{$value_to_display}</option>";

                                        echo "<option $selected value='{$value_to_display}'>{$text_to_display}</option>", PHP_EOL;
                                    }
                                    echo '<option value=0>Ninguno</option>';
                                    echo "</select>", PHP_EOL;
                                } else {
                                    echo'

                                      <span class="mr-2" >Parcial</span>
                                      <label class="switcher-control switcher-control-lg mt-1 mb-1">
                                      <input id="ParcialBusqueda'.$grouped_field->Name.'" type="checkbox" class="switcher-input" >
                                      <span class="switcher-indicator"></span>
                                      <span class="switcher-label-on">
                                          <i class="fas fa-check"></i></span> <span class="switcher-label-off">
                                          <i class="fas fa-times"></i>
                                      </span>
                                      </label>
                                  ', PHP_EOL;
                                    $property_to_display = $grouped_field->BusquedaSelect;
                                    $property_to_replace = $grouped_field->CampoForaneoValor;
                                    $id_to_replace = $object_raw_model_to_edit->$property_to_replace;
                                    $foreing_match = $object_foreing_table->find_first("$property_to_replace = $id_to_replace");

                                    echo "<select data-key-replace='$grouped_field->CampoForaneoValor' $required data-depend='$grouped_field->DependeDe'
                                   data-filter='$grouped_field->BusquedaSelect' id='$grouped_field->CampoForaneoValor' name='$grouped_field->CampoForaneoValor'
                                   class='remoteinfo form-control'>", PHP_EOL;
                                    echo "<option value='{$id_to_replace}'>{$foreing_match->$property_to_display}</oprtion>";
                                    echo '</select>', PHP_EOL;
                                }
                            } else {
                                echo "<input type='text' $required data-allow-decimal='false'
                              value='{$object_raw_model_to_edit->$raw_model_property_to_replace}'
                               data-mask='currency'  $grouped_field->Extras id='$grouped_field->Name' name='$grouped_field->Name'>" , PHP_EOL;
                            }
                        }

                        if (strpos($type_from_model, 'decimal') !== false || strpos($type_from_model, 'number') !== false) {
                            echo "<input type='text'
                          value='{$object_raw_model_to_edit->$raw_model_property_to_replace}'
                          autocomplete='off' data-allow-decimal='true' data-decimal-limit='2' data-mask='currency' $grouped_field->Extras id='$grouped_field->Nale' name='$grouped_field->Name'>" , PHP_EOL;
                        }




                        //echo $type_from_model;
                        echo "</div>", PHP_EOL;
                    }
                } catch (\Exception $e) {
                    echo "<div class='alert alert-danger has-icon alert-dismissible fade show'>
                        <div class='alert-icon'>
                            <span class='oi oi-bell'></span>
                        </div>
                        <h4 class='alert-heading'> Error </h4>
                        <input type='text' disabled placeholder='Al construir el campo, revice que su valor no sea nulo'
                        $grouped_field->Extras id='$grouped_field->Name' name='$grouped_field->Name'>
                    </div>";

                    echo "</div>", PHP_EOL;
                }
            }


            echo "</div>" , PHP_EOL;




            // code...
        }

        echo '<div class="row">';
        echo '<div class="col">  <input class="btn btn-primary btn-block" type="submit" value="Enviar" /> </div>';
        echo '<div class="col">  ';
        echo Html::linkAction('', 'Cancelar', 'class="btn btn-danger btn-block"');
        echo '</div>';
        echo "</form>" , PHP_EOL;

        // code...
    }

    public static function FromModelToAddForm($conf_table, $raw_model)
    {
        // get globals scoped variables
        $object_raw_model  = new $raw_model();
        $not_null_fields   = $object_raw_model->not_null;
        $types_raw_model   = $object_raw_model->_data_type;
        $model_primary_key = $object_raw_model->primary_key[0];
        $object_conf_table = new $conf_table();
        $max_common_select_values = 15;

        $conf_table_grouped_by_type = $object_conf_table->find(
            "conditions:  TablaPropietaria ='$raw_model'",
            "order: Orden ASC",
            "group: Type"
        );

        // open form for creation
        echo "<form action='' method='post' >" , PHP_EOL;
        // get grouped controls
        foreach ($conf_table_grouped_by_type  as $group_key => $group_value) {
            echo "<hr>" , PHP_EOL;

            $ordering_asc = "order: Orden ASC";
            $grouped_conditions = "conditions:  TablaPropietaria ='$raw_model' AND Type = '$group_value->Type'";
            $grouped_fields = $object_conf_table->find($grouped_conditions, $ordering_asc);

            echo "<div class='form-row'>" , PHP_EOL;

            foreach ($grouped_fields  as $grouped_field_key => $grouped_field) {
                try {
                    if ($grouped_field->VisibleEnForm && $grouped_field->Name !== $model_primary_key) {
                        $required = '';
                        echo "<div class='col-6 pt-2 pb-2'>", PHP_EOL;

                        echo "<label>$grouped_field->Label";
                        if (in_array($grouped_field->Name, $not_null_fields)) {
                            $required = 'required';
                            echo "<span class='badge badge-danger'>Requerido</span></label>", PHP_EOL;
                        } else {
                            echo "</label>", PHP_EOL;
                        }

                        $type_from_model = $types_raw_model[ $grouped_field->Name ];

                        if (strpos($type_from_model, 'enum') !== false) {
                            $enum_options;
                            preg_match("/^enum\(\'(.*)\'\)$/", $type_from_model, $enum_options);
                            $enum_options = explode("','", $enum_options[1]);

                            echo "<select id='$grouped_field->Name' $required name='$grouped_field->Name' $grouped_field->Extras >", PHP_EOL;
                            echo "<option value=0>Seleccione</option>", PHP_EOL;
                            foreach ($enum_options as $option) {
                                echo "<option  value=\"$option\">$option</option>", PHP_EOL;
                            }
                            echo '<option value=0>Ninguno</option>';
                            echo "</select>", PHP_EOL;
                        }

                        if (strpos($type_from_model, 'date') !== false) {
                            echo "<input type='date' $grouped_field->Extras  id='$grouped_field->Name' name='$grouped_field->Extras' >" , PHP_EOL;
                        }

                        if (strpos($type_from_model, 'varchar') !== false) {
                            $max_varchar_len = 30;
                            $varchar_len = 0;
                            preg_match("/^varchar\((.*)\)$/", $type_from_model, $varchar_len);
                            $varchar_len = intval($varchar_len[1]);
                            if ($varchar_len <= 30) {
                                echo "<input type='text' maxlength='$varchar_len' $required $grouped_field->Extras id='$grouped_field->Name' name='$grouped_field->Name' />" , PHP_EOL;
                            } else {
                                echo "<textarea maxlength='$varchar_len' $grouped_field->Extras $required id='$grouped_field->Name' name='$grouped_field->Name' ></textarea>" , PHP_EOL;
                            }
                        }

                        if (strpos($type_from_model, 'int') !== false) {
                            if ($grouped_field->Type === 'select') {
                                $object_owner_table = (new $grouped_field->TablaForanea);
                                $related_options = $object_owner_table->find();

                                if (count($related_options) <= $max_common_select_values) {
                                    echo "<select id='$grouped_field->Name' $required name='$grouped_field->Name' $grouped_field->Extras >", PHP_EOL;
                                    echo "<option value=0>Seleccione</option>", PHP_EOL;
                                    $from_config_field_to_replace_value = $grouped_field->BusquedaSelect;
                                    $from_config_field_to_replace_key = $grouped_field->CampoForaneoValor;

                                    foreach ($related_options as $related_option_key => $related_option_value) {
                                        echo "<option value='{$related_option_value->$from_config_field_to_replace_key}'>{$related_option_value->$from_config_field_to_replace_value}</option>", PHP_EOL;
                                    }
                                    echo '<option value=0>Ninguno</option>';
                                    echo "</select>", PHP_EOL;
                                } else {
                                    echo'

                                      <span class="mr-2" >Parcial</span>
                                      <label class="switcher-control switcher-control-lg mt-1 mb-1">
                                      <input id="ParcialBusqueda'.$grouped_field->Name.'" type="checkbox" class="switcher-input" >
                                      <span class="switcher-indicator"></span>
                                      <span class="switcher-label-on">
                                          <i class="fas fa-check"></i></span> <span class="switcher-label-off">
                                          <i class="fas fa-times"></i>
                                      </span>
                                      </label>
                                  ', PHP_EOL;
                                    echo "<select data-key-replace='$grouped_field->CampoForaneoValor' $required data-depend='$grouped_field->DependeDe'
                                   data-filter='$grouped_field->BusquedaSelect' id='$grouped_field->CampoForaneoValor' name='$grouped_field->CampoForaneoValor'
                                   class='remoteinfo form-control'></select>", PHP_EOL;
                                }
                            } else {
                                echo "<input type='text' $required data-allow-decimal='false'   data-mask='currency'  $grouped_field->Extras id='$grouped_field->Name' name='$grouped_field->Name'>" , PHP_EOL;
                            }
                        }

                        if (strpos($type_from_model, 'decimal') !== false || strpos($type_from_model, 'number') !== false) {
                            echo "<input type='text' autocomplete='off' data-allow-decimal='true' data-decimal-limit='2' data-mask='currency' $grouped_field->Extras id='$grouped_field->Nale' name='$grouped_field->Name'>" , PHP_EOL;
                        }




                        //echo $type_from_model;
                        echo "</div>", PHP_EOL;
                    }
                } catch (\Exception $e) {
                    echo "<div class='alert alert-danger has-icon alert-dismissible fade show'>
                      <div class='alert-icon'>
                          <span class='oi oi-bell'></span>
                      </div>
                      <h4 class='alert-heading'> Error </h4>
                      <input type='text' disabled placeholder='Al construir el campo, revice que su valor no sea nulo'
                      $grouped_field->Extras id='$grouped_field->Name' name='$grouped_field->Name'>
                  </div>";

                    echo "</div>", PHP_EOL;
                }
            }

            echo "</div>" , PHP_EOL;




            // code...
        }

        echo '<div class="row">';
        echo '<div class="col">  <input class="btn btn-primary btn-block" type="submit" value="Enviar" /> </div>';
        echo '<div class="col">  ';
        echo Html::linkAction('', 'Cancelar', 'class="btn btn-danger btn-block"');
        echo '</div>';
        echo "</form>" , PHP_EOL;

        // code...
    }

    public static function FromConfig($table, $model, $action = '', $skipRequireds = false)
    {
        $modelForm = new $table();
        //echo " el modelo es ". json_encode((array)(new $model

        $t = (new $model)->not_null;//_data_type;
        foreach ($t as $key => $value) {
            echo 'clave '. $key  . var_dump($value) . '<hr>';
            // code...
        }
        $pk = (new $model())->primary_key[0];

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

                                  if (self::$maxSelect > $registros) {
                                      if (substr_count($field->Extras, 'required') > 0) {
                                          echo "<label >$field->Label
                                          <span class='badge badge-danger'>Requerido</span>
                                    </label>" , PHP_EOL;
                                      } else {
                                          echo "<label >$field->Label</label>" , PHP_EOL;
                                      }
                                      $tablaForanea  = (new $field->TablaForanea())->find_all_by_sql($field->Sentencias);


                                      if($field->Name && $field->Name != $field->CampoForaneoValor)
                                      echo "<select id=\"$field->CampoForaneoValor\"  value=\"$valorCampo\" $field->Extras  name=\"$field->Name\" >", PHP_EOL;
                                      else
                                      echo "<select * id=\"$field->Name\"  value=\"$valorCampo\" $field->Extras  name=\"$field->Name\" >", PHP_EOL;


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
                                  if($field->CampoForaneoValor&&$field->CampoForaneoValor != $field->Name)
                                  $nombre = $field->CampoForaneoValor;

                                    if( Router::get('action') == 'editar' ){
                                      $extractor = $field->BusquedaSelect;
                                      $buscador = (new $field->TablaForanea)->find_first("conditions: $field->CampoForaneo = $valorCampo ");

                                      echo '<select data-key-replace="'.$nombre.'"   value="'.$valorCampo.'" data-depend="'.$field->DependeDe.'" data-filter="'.$field->BusquedaSelect.'" id="'.$field->Name.'" name="'. $field->Name .'"  class="remoteinfo form-control">' , PHP_EOL;
                                          echo "<option value='$valorCampo'> ". $buscador->$extractor ."</oprtion>";
                                      echo '</select>' , PHP_EOL;;
                                    }
                                    else{

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

                                  if (self::$maxSelect > $registros) {
                                      echo "<label >$field->Label</label>" , PHP_EOL;
                                      $tablaForanea  = (new $field->TablaForanea)->find_all_by_sql($field->Sentencias);


                                      echo "<select id=\"$field->Name\"  value=\"$valorCampo\" $field->Extras  name=\"$field->Name\" >", PHP_EOL;
                                      echo "<option selected value=\"\"> </option>", PHP_EOL;
                                      foreach ($tablaForanea as $value) {
                                          echo "<option value=\"".$value->$campoValue."\">" . $value->$campo ."</option>", PHP_EOL;
                                      }
                                      echo '</select>', PHP_EOL;
                                  } else {
                                      echo '
                                    <div >
                                        <label class="mr-5" >'.$field->Label.'</label>
                                        <span class="mr-2" >Busqueda Parcial</span>
                                        <label class="switcher-control switcher-control-lg">
                                            <input id="ParcialBusqueda'. $field->Name .'" type="checkbox" class="switcher-input" >
                                            <span class="switcher-indicator"></span>
                                            <span class="switcher-label-on">
                                                <i class="fas fa-check"></i></span> <span class="switcher-label-off">
                                                <i class="fas fa-times"></i>
                                            </span>
                                        </label>
                                  </div>', PHP_EOL;



                                      if (Router::get('action') == 'editar') {
                                          $extractor = $field->BusquedaSelect;
                                          $buscador = (new $field->TablaForanea)->find_first("conditions: $field->CampoForaneo = $valorCampo ");

                                          echo '<select   value="'.$valorCampo.'" data-depend="'.$field->DependeDe.'" data-filter="'.$field->BusquedaSelect.'" id="'.$field->Name.'" name="'. $field->Name .'"   class="remoteinfo form-control">' , PHP_EOL;
                                          echo "<option value='$valorCampo'> ". $buscador->$extractor ."</oprtion>";
                                          echo '</select>' , PHP_EOL;
                                          ;
                                      } else {
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

                    if (!($field->DateFormat)) {
                        if ($field->Type == 'number') {
                            echo "<input type= \"$tipo\"   autocomplete='off' data-allow-decimal='true' data-decimal-limit='2' data-mask='currency'  $field->Extras id=\"$field->Name\" name=\"$field->Name\" value=\"$valorCampo\" >" , PHP_EOL;
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
        $datosConfiguracionTabla = (new $configuracion)->find("TablaPropietaria LIKE '$tablaMaestro'");
        $esclavos = $datosConfiguracionTabla[0]->Esclavos;
        $dataEsclavo= (new $configuracion)->find("TablaPropietaria LIKE '$esclavos'");
        $page = implode(Router::get('parameters')) ;
        $data = (new $tablaMaestro)->paginate("page: $page");


        View::partial('sicap_form_builder/masterdetailform', false, array('tablamaestro'=>$tablaMaestro, 'dataEsclavo'=>$dataEsclavo, 'tablaesclavo'=>$esclavos, 'data'=>$data, 'ctd'=>$datosConfiguracionTabla ));
        View::partial('paginators/punbb', false, array('page' => $data ,'url' => Router::get('controller_path').'/masterdetail'));
    }


    public static function FromConfigDetails($tablaMaestro, $id, $configuracion = 'configuracionTabla')
    {
        $datosConfiguracionTabla = (new $configuracion)->find("TablaPropietaria LIKE '$tablaMaestro'");
        $esclavos = $datosConfiguracionTabla[0]->Esclavos;
        $dataEsclavo= (new $configuracion)->find("TablaPropietaria LIKE '$esclavos'");
        $page = implode(Router::get('parameters')) ;
        $data = (new $tablaMaestro)->find($id);


        View::partial('sicap_form_builder/details', false, array( 'id'=>$id, 'tablamaestro'=>$tablaMaestro, 'dataEsclavo'=>$dataEsclavo, 'tablaesclavo'=>$esclavos, 'data'=>$data, 'ctd'=>$datosConfiguracionTabla ));
        //View::partial('paginators/punbb', false, array('page' => $data ,'url' => Router::get('controller_path').'/masterdetail'));
    }
}
