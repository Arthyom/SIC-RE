<?php

    class UsuariosController extends AjaxScaffoldController
    {
        public $model = 'usuarios';
        public $scaffold = 'AutoBuildSicap';
        public $configuracion = 'configuraciontabla';

        public function regenerar(int $usuario_id)
        {
            View::template(null);
            View::select(null);
            echo 'hola';
            Flash::info('Clave regenerada correctamente');
            $respuest = ( new $this->model)->regenerate($usuario_id);
            Redirect::to('');
        }

        public function perfil($id)
        {
            if (!empty($_FILES['image'])) {
                if ((new archivos)->saveImage('image', $id, 'usuarios')) {
                    Flash::info('Informacion actualizada');
                    Redirect::to('');
                }
            }
        }
    }
