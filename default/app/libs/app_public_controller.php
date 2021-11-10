<?php
/**
 * @see Controller nuevo controller
 */
require_once CORE_PATH . 'kumbia/controller.php';

/**
 * Controlador principal que heredan los controladores
 *
 * Todos las controladores heredan de esta clase en un nivel superior
 * por lo tanto los métodos aquií definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 */
abstract class AppPublicController extends Controller
{

    final protected function initialize()
    {


        //if( $this->controller_name != 'login' )
          // return (new usuarios)->estaAutenticado();




    }

    final protected function finalize()
    {

    }

}
