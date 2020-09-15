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
       
        
/*
        if( Auth::is_valid() ){

            $acl = new MyAcl('roles');
            $modulo = $this->module_name;
            $controlador = $this->controller_name;
            $accion = $this->action_name;
            $grupo_id = Auth::get('grupos_id');
            $rol = (new grupos)->find($grupo_id)->rol;

            if( $acl->check($rol, $modulo, $controlador, $accion) ){
                Flash::info("Correcto");
                return true;
            }
            else{
                Flash::info("No tienes privilegios suficientes");
                Redirect::to('/');
                return false;
            }
            return true;

        }


        Redirect::to('login');
        return false;

*/
    }

    public function FunctionName()
    {

    }

    final protected function finalize()
    {

    }

}
