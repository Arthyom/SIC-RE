<?php
/**
 * @see Controller nuevo controller
 */
require_once CORE_PATH . 'kumbia/controller.php';

/**
 * Controlador para proteger los controladores que heredan
 * Para empezar a crear una convención de seguridad y módulos
 *
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los métodos aquí definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 */
abstract class AdminController extends Controller
{

    final protected function initialize()
    {
    
        // toda la verificacion se hace justo aqui
        return ( new usuarios )->estaAutenticado();
       
         
    }

    public function FunctionName()
    {

    }

    final protected function finalize()
    {

    }

}
