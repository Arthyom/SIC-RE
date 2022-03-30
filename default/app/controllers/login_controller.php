  <?php

    class LoginController extends AppController
    {
        public $model = 'usuariorol';

        public function index() 
        {
            // seleccionar una plantilla simple, no mostrando el menu
            View::template('layouts/empty');

            // si existen campos con los nombres...
            if (Input::hasPost("usr", "pwd")) {
                $usuarioVerificar = new usuarios();

                $usr = Input::post("usr");
                $pwd = Input::post("pwd");

                if ($usuarioVerificar->validarUsuario($usr, $pwd)) {
                    Redirect::to('/');
                } else {
                    Flash::error('Credenciales invalidos, por favor verifiquelas');

                    //  Redirect::to('login');
                }
            }
        }
            
  



        public function logout()
        {
            Logger::log('o', [Auth::get('Usuario'), Auth::get('Clave')], null, null, null, null, true);
            Auth::destroy_identity();
            Redirect::to('login');
        }

        public function activar()
        {
            View::template('layouts/empty');

            try {
                if ( isset( $_FILES['key']['name'] )) {
                $file = Upload::factory('key', 'file');
                $file->setExtensions(array('key'));
                $path = '/var/www'. PUBLIC_PATH .  'files/upload/' . $file->saveRandom();

                $key_file_resource = fopen($path, "r");
                $content = fread($key_file_resource, filesize($path));

                $string_decoded = trim( base64_decode($content) );
                $dinamic_uid = substr($string_decoded,0 ,13);
                $to_unserialize = str_replace($dinamic_uid,'', $string_decoded);
                //$to_unserialize = str_replace('                ', '', $to_unserialize);


                $unser = unserialize( $to_unserialize );
                $unser->Clave = $dinamic_uid;
                $unser->Activo = 'Si';
                $unser->update();
                $unser->confirmar();


                //Flash::info(  $unser->Activo   );


              Flash::info( 'Se ha enviado un correo de confirmacion con usuario y contraseña. Vuelva al login e inicie sesion' );



            } else {
                                //Flash::error( 'no fijado' );

            }
                //code...
            } catch (\Throwable $th) {
                //throw $th;
                Flash::error($th->getMessage());
            }


        }
    }