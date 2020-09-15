<?php
    
    class usuarios extends ActiveRecord{	
        protected $source = 'usuarios';

        
        public function validarUsuario( $usr , $pwd )
        {
            // Clave -> nombre del campo que representa a la contraseña
            // Usuario -> nombre del campo que representa al nombre de usuario     
            $auth = new Auth("model", "class: usuarios", "Usuario: $usr", "Clave: $pwd");

            if( $auth->authenticate() ){

                $usrData =  $auth->get_identity() ;
                echo $usrData['IdUsuario'] . ' '. $usr;
                Logger::log('a', [ $usr, $pwd ] );



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



                

           

        

                return true;
            }
            else
            {
                return false; 
            }
        } 

        // verificar si el usuario esta autenticado
        public function estaAutenticado()
        {
            if( Auth::is_valid() ){
            
                return true;
            }
            else{
                Redirect::to('login');
                return false;
            }
        }

        // verificar si el usuario esta autenticado
        public function jsonEstaAutenticado()
        {
            if( Auth::is_valid() ){
                return true;
            }
            else{
                return false;
            }
        }




    }