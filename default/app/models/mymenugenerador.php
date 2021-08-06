<?php
    
    class mymenugenerador extends ActiveRecord
    {
        protected $source = 'mymenugenerador';

        public function buscarPorId($id = 0)
        {
            return ( new $this->source )->find($id);
        }

        public function buscarPorIdPadre($parent_id = 0)
        {
            return ( new $this->source )->find("parent_id LIKE $parent_id AND position != 0");
        }
        

        public static function esModeloOControlador($nombre)
        {
            $modeloExistente = APP_PATH.'models/'.$nombre.'.php';
            $controladorExistente = APP_PATH.'controllers/'.$nombre.'_contronller.php';

            if( $nombre )
                return file_exists( $modeloExistente ) || file_exists( $controladorExistente );
            
            return false;
        }
    }
