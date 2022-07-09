<?php

abstract class ScaffoldFileController extends AdminController
{
    public function image($id)
    {
        $this->id = $id;
        if (isset($_FILES['image']['name'])) {
            $resource = new $this->controller_name();
            if ($resource->saveImage('image', $id)) {
                Flash::info('Imagen actualizada correctamente');
            } else {
                Flash::error('Error al intentar actualizar');
            }
        }
    }

    /*
    public function get( $id )
    {
        View::template(null);
        View::select(null);

        $this->idRecurso = $idRecurso;
        $resource = new $this->controller_name();
        $resource->getImage( $id);

    }*/
}
