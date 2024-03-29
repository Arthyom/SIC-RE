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
class ModelForm
{
    /**
     * Genera un form de un modelo (objeto) automáticamente.
     *
     * @param object $model
     * @param string $action
     */


    public static function create($model, $action = '')
    {
        $model_name = get_class($model);
        if (!$action) {
            $action =  Router::get('controller') .'/'. Router::get('action'). '/'. implode(Router::get('parameters'));
        }

        echo "$action";

        echo '<form action="', PUBLIC_PATH.$action, '" method="post" id="', $model_name, '" class="">' , PHP_EOL;
        $pk = $model->primary_key[0];
        echo '<input id="', $model_name, '_', $pk, '" name="', $model_name, '[', $pk, ']" class="form-control" value="', $model->$pk , '" type="hidden">' , PHP_EOL;

        $fields = array_diff($model->fields, $model->_at, $model->_in, $model->primary_key);

        foreach ($fields as $field) {
            echo '<div class="form-group">' ;
            $tipo = trim(preg_replace('/(\(.*\))/', '', $model->_data_type[$field])); //TODO: recoger tamaño y otros valores
            $alias = $model->get_alias($field);
            $formId = $model_name.'_'.$field;
            $formName = $model_name.'['.$field.']';

            if (in_array($field, $model->not_null)) {
                echo "<label for=\"$formId\" class=\"required\">$alias *</label>" , PHP_EOL;
            } else {
                echo "<label for=\"$formId\">$alias</label>" , PHP_EOL;
            }

            switch ($tipo) {
                case 'tinyint': case 'smallint': case 'mediumint':
                case 'integer': case 'int': case 'bigint':
                case 'float': case 'double': case 'precision':
                case 'real': case 'decimal': case 'numeric':
                case 'year': case 'day': case 'int unsigned': // Números

                    if (strripos($field, '_id', -3)) {
                        echo Form::dbSelect($model_name.'.'.$field, null, null, 'Seleccione', 'class="form-control"', $model->$field);
                        break;
                    }

                    echo "<input id=\"$formId\" type=\"number\" name=\"$formName\" class=\"form-control\" value=\"{$model->$field}\">" , PHP_EOL;
                    break;

                case 'date': // Usar el js de datetime
                    echo "<input id=\"$formId\" type=\"date\" name=\"$formName\"  class=\"form-control\" value=\"{$model->$field}\">" , PHP_EOL;
                    break;

                case 'datetime': case 'timestamp':
                    echo "<input id=\"$formId\" type=\"datetime\" name=\"$formName\"  class=\"form-control\" value=\"{$model->$field}\">" , PHP_EOL;
                    break;

                case 'enum': case 'set': case 'bool':
                    $enumList = explode(',', str_replace("'", '', substr($model->_data_type[$field], 5, (strlen($model->_data_type[$field]) - 6))));
                    echo "<select id=\"$formId\"   class=\"form-control\"  name=\"$formName\" >", PHP_EOL;
                    foreach ($enumList as $value) {
                        echo "<option value=\"{$value}\">$value</option>", PHP_EOL;
                    }
                    echo '</select>', PHP_EOL;
                    break;

                case 'text': case 'mediumtext': case 'longtext': // Usar textarea
                case 'blob': case 'mediumblob': case 'longblob':
                    echo "<textarea  class=\"form-control\" id=\"$formId\" name=\"$formName\">{$model->$field}</textarea>" , PHP_EOL;
                    break;

                default: //text,tinytext,varchar, char,etc se comprobara su tamaño
                    echo "<input  class=\"form-control\" id=\"$formId\" type=\"text\" name=\"$formName\" value=\"{$model->$field}\">" , PHP_EOL;
            }
            echo '</div>';
        }
        echo '<div class="row">';
            echo '<div class="col">  <input class="btn btn-primary btn-block" type="submit" value="Enviar" /> </div>';
            echo '<div class="col">  <a class="btn btn-danger btn-block" hred="/" > Cancelar </a> </div>';
        echo '</div>';
        echo '</form>' , PHP_EOL;
    }
