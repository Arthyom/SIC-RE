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
 * Clase para enviar mensajes a la vista
 *
 * Envio de mensajes de advertencia, éxito, información
 * y errores a la vista.
 * Tambien envia mensajes en la consola, si se usa desde consola.
 *
 * @category   Kumbia
 * @package    Flash
 */
class Flash
{

    /**
     * Visualiza un mensaje flash
     *
     * @param string $name  Para tipo de mensaje y para CSS class='$name'.
     * @param string $text  Mensaje a mostrar
     */
    public static function show($name, $text, $heading='Information', $icon='flag')
    {

        echo '
        
        <div class="alert alert-'.$name.' has-icon alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <div class="alert-icon">
                <span class="oi oi-'.$icon.'"></span>
            </div>
            <h4 class="alert-heading"> '.$heading.' </h4>
            <p class="mb-0"> 
                '. $text .'
            </p>
        </div>';
        
        //if (isset($_SERVER['SERVER_SOFTWARE'])) {
        //echo '<div class="', $name, ' flash">', $text, '</div>', PHP_EOL;
          //  return;
        //}
        // salida CLI
        //echo $name, ': ', strip_tags($text), PHP_EOL;
    }

    /**
     * Visualiza un mensaje de error
     *
     * @param string $text
     */
    public static function error( $message, $class='danger',$heading='Error')
    {
        return self::show($class, $message, $heading);
    }

    /**
     * Visualiza un mensaje de advertencia en pantalla
     *
     * @param string $text
     */
    public static function warning( $message, $class='warning', $heading='Warning')
    {
        return self::show('warning', $text, $heading);
    }

    /**
     * Visualiza informacion en pantalla
     *
     * @param string $text
     */
    public static function info($message, $class='info',  $heading='Info', $icon='check')
    {
        return self::show($class, $text, $heading, $icon);
    }

    /**
     * Visualiza informacion de suceso correcto en pantalla
     *
     * @param string $text
     */
    public static function valid( $message, $class='success', $heading='Correct', $icon='check')
    {
        return self::show($class, $text, $heading, $icon);
    }

}