<?php

    class LoginController extends AppController{

        public $model = 'usuariorol';

        public function index()
        {
            // seleccionar una plantilla simple, no mostrando el menu
            View::template('layouts/empty');

            // si existen campos con los nombres...
            if( Input::hasPost("usr","pwd") ){
                $usuarioVerificar = new usuarios();

                $usr = Input::post("usr");
                $pwd = Input::post("pwd");

                if( $usuarioVerificar->validarUsuario($usr, $pwd) ){
                    Redirect::to('/');
                }
                else{
                  Flash::error('Credenciales invalidos, por favor verifiquelas');

                  //  Redirect::to('login');
                }

            }
        }





        public function logout()
        {
            Logger::log('o', [Auth::get('Usuario'), Auth::get('Clave')], null, null, null, null, true );
            Auth::destroy_identity();
            Redirect::to('login');
        }
    }
