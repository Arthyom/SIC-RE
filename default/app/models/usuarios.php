<?php




    class usuarios extends ActiveRecord
    {
        protected $source = 'usuarios';


        public function validarUsuario($usr, $pwd)
        {
            // Clave -> nombre del campo que representa a la contraseña
            // Usuario -> nombre del campo que representa al nombre de usuario
            $auth = new Auth("model", "class: usuarios", "Usuario: $usr", "Clave: $pwd", "Activo: Si ");

            if ($auth->authenticate()) {
                $usrData =  $auth->get_identity() ;
                Logger::log('a', [ $usr, $pwd ]);


                /*
                $s = (new usuarios)->sql("SELECT Nombre, Lineasporpagina, Tiempoespera, Diascaptacion, Diasprestamo, OrdenNombre, (SELECT Fecha FROM dolares WHERE Fecha=DATE(NOW())) AS FechaDolar, DATE(NOW()) AS Hoy, DAYOFWEEK(now()) AS Dia FROM configuracion");

                $s =  $s->fetch_assoc();


                $_SESSION["RegistrosPorPagina"]=$s["Lineasporpagina"];
                $_SESSION["Tiempoespera"]=$s["Tiempoespera"];
                $_SESSION["Nombredelacaja"]=$s["Nombre"];
                $_SESSION["Diascaptacion"]=$s["DiasCaptacion"];
                $_SESSION["Diasprestamo"]=$s["DiasPrestamo"];
                $_SESSION["miOrdenNombre"]=$s["OrdenNombre"];

                $_SESSION['Usuario'] = $usr;
                $_SESSION["miSessionCaja"]=$usrData["IdUsuario"];
                $_SESSION["mibgUsuarioAct"]="alfredo";
                $_SESSION["mibgClaveUsuar"]="Alfredo2020+";
                $_SESSION["mibgBaseDatos"]="florenciocopia";

                $_POST['Usuario'] = $usr;

                // fijar elementos en la session

                $k = (new usuarios)->sql( "SELECT IdUsuario, Nombre, Nivel, IdGrupoUsuarios, DameGrupoUsuario(IdUsuario) AS Grupo, IdSucursal, (SELECT Nombre FROM sucursales WHERE IdSucursal=us.IdSucursal LIMIT 0,1) AS Sucursal, DATEDIFF(NOW(),FechaClave) AS DiasClave, (SELECT MayusculasActivo FROM configuracion) AS MayusculasActivo, (SELECT DiasCaducaClaveAcceso FROM configuracion) AS DiasCaduca, DATE_FORMAT(NOW(),'%d/%m/%Y') AS Fecha, DATE_FORMAT(NOW(),'%H:%i:%s') AS Hora, us.NominaExterna FROM usuarios AS us WHERE IdUsuario='".$_SESSION["miSessionCaja"]."'  ");
                $k = $k->fetch_assoc();

                echo var_dump( $k );


               // $result2=mysql_query($miCadenaSQL);
               // $row2=mysql_fetch_array($result2);
                $_SESSION["mibgIdGrupo2"]=$k["IdGrupoUsuarios"];
                $_SESSION["mibgIdGrupo"]=$k["Grupo"];
                $_SESSION["mibgIdSucursal"]=$k["IdSucursal"];
                $_SESSION["mibgSucursal"]=$k["Sucursal"];
                $_SESSION["miMayusculasActivo"]=$k["MayusculasActivo"];
                $_SESSION["miNivelUsuario"]=$k["Nivel"];
                $_SESSION["NominaExterna"]=$k["NominaExterna"];
             // $_SESSION["miSessionCaja"]=$row2["IdUsuario"];
                $_SESSION["miSessionNombre"]=$_POST['Usuario'];
                $_SESSION["miSessionNombreUsuario"]=$k['Nombre'];
                $_SESSION["ultimoAcceso"]= date("Y-n-j H:i:s");
                $_SESSION["miFechaActiva"]=$k["Fecha"];
                $_SESSION["miHoraActiva"]=$k["Hora"];
                $_SESSION["miSessionActual"]=date("YnjHis");
                $_SESSION["miArregloNoAplicar"]=array();
              //  $miIp=getIP();


*/
                $_SESSION["miSessionCaja"]=true;







                return true;
            } else {
                return false;
            }
        }

        // verificar si el usuario esta autenticado
        public function estaAutenticado()
        {
            if (Auth::is_valid()) {
                return true;
            } else {
                Redirect::to('login');
                return false;
            }
        }

        // verificar si el usuario esta autenticado
        public function jsonEstaAutenticado()
        {
            if (Auth::is_valid()) {
                return true;
            } else {
                return false;
            }
        }


        //override save method to customice the saving data process
        public function create($data_to_save)
        {
            $base_64_string_data = base64_encode( uniqid() . serialize($data_to_save) );
            $path = dirname(__DIR__).'/temp/'.$data_to_save['Usuario'].'.key';
            $data_to_save['Clave'] = substr($base_64_string_data, 0, 34);
            $to_mail = $data_to_save['CorreoElectronico'];
            $mail = new MarteMailer('allpipiasaaa@gmail.com', '2010_Wflsyo?!');
            if (parent::save($data_to_save)) {
                    $key_file_resource = fopen($path, "w");
                    fwrite($key_file_resource, $base_64_string_data);
                    fclose($key_file_resource);

                     $mail->Subject = 'Envio de archivo KEY, alta de usuario';
                    $mail->Body    = 'Envio de archivo key de contraseña, alta de usuario';
                    $mail->AddAttachment($path, 'keyname.key');
                    $mail->addAddress($to_mail);
                    $mail->Send();

                  
                }
            
        }

        public function regenerate($id)
        {
            $current_model = $this->find($id);

                $base_64_string_data = base64_encode(uniqid().serialize($current_model));
                $path = dirname(__DIR__).'/temp/'.$current_model->Usuario.'.key';
                $current_model->Clave  = substr($base_64_string_data, 0, 34);
                $to_mail = $current_model->CorreoElectronico;
            $mail = new MarteMailer('allpipiasaaa@gmail.com', '2010_Wflsyo?!');

                if ((parent::update((array)$current_model))) {
                    $key_file_resource = fopen($path, "w");
                    fwrite($key_file_resource, $base_64_string_data);
                    fclose($key_file_resource);

                    $mail->AddAttachment($path, 'keyname.key');
                    $mail->Subject = 'Envio de archivo KEY, clave regenerado';
                    $mail->Body    = 'Envio de archivo key de contraseña, clave regenerada';
                                        $mail->addAddress($to_mail);

                    $mail->Send();
                }

          
        }

        public function confirmar()
        {
            
            $mail = new MarteMailer('allpipiasaaa@gmail.com', '2010_Wflsyo?!');
            $mail->addAddress($this->CorreoElectronico);
            $mail->Subject = 'Envio de archivo KEY, confirmacion';
            $mail->Body    = 'Envio de archivo key: contraseña y usuario, creada y validada';
            $mail->Body  = "<h1> Clave: {$this->Clave} Usuario: {$this->Usuario} </h1>";
            $mail->Send();


            // code...
        }
    }