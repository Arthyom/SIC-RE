<?php
/**
 * @see KumbiaActiveRecord
 */
require_once CORE_PATH.'libs/kumbia_active_record/kumbia_active_record.php';

/**
 * ActiveRecord
 *
 * Clase padre ActiveRecord para añadir tus métodos propios
 *
 * @category Kumbia
 * @package Db
 * @subpackage ActiveRecord
 */
abstract class ActiveFileRecord extends KumbiaActiveRecord
{
    private $path =        "/var/www/".NOMBRE_CAJA."/default/public/img";
    private $noImagePath = "/var/www/".NOMBRE_CAJA."/default/public/assets/images/avatars/unknown-profile.jpg";

    public function saveImage($fieldName, $idRecurso, $recurso)
    {
        $file = Upload::factory($fieldName, 'image');
        $file->setExtensions(array('jpg', 'png', 'gif', 'jpeg'));
        $file->setPath($this->path);
        if ($file->isUploaded()) {
            $nombre = $file->saveRandom();
            $ruta = "{$this->path}/$nombre";

            if (!(new archivos)->exists("idRecurso = $idRecurso")) {
                return (new archivos)->save([
                   'idRecurso'=>$idRecurso,
                   'recurso'=>$recurso,
               'nombre'=>$nombre,
               'ruta'=>$ruta
                ]);
            } else {
                $existingFile = (new archivos())->find_first("conditions: idRecurso = $idRecurso and recurso = '$recurso'");
                return $existingFile->update(
                    [
                       'idRecurso'=>$idRecurso,
                       'recurso'=>$recurso,
                        'nombre'=>$nombre,
                        'ruta'=>$ruta
                    ]
                );
            }
        }
    }

    public function getImage($idRecurso, $recurso)
    {
        $file =  (new archivos)->find_first("conditions: idRecurso = $idRecurso and recurso = '$recurso'");

        if (!$file) {
            $file = new archivos();
            $file->ruta = $this->noImagePath;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $type = $finfo->file($file->ruta);
        return 'data:' . $type . ';base64,' . base64_encode(file_get_contents($file->ruta));
    }
}