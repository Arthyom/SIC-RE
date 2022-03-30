<?php

/** */


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

    private  $_maxSelectItems;

    public static function getPropertyKey($key)
    {
        $globalConfigs =  include APP_PATH . 'config/config.php';
        $globalConfigs = json_decode(json_encode($globalConfigs, JSON_FORCE_OBJECT));
        return $globalConfigs->global_constants->$key;
    }

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
        $max_common_select_values = SicapFormBuilder::getPropertyKey('max_select_items');

        $conf_table_grouped_by_type = $object_conf_table->find(
            "conditions:  TablaPropietaria ='$raw_model'",
            "order: Orden ASC",
            "group: Type"
        );

        // open form for creation
        echo "<form action='' method='post' >", PHP_EOL;
        // get grouped controls
        foreach ($conf_table_grouped_by_type  as $group_key => $group_value) {
            echo "<hr>", PHP_EOL;

            $ordering_asc = "order: Orden ASC";
            $grouped_conditions = "conditions:  TablaPropietaria ='$raw_model' AND Type = '$group_value->Type'";
            $grouped_fields = $object_conf_table->find($grouped_conditions, $ordering_asc);

            echo "<div class='form-row'>", PHP_EOL;

            foreach ($grouped_fields  as $grouped_field_key => $grouped_field) {
                $raw_model_property_to_replace = $grouped_field->Name;

                try {
                    //echo var_dump($grouped_field->VisibleEnForm );
                    if ($grouped_field->VisibleEnForm  && $grouped_field->Name !== $model_primary_key) {
                        $grouped_field->Extras = str_replace('required', '', $grouped_field->Extras);

                        $required = '';
                        echo "<div class='col-6 pt-2 pb-2'>", PHP_EOL;

                        echo "<label>$grouped_field->Label";
                        if (in_array($grouped_field->Name, $not_null_fields)) {
                            $required = 'required';
                            echo "<span class='badge badge-danger'>Requerido</span></label>", PHP_EOL;
                        } else {
                            echo "</label>", PHP_EOL;
                        }

                        $type_from_model = $types_raw_model[$grouped_field->Name];

                        if (strpos($type_from_model, 'enum') !== false) {
                            $enum_options = null;
                            preg_match("/^enum\(\'(.*)\'\)$/", $type_from_model, $enum_options);
                            $enum_options = explode("','", $enum_options[1]);

                            echo "<select id='$grouped_field->Name' $required name='$grouped_field->Name' $grouped_field->Extras >", PHP_EOL;
                            echo "<option value=0>Seleccione</option>", PHP_EOL;
                            foreach ($enum_options as $option) {
                                $selected = '';

                                $data_from_raw_model = $object_raw_model_to_edit->$raw_model_property_to_replace;
                                if (mb_strtolower($option) === mb_strtolower($data_from_raw_model)) {
                                    $selected = 'selected';
                                }
                                echo "<option $selected  value=\"$option\">$option</option>", PHP_EOL;
                            }
                            echo '<option value=0>Ninguno</option>';
                            echo "</select>", PHP_EOL;
                        }

                        if (strpos($type_from_model, 'date') !== false) {
                            echo "<input value='{$object_raw_model_to_edit->$raw_model_property_to_replace}' type='date' $grouped_field->Extras  id='$grouped_field->Name' name='$grouped_field->Name' >", PHP_EOL;
                        }

                        if (strpos($type_from_model, 'varchar') !== false) {
                            $max_varchar_len = 30;
                            $varchar_len = 0;
                            preg_match("/^varchar\((.*)\)$/", $type_from_model, $varchar_len);
                            $varchar_len = intval($varchar_len[1]);
                            if ($varchar_len <= 30) {
                                echo "<input type='text'   value='{$object_raw_model_to_edit->$raw_model_property_to_replace}' maxlength='$varchar_len' $required $grouped_field->Extras id='$grouped_field->Name' name='$grouped_field->Name' />", PHP_EOL;
                            } else {
                                echo "<textarea  maxlength='$varchar_len' $grouped_field->Extras $required id='$grouped_field->Name' name='$grouped_field->Name' >{$object_raw_model_to_edit->$raw_model_property_to_replace}
                                 </textarea>", PHP_EOL;
                            }
                        }

                        if (strpos($type_from_model, 'int') !== false) {
                            if ($grouped_field->Type === 'select') {
                                $object_foreing_table = (new $grouped_field->TablaForanea);
                                $related_options = $object_foreing_table->find();
                                if (count($related_options) < $max_common_select_values) {
                                    echo "<select id='$grouped_field->Name' $required name='$grouped_field->Name' $grouped_field->Extras >", PHP_EOL;
                                    echo "<option value=0>Seleccione</option>", PHP_EOL;
                                    $from_config_field_to_replace_value = $grouped_field->BusquedaSelect;
                                    $from_config_field_to_replace_key = $grouped_field->CampoForaneoValor;
                                    $value_to_display = $related_option_value->$from_config_field_to_replace_key;
                                    $text_to_display = $related_option_value->$from_config_field_to_replace_value;
                                    $text_to_compare = $object_raw_model_to_edit->$raw_model_property_to_replace;

                                    foreach ($related_options as $related_option_key => $related_option_value) {
                                        $selected = '';

                                        if ($text_to_compare === $related_option_value->$from_config_field_to_replace_key) {
                                            $selected = 'selected';
                                        }
                                        //echo "<option>{$value_to_display}</option>";


                                        echo "<option $selected value='{$related_option_value->$from_config_field_to_replace_key}'>{$related_option_value->$from_config_field_to_replace_value}</option>", PHP_EOL;
                                    }
                                    echo '<option value=0>Ninguno</option>';
                                    echo "</select>", PHP_EOL;
                                } else {
                                    echo '
 
                                       <span class="mr-2" >Parcial</span>
                                       <label class="switcher-control switcher-control-lg mt-1 mb-1">
                                       <input id="ParcialBusqueda' . $grouped_field->Name . '" type="checkbox" class="switcher-input" >
                                       <span class="switcher-indicator"></span>
                                       <span class="switcher-label-on">
                                           <i class="fas fa-check"></i></span> <span class="switcher-label-off">
                                           <i class="fas fa-times"></i>
                                       </span>
                                       </label>
                                   ', PHP_EOL;
                                    $filter = str_replace(' ', '', $grouped_field->BusquedaSelect);
                                    $valorSelect = '';
                                    $listaParametros = explode(',', $filter);
                                    if (in_array($grouped_field->Name, $object_foreing_table->fields))
                                        $property_to_replace = $grouped_field->CampoForaneoValor;
                                    else
                                        $property_to_replace = $grouped_field->Name;

                                    $id_to_replace = $object_raw_model_to_edit->$property_to_replace;


                                    $property_to_replace = $grouped_field->CampoForaneoValor;


                                    $foreing_match = $object_foreing_table->find_first("$property_to_replace = $id_to_replace");

                                    //check if there are a parameter list for 'BusquedaSelect' field
                                    if (strstr($grouped_field->BusquedaSelect, ',') !== false) {
                                        foreach ($listaParametros as $key => $parametro) {
                                            $valorSelect .= $foreing_match->$parametro . ' ';
                                        }
                                    } else {
                                        $valorSelect = $grouped_field->BusquedaSelect;
                                        $valorSelect = $foreing_match->$valorSelect;
                                    }
                                    echo "<select data-key-replace='$grouped_field->CampoForaneoValor' $required data-depend='$grouped_field->DependeDe'
                                    data-filter='$filter' id='$grouped_field->Name' name='$grouped_field->Name'
                                    class='remoteinfo form-control'>", PHP_EOL;
                                    echo "<option value='{$id_to_replace}'>{$valorSelect}</option>";
                                    echo '</select>', PHP_EOL;
                                }
                            } else {
                                switch ($type_from_model) {
                                    case 'tinyint(1)':
                                        $active = $object_raw_model_to_edit->$raw_model_property_to_replace ?  true : false;
                                        $checked = $active ? 'checked' : '';
                                        echo "
                                       <div class='list-group-item d-flex justify-content-between align-items-center'>
                                       
                                       <div>
                                       
                                         <label class='switcher-control switcher-control-success switcher-control-lg'>
                                           <input 
                                           value='$active' 
                                           type='checkbox'  class='switcher-input' $checked $required name='$grouped_field->Name'> 
                                           <span class='switcher-indicator'></span> 
                                           <span class='switcher-label-on'><i class='fas fa-check'></i></span> 
                                           <span class='switcher-label-off'><i class='fas fa-times'></i></span>
                                       </label> 
                        
                                       </div>
                                     </div>";

                                        break;

                                    default:
                                        echo "<input type='text' $required data-allow-decimal='false'
                                      value='{$object_raw_model_to_edit->$raw_model_property_to_replace}'
                                       data-mask='currency'  $grouped_field->Extras id='$grouped_field->Name' name='$grouped_field->Name'>", PHP_EOL;
                                        break;
                                }
                            }
                        }

                        if (strpos($type_from_model, 'decimal') !== false || strpos($type_from_model, 'number') !== false) {
                            echo "<input type='text'
                           value='{$object_raw_model_to_edit->$raw_model_property_to_replace}'
                           autocomplete='off' data-allow-decimal='true' data-decimal-limit='2' data-mask='currency' $grouped_field->Extras id='$grouped_field->Nale' name='$grouped_field->Name'>", PHP_EOL;
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


            echo "</div>", PHP_EOL;




            // code...
        }

        echo '<div class="row">';
        echo '<div class="col">  <input class="btn btn-primary btn-block" type="submit" value="Enviar" /> </div>';
        echo '<div class="col">  ';
        echo Html::linkAction('', 'Cancelar', 'class="btn btn-danger btn-block"');
        echo '</div>';
        echo "</form>", PHP_EOL;

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
        $max_common_select_values = SicapFormBuilder::getPropertyKey('max_select_items');

        $conf_table_grouped_by_type = $object_conf_table->find(
            "conditions:  TablaPropietaria ='$raw_model'",
            "order: Orden ASC",
            "group: Type"
        );

        // open form for creation
        echo "<form action='crear' method='post' >", PHP_EOL;
        // get grouped controls
        foreach ($conf_table_grouped_by_type  as $group_key => $group_value) {
            echo "<hr>", PHP_EOL;

            $ordering_asc = "order: Orden ASC";
            $grouped_conditions = "conditions:  TablaPropietaria ='$raw_model' AND Type = '$group_value->Type'";
            $grouped_fields = $object_conf_table->find($grouped_conditions, $ordering_asc);

            echo "<div class='form-row'>", PHP_EOL;

            foreach ($grouped_fields  as $grouped_field_key => $grouped_field) {
                try {
                    if ($grouped_field->VisibleEnForm && $grouped_field->Name !== $model_primary_key) {
                        $grouped_field->Extras = str_replace('required', '', $grouped_field->Extras);
                        $required = '';
                        echo "<div class='col-6 pt-2 pb-2'>", PHP_EOL;

                        echo "<label>$grouped_field->Label";
                        if (in_array($grouped_field->Name, $not_null_fields)) {
                            $required = 'required';
                            echo "<span class='badge badge-danger'>Requerido</span></label>", PHP_EOL;
                        } else {
                            echo "</label>", PHP_EOL;
                        }

                        $type_from_model = $types_raw_model[$grouped_field->Name];

                        if (strpos($type_from_model, 'enum') !== false) {
                            $enum_options = null;
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
                            echo "<input type='date' $grouped_field->Extras  id='$grouped_field->Name' name='$grouped_field->Name' >", PHP_EOL;
                        }

                        if (strpos($type_from_model, 'varchar') !== false) {
                            $max_varchar_len = 30;
                            $varchar_len = 0;
                            preg_match("/^varchar\((.*)\)$/", $type_from_model, $varchar_len);
                            $varchar_len = intval($varchar_len[1]);
                            if ($varchar_len <= 30) {
                                echo "<input type='text' maxlength='$varchar_len' $required $grouped_field->Extras id='$grouped_field->Name' name='$grouped_field->Name' />", PHP_EOL;
                            } else {
                                echo "<textarea maxlength='$varchar_len' $grouped_field->Extras $required id='$grouped_field->Name' name='$grouped_field->Name' ></textarea>", PHP_EOL;
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

                                    echo '
 
                                       <span class="mr-2" >Parcial</span>
                                       <label class="switcher-control switcher-control-lg mt-1 mb-1">
                                       <input id="ParcialBusqueda' . $grouped_field->Name . '" type="checkbox" class="switcher-input" >
                                       <span class="switcher-indicator"></span>
                                       <span class="switcher-label-on">
                                           <i class="fas fa-check"></i></span> <span class="switcher-label-off">
                                           <i class="fas fa-times"></i>
                                       </span>
                                       </label> 
                                   ', PHP_EOL;
                                    $filter = str_replace(' ', '', $grouped_field->BusquedaSelect);
                                    echo "<select data-key-replace='$grouped_field->CampoForaneoValor' $required data-depend='$grouped_field->DependeDe'
                                    data-filter='$filter' id='$grouped_field->Name' name='$grouped_field->Name'
                                    class='remoteinfo form-control'></select>", PHP_EOL;
                                }
                            } else {
                                switch ($type_from_model) {
                                    case 'tinyint(1)':
                                        echo "
                                        <div class='list-group-item d-flex justify-content-between align-items-center'>
                                        
                                        <div>
                                        
                                          <label class='switcher-control switcher-control-success switcher-control-lg'>
                                            <input type='checkbox' class='switcher-input' $required name='$grouped_field->Name'> 
                                            <span class='switcher-indicator'></span> 
                                            <span class='switcher-label-on'><i class='fas fa-check'></i></span> 
                                            <span class='switcher-label-off'><i class='fas fa-times'></i></span>
                                        </label> 
                         
                                        </div>
                                      </div>";

                                        break;

                                    default:
                                        echo "<input type='text' $required data-allow-decimal='false'   data-mask='currency'  $grouped_field->Extras id='$grouped_field->Name' name='$grouped_field->Name'>", PHP_EOL;
                                        break;
                                }
                            }
                        }

                        if (strpos($type_from_model, 'decimal') !== false || strpos($type_from_model, 'number') !== false) {
                            echo "<input type='text' autocomplete='off' data-allow-decimal='true' data-decimal-limit='2' data-mask='currency' $grouped_field->Extras id='$grouped_field->Nale' name='$grouped_field->Name'>", PHP_EOL;
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

            echo "</div>", PHP_EOL;




            // code...
        }

        echo '<div class="row">';
        echo '<div class="col">  <input class="btn btn-primary btn-block" type="submit" value="Enviar" /> </div>';
        echo '<div class="col">  ';
        echo Html::linkAction('', 'Cancelar', 'class="btn btn-danger btn-block"');
        echo '</div>';
        echo "</form>", PHP_EOL;

        // code...
    }


    public static function FromModelToSearchForm($conf_table, $raw_model)
    {
        // get globals scoped variables
        $object_raw_model  = new $raw_model();
        $not_null_fields   = $object_raw_model->not_null;
        $types_raw_model   = $object_raw_model->_data_type;
        $model_primary_key = $object_raw_model->primary_key[0];
        $object_conf_table = new $conf_table();
        $max_common_select_values = SicapFormBuilder::getPropertyKey('max_select_items');



        $conf_table_grouped_by_type = $object_conf_table->find(
            "conditions:  TablaPropietaria ='$raw_model'",
            "order: Orden ASC",
            "group: Type"
        );

        // open form for creation
        echo "<form action='filtrar' method='post' >", PHP_EOL;
        // get grouped controls
        foreach ($conf_table_grouped_by_type  as $group_key => $group_value) {
            echo "<hr>", PHP_EOL;

            $ordering_asc = "order: Orden ASC";
            $grouped_conditions = "conditions:  TablaPropietaria ='$raw_model' AND Type = '$group_value->Type'";
            $grouped_fields = $object_conf_table->find($grouped_conditions, $ordering_asc);

            echo "<div class='form-row'>", PHP_EOL;

            foreach ($grouped_fields  as $grouped_field_key => $grouped_field) {
                try {
                    if ($grouped_field->VisibleEnBusqueda && $grouped_field->Name !== $model_primary_key) {
                        // remove required in "Extra" property
                        $grouped_field->Extras = str_replace('required', '', $grouped_field->Extras);
                        $required = '';
                        echo "<div class='col-6 pt-2 pb-2'>", PHP_EOL;

                        echo "<label>$grouped_field->Label</label>";

                        $type_from_model = $types_raw_model[$grouped_field->Name];

                        if (strpos($type_from_model, 'enum') !== false) {
                            $enum_options = null;
                            preg_match("/^enum\(\'(.*)\'\)$/", $type_from_model, $enum_options);
                            $enum_options = explode("','", $enum_options[1]);

                            echo "<select id='$grouped_field->Name'  name='$grouped_field->Name' $grouped_field->Extras >", PHP_EOL;
                            echo "<option value>Seleccione</option>", PHP_EOL;
                            foreach ($enum_options as $option) {
                                echo "<option  value=\"$option\">$option</option>", PHP_EOL;
                            }
                            echo '<option value>Ninguno</option>';
                            echo "</select>", PHP_EOL;
                        }

                        if (strpos($type_from_model, 'date') !== false) {
                            echo "<input type='date' $grouped_field->Extras  id='$grouped_field->Name' name='$grouped_field->Name' >", PHP_EOL;
                        }

                        if (strpos($type_from_model, 'varchar') !== false) {
                            $max_varchar_len = 30;
                            $varchar_len = 0;
                            preg_match("/^varchar\((.*)\)$/", $type_from_model, $varchar_len);
                            $varchar_len = intval($varchar_len[1]);
                            if ($varchar_len <= 30) {
                                echo "<input type='text' maxlength='$varchar_len' $required $grouped_field->Extras id='$grouped_field->Name' name='$grouped_field->Name' />", PHP_EOL;
                            } else {
                                echo "<textarea maxlength='$varchar_len' $grouped_field->Extras $required id='$grouped_field->Name' name='$grouped_field->Name' ></textarea>", PHP_EOL;
                            }
                        }


                        if (strpos($type_from_model, 'int') !== false) {
                            if ($grouped_field->Type === 'select') {
                                $object_owner_table = (new $grouped_field->TablaForanea);
                                $related_options = $object_owner_table->find();

                                if (count($related_options) <= $max_common_select_values) {
                                    echo "<select data-limited='$grouped_field->Limitar'  id='$grouped_field->Name' $required name='$grouped_field->Name' $grouped_field->Extras >", PHP_EOL;
                                    echo "<option value=0>Seleccione</option>", PHP_EOL;
                                    $from_config_field_to_replace_value = $grouped_field->BusquedaSelect;
                                    $from_config_field_to_replace_key = $grouped_field->CampoForaneoValor;

                                    foreach ($related_options as $related_option_key => $related_option_value) {
                                        echo "<option value='{$related_option_value->$from_config_field_to_replace_key}'>{$related_option_value->$from_config_field_to_replace_value}</option>", PHP_EOL;
                                    }
                                    echo '<option value=0>Ninguno</option>';
                                    echo "</select>", PHP_EOL;
                                } else {

                                    echo '
 
                                       <span class="mr-2" >Parcial</span>
                                       <label class="switcher-control switcher-control-lg mt-1 mb-1">
                                       <input id="ParcialBusqueda' . $grouped_field->Name . '" type="checkbox" class="switcher-input" >
                                       <span class="switcher-indicator"></span>
                                       <span class="switcher-label-on">
                                           <i class="fas fa-check"></i></span> <span class="switcher-label-off">
                                           <i class="fas fa-times"></i>
                                       </span>
                                       </label> 
                                   ', PHP_EOL;
                                    $filter = str_replace(' ', '', $grouped_field->BusquedaSelect);
                                    echo "<select data-limited='$grouped_field->Limitar' data-key-replace='$grouped_field->CampoForaneoValor' $required data-depend='$grouped_field->DependeDe'
                                    data-filter='$filter' id='$grouped_field->Name' name='$grouped_field->Name'
                                    class='remoteinfo form-control'></select>", PHP_EOL;
                                }
                            } else {
                                switch ($type_from_model) {
                                    case 'tinyint(1)':
                                        echo "
                                        <div class='list-group-item d-flex justify-content-between align-items-center'>
                                        
                                        <div>
                                        
                                          <label class='switcher-control switcher-control-success switcher-control-lg'>
                                            <input type='checkbox' class='switcher-input' $required name='$grouped_field->Name'> 
                                            <span class='switcher-indicator'></span> 
                                            <span class='switcher-label-on'><i class='fas fa-check'></i></span> 
                                            <span class='switcher-label-off'><i class='fas fa-times'></i></span>
                                        </label> 
                         
                                        </div>
                                      </div>";

                                        break;

                                    default:
                                        echo "<input type='text' $required data-allow-decimal='false'   data-mask='currency'  $grouped_field->Extras id='$grouped_field->Name' name='$grouped_field->Name'>", PHP_EOL;
                                        break;
                                }
                            }
                        }

                        if (strpos($type_from_model, 'decimal') !== false || strpos($type_from_model, 'number') !== false) {
                            echo "<input type='text' autocomplete='off' data-allow-decimal='true' data-decimal-limit='2' data-mask='currency' $grouped_field->Extras id='$grouped_field->Nale' name='$grouped_field->Name'>", PHP_EOL;
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

            echo "</div>", PHP_EOL;




            // code...
        }

        echo '<div class="row">';
        echo '<div class="col">  <input class="btn btn-primary btn-block" type="submit" value="Enviar" /> </div>';
        echo '<div class="col">  ';
        echo Html::linkAction('', 'Cancelar', 'class="btn btn-danger btn-block"');
        echo '</div>';
        echo "</form>", PHP_EOL;

        // code...
    }


    public static function FromConfigMasterDetail($tablaMaestro, $configuracion = 'configuracionTabla')
    {
        $datosConfiguracionTabla = (new $configuracion)->find("TablaPropietaria LIKE '$tablaMaestro'");
        $esclavos = $datosConfiguracionTabla[0]->Esclavos;
        $dataEsclavo = (new $configuracion)->find("TablaPropietaria LIKE '$esclavos'");
        $page = implode(Router::get('parameters'));
        $data = (new $tablaMaestro)->paginate("page: $page");


        View::partial('sicap_form_builder/masterdetailform', false, array('tablamaestro' => $tablaMaestro, 'dataEsclavo' => $dataEsclavo, 'tablaesclavo' => $esclavos, 'data' => $data, 'ctd' => $datosConfiguracionTabla));
        View::partial('paginators/punbb', false, array('page' => $data, 'url' => Router::get('controller_path') . '/masterdetail'));
    }


    public static function FromConfigDetails($tablaMaestro, $id, $configuracion = 'configuraciontabla')
    {
        $datosConfiguracionTabla = (new $configuracion)->find("TablaPropietaria LIKE '$tablaMaestro'");
        $esclavos = $datosConfiguracionTabla[0]->Esclavos;
        $dataEsclavo = (new $configuracion)->find("TablaPropietaria LIKE '$esclavos'");
        $page = implode(Router::get('parameters'));
        $data = (new $tablaMaestro)->find($id);


        View::partial('sicap_form_builder/masterdetailform', false, array('id' => $id, 'tablamaestro' => $tablaMaestro, 'dataEsclavo' => $dataEsclavo, 'tablaesclavo' => $esclavos, 'data' => $data, 'ctd' => $datosConfiguracionTabla));
        //View::partial('paginators/punbb', false, array('page' => $data ,'url' => Router::get('controller_path').'/masterdetail'));

    }
}