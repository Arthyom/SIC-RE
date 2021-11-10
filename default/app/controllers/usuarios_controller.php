<?php

    class UsuariosController extends AjaxScaffoldController
    {
        public $model = 'usuarios';
        public $scaffold = 'AutoBuildSicap';
        public $configuracion = 'configuraciontabla';


        public function regenerar(int $usuario_id)
        {
            //  Flash::info('Clave regenerada correctamente');
            $respuest = ( new $this->model)->regenerate($usuario_id);
            Redirect::to('');
        }
    }
