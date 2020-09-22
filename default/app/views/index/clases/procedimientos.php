<?php
if(!isset($_SESSION["NumeroTotalPaginas"]))
$_SESSION["NumeroTotalPaginas"]=0;
function conectarWebService()
{

  /*
$dbserver='localhost';
$dbuser='UsYuririayZXs';
$dbpwd='uSyURIa2020Gto';
$dbase='yuriria';
*/

$dbserver='***nombre_host***';
$dbuser='***nombre_usuario***';
$dbpwd='***nombre_pass***';
$dbase='***nombre_db***';

$link = mysqli_connect ($dbserver, $dbuser, $dbpwd, $dbase) or die ("No me puedo conectar");
}
/*
function conectarWebServiceSiscoop()
{
	try {
						$opciones = array( 'soap_version' => SOAP_1_1,
                         'login' => 'ws_san_apostol',
                         'password' =>'ws_sant_apostol2',
                         'cache_wsdl' => WSDL_CACHE_NONE,
                         'trace' => 1);
						$url="http://172.16.28.85:6969/syc-siscoop/webservice/siscoopAlternativeService?wsdl";
						$client = new SoapClient($url, $opciones);
    } catch (Exception $e)
    {
						echo "error en web service";
    }
      return $client;
}

function ObtenerClabeInterbancaria($IdPersona)
{
	$Socio=null;
	$link=Conectarse();
	$rowSocio=mysqli_fetch_array(mysqli_query($link,"SELECT LPAD(IdSocio,5,'0') AS IdSocio FROM socios WHERE IdPersona='$IdPersona'"));
	$Socio=$rowSocio['IdSocio'];
	$ClabeInterbancaria=null;

	$Array17Digitos=array(6,4,6,			//Banco
												1,8,0,			//Plaza
												1,3,5,7,		//Empresa
												3,7);				//Entidad
	$ArrayFactores=array(3,7,1,			//Factor de Peso Banco
											 3,7,1,			//Factor de Peso Plaza
											 3,7,1,3,		//Factor de Peso Empresa
											 7,1,			//Factor de Peso Entidad
											 3,7,1,3,7);	//Factor de Peso Cuenta
	$ArrayCuenta=array();
	for($i=0;$i<strlen($Socio);$i++)
	{
		array_push($ArrayCuenta,$Socio[$i]);
		array_push($Array17Digitos,$Socio[$i]);
	}
	if(count($Array17Digitos)==17)
	{
		$ArrayProductos=array();
		for($i=0;$i<count($Array17Digitos);$i++)
		{
			array_push($ArrayProductos,($Array17Digitos[$i]*$ArrayFactores[$i]));
		}
		$ArrayModulo10=array();
		for($i=0;$i<count($ArrayProductos);$i++)
		{
			array_push($ArrayModulo10,($ArrayProductos[$i] % 10));
		}
		$Suma=0;
		for($i=0;$i<count($ArrayModulo10);$i++)
		{
			$Suma=$Suma+$ArrayModulo10[$i];
		}
		$Modulo10=$Suma % 10;
		$Modulo10Resta=10-$Modulo10;
		$Modulo10=$Modulo10Resta % 10;
		$DigitoVerificador=$Modulo10;
		array_push($Array17Digitos,$DigitoVerificador);
		for($i=0;$i<count($Array17Digitos);$i++)
		{
			$ClabeInterbancaria.=$Array17Digitos[$i];
		}
		if(strlen($ClabeInterbancaria)<>18)
		{
			$ClabeInterbancaria='ERROR';
		}
	}
	else
	{
		return 'ERROR DE CUENTA';
	}
	return $ClabeInterbancaria;
}
*/
function getBrowser()
{
	$user_agent = $_SERVER['HTTP_USER_AGENT'];

    if(strpos($user_agent, 'MSIE') !== FALSE)
        return 'Internet explorer';
    elseif(strpos($user_agent, 'Edge') !== FALSE) //Microsoft Edge
        return 'Microsoft Edge';
    elseif(strpos($user_agent, 'Trident') !== FALSE) //IE 11
        return 'Internet explorer';
    elseif(strpos($user_agent, 'Opera Mini') !== FALSE)
        return "Opera Mini";
    elseif(strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE)
        return "Opera";
    elseif(strpos($user_agent, 'Firefox') !== FALSE)
        return 'Mozilla Firefox';
    elseif(strpos($user_agent, 'Chrome') !== FALSE)
        return 'Google Chrome';
    elseif(strpos($user_agent, 'Safari') !== FALSE)
        return "Safari";
    else
        return 'No hemos podido detectar su navegador';

}

function OperacionATM($IdATMTarjeta,$Comando,$Dato=0)
{
	$link=Conectarse();

	$cadenasql="SELECT atm.NoTarjeta, atm.IdAtmTarjeta, atm.IdCaptacion, DameNombre(atm.IdPersona,0) AS Nombre, DameDomicilio(atm.IdPersona,2) AS Domicilio, DameDomicilio(atm.IdPersona,4) AS Colonia, DameDomicilio(atm.IdPersona,7) AS Estado, DameDomicilio(atm.IdPersona,8) AS CP, se.Nombre AS Producto, pe.RFC, pe.TelefonoFijo, pe.Celular, pe.TelefonoExtra, suc.Nombre AS Sucursal, pe.CorreoElectronico, DameDomicilio(atm.IdPersona,26) AS Municipio, DameDomicilio(atm.IdPersona,5) AS Ciudad FROM atmtarjetas AS atm INNER JOIN captacion AS ca ON ca.IdCaptacion=atm.IdCaptacion INNER JOIN servicios AS se ON se.IdServicio=ca.IdServicio INNER JOIN personas AS pe ON pe.IdPersona=atm.IdPersona INNER JOIN sucursales AS suc ON suc.IdSucursal=pe.IdSucursal WHERE atm.IdAtmTarjeta=".$IdATMTarjeta;

	$result=mysqli_query($link,$cadenasql);
	$row=mysqli_fetch_array($result);

	$NoTarjeta=$row['NoTarjeta'];
	$IdCaptacion=$row["IdCaptacion"];
	$NombreLargo=substr($row["Nombre"],0,35);
	$NombreCorto=substr($row["Nombre"],0,25);
	$Domicilio=substr($row["Domicilio"],0,30);
	$Colonia=substr($row["Colonia"],0,30);
	$Municipio=substr($row["Municipio"],0,30);
	$Ciudad=substr($row["Ciudad"],0,30);
	$Estado=substr($row["Estado"],0,30);
	$CP=$row["CP"];
	$RFC=substr($row["RFC"],0,21);
	$Telefono=substr($row["TelefonoFijo"],0,10);
	$Celular=substr($row["Celular"],0,10);
	$TelefonoExtra=substr($row["TelefonoExtra"],0,10);
	$Sucursal=substr($row["Sucursal"],0,25);
	$Producto=substr($row["Producto"],0,25);
	$Email=$row["CorreoElectronico"];

	//var_dump($objeto); Para obtener el arreglo de datos enviado por el WSDL.
		try
  {
		/*Catálogo de Comandos (ArrayComando)
			0.- Asignar Cuenta.
			1.- Activar Tarjeta.
			2.- Asignar NIP.
			3.- Consultar Datos.
			4.- Actualizar Datos.
			5.- Asignar Límite Transacción.
			6.- Eliminar Límite Transacción.
			7.- Bloqueo Parcial.
			8.- Desbloqueo Parcial.
			9.- Bloqueo Definitivo.
			10.- Ultimos Movimientos.
			11.- Reemplazo de Tarjeta.
			12.- Depositar Saldo.
			13.- Retirar Saldo.
		*/
    $ArrayComando=array('AsignarCuenta','ActivarTarjeta','AsignarNIP','ConsultarDatos','ActualizarDatos','AsignarLimiteTransaccion','EliminarLimiteTransaccion', 'BloqueoParcial','DesbloqueoParcial','BloqueoDefinitivo','UltimosMovimientos','ReemplazarTarjeta','DepositarSaldo','RetirarSaldo');
		$authorization="*";
    $descripcion="?";
    $codigo="#";
		$param=null;

    $credenciales = array('soap_version' => SOAP_1_1,
													'login' => 'ws_san_apostol',
													'password' =>'=s4N_s&C19',
													'cache_wsdl' => WSDL_CACHE_NONE,
													'trace' => 1);
    $wsdl="http://200.15.1.143:8080/syc/webservice/siscoopAlternativeService?wsdl";
    $client = new SoapClient($wsdl, $credenciales);

    if(in_array($Comando,$ArrayComando)===true)
    {
			$params = new stdClass();
      $params->arg0=$NoTarjeta;
      $resultados = $client->getBalanceQuery($params);

			$result=objectToArray($resultados);
      $saldoatm=$result['return']['availableAmount'];

      if($Comando==$ArrayComando[0])
      {
				$param= new stdClass();
				$param->pan=$NoTarjeta;;
				$param->clientInfo=new stdClass();
				$param->clientInfo->cuenta=$IdCaptacion;
				$param->clientInfo->nombreLargo=utf8_encode(limpiarCaracteres($NombreLargo,0));
				$param->clientInfo->nombreCorto=utf8_encode(limpiarCaracteres($NombreCorto,0));
				$param->clientInfo->direccion=utf8_encode(limpiarCaracteres($Domicilio,0));
				$param->clientInfo->colonia=limpiarCaracteres($Colonia,0);
				$param->clientInfo->entidadFederativa=limpiarCaracteres($Estado,0);
				$param->clientInfo->codigoPostal=$CP;
				$param->clientInfo->rfc=$RFC;
				$param->clientInfo->telefono=$Telefono;
				$param->clientInfo->telefono2=$Celular;
				$param->clientInfo->sucursal=$Sucursal;
				$param->clientInfo->producto=$Producto;

				$resultado = $client->assignmentAccount($param);
      }
			elseif($Comando==$ArrayComando[1])
			{
				$param = new stdClass();
				$param->arg0=$NoTarjeta;

				$resultado=$client->activateCard($param);
				$result=objectToArray($resultado);
				$codigo=$result['return']['code'];

				if($codigo==1)
				{
					$CadenaSQLActivacion="UPDATE atmtarjetas SET FechaActivacion=NOW(), Estado='Activa' WHERE IdAtmTarjeta='$IdATMTarjeta'";
					$resultActivacion=mysqli_query($link,$CadenaSQLActivacion);
				}
			}
			elseif($Comando==$ArrayComando[2])
			{
				$paramx=new stdClass();
				$param->nipRequest=new stdClass();
				$param->nipRequest->arg0=new stdClass();
				$param->nipRequest->arg0->numeroTarjeta=$NoTarjeta;
				$param->nipRequest->arg0->nip=$_POST["NIP"];
				$resultado=$client->asignaNIP($param);
			}
			elseif($Comando==$ArrayComando[3])
			{
				$param=new stdClass();
				$param->arg0=$NoTarjeta;
				$resultado=$client->getBalanceQuery($param);

				$result=objectToArray($resultado);
				return $result;
			}
			elseif($Comando==$ArrayComando[4])
			{
				$param=new stdClass();
				$param->pan=$NoTarjeta;
				$param->Name=$NombreLargo;
				$param->ShortName=limpiarCaracteres($NombreCorto,0);
				$param->email=$Email;
				$param->address=limpiarCaracteres($Domicilio,0);
				$param->colony=limpiarCaracteres($Colonia,0);
				$param->zipCode=$CP;
				$param->municipality=limpiarCaracteres($Municipio,0);
				$param->city=limpiarCaracteres($Ciudad,0);
				$param->state=limpiarCaracteres($Estado,0);
				$param->rfc=$RFC;
				$param->homePhone=$Telefono;
				$param->cellPhone=$Celular;
				$param->workPhone=$TelefonoExtra;

				$resultado=$client->updateDemographicData($param);

				$result=objectToArray($resultado);
				$codigo=$result['return']['code'];

				if($codigo==1)
					echo ConvertirAlert("<script>swal(\"Código: ".$codigo."\",\"Datos actualizados correctamente\\n\\nNo.Tarjeta: $NoTarjeta\\nNombre: $NombreLargo\\nNombre Corto: $NombreCorto\\nCorreo Electrónico: $Email\\nDirección: $Domicilio\\nColonia: $Colonia\\nCódigo Postal: $CP\\nMunicipio: $Municipio\\nCiudad: $Ciudad\\nEstado: $Estado\\nRFC: $RFC\\nTeléfono Fijo: $Telefono\\nCelular: $Celular\\nTeléfono Extra: $TelefonoExtra\",\"success\");</script>");
			}
			elseif($Comando==$ArrayComando[5])
			{
				$param=new stdClass();
				$param->arg0=$NoTarjeta;
				$param->arg1=new stdClass();
				$param->arg1->maxWithdrawalAmount=8000;
				$param->arg1->maxWithdrawalNumber=8000;
				$param->arg1->maxBuysAmount=20000;
				$param->arg1->maxBuysNumber=20000;
				$param->arg1->maxWithdrawalAmountInt=8000;
				$param->arg1->maxWithdrawalNumberInt=8000;

				$resultado=$client->updateIndividualLimits($param);
			}
			elseif($Comando==$ArrayComando[6])
			{
				$param= new stdClass();
				$param->arg0=$NoTarjeta;

				$resultado = $client->deleteIndividualLimits($param);
			}
			elseif($Comando==$ArrayComando[7])
			{
				$param=new stdClass();
				$param->pan=$NoTarjeta;
				$param->operationType=1;

				$resultado=$client->temporaryLock($param);
				$result=objectToArray($resultado);
				$codigo=$result['return']['code'];

				if($codigo==1)
				{
					$CadenaSQLBloqueo="UPDATE atmtarjetas SET FechaCancelacion=NOW(), Estado='Bloqueo parcial' WHERE IdAtmTarjeta='$IdATMTarjeta'";
					$resultBloqueo=mysqli_query($link,$CadenaSQLBloqueo);
				}
			}
			elseif($Comando==$ArrayComando[8])
			{
				$param=new stdClass();
				$param->pan=$NoTarjeta;
				$param->operationType=2;

				$resultado=$client->temporaryLock($param);
				$result=objectToArray($resultado);
				$codigo=$result['return']['code'];

				if($codigo==1)
				{
					$CadenaSQLBloqueo="UPDATE atmtarjetas SET Estado='Activa' WHERE IdAtmTarjeta='$IdATMTarjeta'";
					$resultBloqueo=mysqli_query($link,$CadenaSQLBloqueo);
				}
			}
			elseif($Comando==$ArrayComando[9])
			{
				$param=new stdClass();
				$param->pan=$NoTarjeta;

				$resultado=$client->cardLock($param);
				$result=objectToArray($resultado);
				$codigo=$result['return']['code'];

				if($codigo==1)
				{
					$CadenaSQLBloqueo="UPDATE atmtarjetas SET FechaCancelacion=NOW(), Estado='Bloqueo definitivo' WHERE IdAtmTarjeta='$IdATMTarjeta'";
					$resultBloqueo=mysqli_query($link,$CadenaSQLBloqueo);
				}
			}
			elseif($Comando==$ArrayComando[10])
			{
				$params=new stdClass();
				$params->pan=$NoTarjeta;
				$params->numMaxOfRows=30;
				$params->operationType=3;

				$resultado=$client->getLastestTransactions($params);
				$result=objectToArray($resultado);

				for($i=0;$i<30;$i++)
				{
					$pan[$i]=$result['return']['lastestTransactions'][$i]['pan'];
					$conceptDescription[$i]=$result['return']['lastestTransactions'][$i]['conceptDescription'];
					$respCode[$i]=$result['return']['lastestTransactions'][$i]['respCode'];
					$respCodeDescription[$i]=$result['return']['lastestTransactions'][$i]['respCodeDescription'];
					$amount[$i]=$result['return']['lastestTransactions'][$i]['amount'];
					$authorization[$i]=$result['return']['lastestTransactions'][$i]['authorization'];
					$trxCode[$i]=$result['return']['lastestTransactions'][$i]['trxCode'];
					$trxDescription[$i]=$result['return']['lastestTransactions'][$i]['trxDescription'];
					$acceptorName[$i]=$result['return']['lastestTransactions'][$i]['acceptorName'];
					$transactionDate[$i]=$result['return']['lastestTransactions'][$i]['transactionDate'];
					$availableBalance[$i]=$result['return']['lastestTransactions'][$i]['availableBalance'];
				}

				$link=Conectarse();

				mysqli_query($link,"DELETE FROM movimientostarjetadebito WHERE IdTarjeta='".$row["IdAtmTarjeta"]."'");
				for($i=0;$i<30;$i++)
				{
					$fecha=substr($transactionDate[$i],0,10);
					$tiempo=substr($transactionDate[$i],10,19);

					$cadenaSQL="INSERT INTO movimientostarjetadebito(IdTarjeta, ConceptoDescripcion, CodigoResp, DescripcionCodigoResp, Monto, NumeroAutorizacion, CodigoTrx, DescripcionTrx, NombreAceptor, FechaTransaccion, BalanceDisponible) VALUES (".$row["IdAtmTarjeta"].",'".$conceptDescription[$i]."',".$respCode[$i].",'".$respCodeDescription[$i]."', ".$amount[$i].",".$authorization[$i].",".$trxCode[$i].",'".$trxDescription[$i]."','".$acceptorName[$i]."','".fechaamysql($fecha).$tiempo."',".$availableBalance[$i].")";
					$result=mysqli_query($link,$cadenaSQL);

					$fecha='';
					$tiempo='';
				}
			}
			elseif($Comando==$ArrayComando[11])
			{
				$param=new stdClass();

				//arg0->card
				//arg1->LockedCard

				$param->arg0=str_replace(' ','',$_POST['TarjetaNueva']);
				$param->arg1=$NoTarjeta;

				$resultado=$client->stockCardReplacement($param);
				$result=objectToArray($resultado);

				$codigo=$result['return']['code'];

				if($codigo==1)
				{
					$CadenaSQLBloqueo="UPDATE atmtarjetas SET NoTarjeta='".str_replace(' ','',$_POST['TarjetaNueva'])."' WHERE IdAtmTarjeta='$IdATMTarjeta'";
					$resultBloqueo=mysqli_query($link,$CadenaSQLBloqueo);
				}
			}
			elseif($Comando==$ArrayComando[12])
			{
				if(!empty($Dato) && $Dato>0 && is_numeric($Dato))
				{
					$param->pan=$NoTarjeta;
					$param->amount=$Dato;

					$resultado=$client->loadBalance($param);
				}
			}
			elseif($Comando==$ArrayComando[13])
			{
				if(strlen($Dato)>0 && $Dato>0)
				{
					$param->pan=$NoTarjeta;
					$param->amount=$Dato;

					$resultado=$client->doWithdrawalAccount($param);
				}
			}

			$result=objectToArray($resultado);

			$authorization=$result['return']['authorization'];
			$descripcion=$result['return']['description'];
			$codigo=$result['return']['code'];

			$cadenaSQLRegistro="call CargaRegistroMovimientosATM('$IdATMTarjeta','$Comando','$codigo','$descripcion','$authorization','$saldoatm','".$_SESSION["miSessionCaja"]."')";
      $resultRegistro=mysqli_query($link,$cadenaSQLRegistro);

			if($Comando==$ArrayComando[12] || $Comando==$ArrayComando[13])
			{
				if(!empty($Dato) && $Dato>0 && is_numeric($Dato))
				{
					echo ("<script>swal(\"Código: ".$codigo."\",\"No. de Autorización: ".$authorization."\\nDescripción: ".$descripcion."\",\"success\");</script>");
				}
				else
					echo ("<script>swal(\"ERROR\",\"Monto no especificado para la transacción.\",\"error\");</script>");
			}
			else
			{
				echo ("<script>swal(\"Código: ".$codigo."\",\"No. de Autorización: ".$authorization."\\nDescripción: ".$descripcion."\",\"success\");</script>");
			}
			return $codigo;
    }
    else
    {
			echo ("<script>swal(\"Código: ".$codigo."\",\"ERROR: El comando $Comando no pudo ser procesado.\",\"error\");</script>");
      return $codigo;
    }
  } catch (Exception $e)
  {
		$Error=$e->getMessage();
		echo ("<script>swal(\"ERROR\",\"Código: $codigo\\nNo. de Autorización: $authorization\\nDescripción: $Error\",\"error\");</script>");
		return $codigo;
  }
}

function DispersionATM()
{
	$link=Conectarse();
	$CadenaSQL="SELECT datm.IdDispersionDebitoATM, datm.IdPersona, datm.IdServicio, datm.IdCaptacion, datm.Monto, datm.IdFicha, datm.IdPoliza, datm.FechaAplicacion, datm.FechaDispersion, datm.Aplicada, IFNULL((SELECT IdAtmTarjeta FROM atmtarjetas WHERE IdPersona=datm.IdPersona AND Estado='Activa' AND IdCaptacion=datm.IdCaptacion LIMIT 0,1),0) AS IdAtmTarjeta, IFNULL((SELECT DATE(FechaActivacion) FROM atmtarjetas WHERE IdPersona=datm.IdPersona AND Estado='Activa' AND IdCaptacion=datm.IdCaptacion LIMIT 0,1),DATE('9999-12-31')) AS FechaActivacion FROM dispersiondebitoatm AS datm WHERE Aplicada='No' AND DATE(datm.FechaAplicacion)>=IFNULL((SELECT DATE(FechaActivacion) FROM atmtarjetas WHERE IdPersona=datm.IdPersona AND Estado='Activa' AND IdCaptacion=datm.IdCaptacion LIMIT 0,1),DATE('9999-12-31'))";
	$result=mysqli_query($link,$CadenaSQL);
	while($row=mysqli_fetch_array($result))
	{
		if($row['IdAtmTarjeta']>0)
		{
			$codigo=OperacionATM($row['IdAtmTarjeta'],'DepositarSaldo', $row['Monto']);
			if($codigo==1)
			{
				mysqli_query($link,"UPDATE dispersiondebitoatm SET Observacion='SUCCESS', Aplicada='Si', FechaDispersion=NOW() WHERE IdDispersionDebitoATM='".$row['IdDispersionDebitoATM']."'");
			}
		}
		else
		{
			if($row['FechaAplicacion']<$row['FechaActivacion'])
				mysqli_query($link,"UPDATE dispersiondebitoatm SET Observacion='Omitido por fecha de aplicación menor a la fecha de activación', Aplicada='Si', FechaDispersion='".$row['FechaAplicacion']."' WHERE IdDispersionDebitoATM='".$row['IdDispersionDebitoATM']."'");
			else
				mysqli_query($link,"UPDATE dispersiondebitoatm SET Observacion='Error en tarjeta o cuenta de captacion' WHERE IdDispersionDebitoATM='".$row['IdDispersionDebitoATM']."'");
		}
	}
	echo " <script> swal('Dispersión ATM', 'Se ha aplicado la dispersión de cuentas ATM', 'success'); </script>";
}


function objectToArray( $object )
{
				if( !is_object( $object ) && !is_array( $object ) )
				{
								return $object;
				}
				if( is_object( $object ) )
				{
								$object = get_object_vars( $object );
				}
				return array_map( 'objectToArray', $object );
}

    function recursiva($idPadre, $conection, $level, $titulo) {
	   $sql = "SELECT my.* FROM mymenugenerador as my INNER JOIN grupomenu as gm ON my.id=gm.id WHERE gm.IdGrupoUsuarios='".$_SESSION["mibgIdGrupo2"]."' AND my.parent_id = $idPadre ORDER BY my.position";

    //    $sql = "SELECT my.* FROM mymenugenerador as my INNER JOIN grupomenu as gm ON my.id=gm.id WHERE gm.Grupo='".$_SESSION["mibgIdGrupo"]."' AND my.parent_id = $idPadre ORDER BY my.position";

     //   $sql = "select * from mymenugenerador where parent_id=".$idPadre." order by id";
        $result = $conection->query($sql);
        if ($result->num_rows > 0) {
            echo '<ul class="submenu'.$level.'">
                    <li class="title-menu">'.$titulo.'</li>
                    <li class="go-back">Atras</li>';
            while($row=mysqli_fetch_assoc($result)) {
                if($row["external_link"]=="")
                {
                    echo '<li class="item-submenu" menu="'.$row["id"].'" level="'.($level+1).'"><a href="#">' . $row["menu_title"].'</a>';
                }
                else
                {
                    echo '<li><a href="'.$row["external_link"].'">' . $row["menu_title"].'</a>';
                }
                recursiva($row["id"], $conection, $level+1, $row["menu_title"]);
                echo '</li>';
            }
            echo '</ul>';
        }
    }

	function imprimir($idPadre, $conection) {
 	   $sql = "SELECT my.* FROM mymenugenerador as my INNER JOIN grupomenu as gm ON my.id=gm.id WHERE gm.IdGrupoUsuarios='".$_SESSION["mibgIdGrupo2"]."' AND my.parent_id = $idPadre ORDER BY my.position";

    //     $sql = "select * from mymenugenerador where parent_id=".$idPadre." order by id";

        $result = $conection->query($sql);
        if ($result->num_rows > 0) {
            echo '<ul class="menu">
            <li class="title-menu"><a title="Principal" href="caja.php">Principal</a></li>';
       //     <li class="title-menu">Todas las categorias</li>';
            while($row=mysqli_fetch_assoc($result)) {
                echo '<li class="item-submenu" menu="'.$row["id"].'" level="1"><a href="#">'. $row["menu_title"].'</a>';
                recursiva($row["id"], $conection, 1, $row["menu_title"]);
                echo '</li>';
            }
            echo '<li class="title-menu"><a title="Salir" href="Logout.php">Salir</a></li><font color=#454c53>'.$row["Nivel"]."</li>";
            echo '</ul>';
        }
    }

function ardisoencabezado($mimenuactivo='1',$miencabezadofactura='0')
{
  /*
$dbserver='localhost';
$dbuser='UsYuririayZXs';
$dbpwd='uSyURIa2020Gto';
$dbase='yuriria';
*/

$dbserver='***nombre_host***';
$dbuser='***nombre_user***';
$dbpwd='***nombre_pass***';
$dbase='***nombre_db***';


    $miVariableArchivo=substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],'/')+1);

$link=Conectarse();
    $miConsultaSQL="SELECT Tiempo FROM mymenugenerador WHERE external_link='".$miVariableArchivo."' ";
    $result2=mysqli_query($link,$miConsultaSQL);
    $rowMeta=mysqli_fetch_array($result2);

    if ( mysqli_num_rows($result2)<1 || $rowMeta['Tiempo']<5)
      $rowMeta['Tiempo']=5;


echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" >
        <head>
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimun-scale=1.0">
        <title>SICAP - Caja Popular Yuriria</title>
          <link rel="shortcut icon" href="estilos/imagenes/sicap_logo_pila.png" />
          <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
          <meta http-equiv="refresh" content="'.($rowMeta['Tiempo']*60).'"/>

          <script src="clases/validaciones.js" type="text/javascript"></script>
          <script src="clases/shortcut.js" type="text/javascript"></script>
          <script src="clases/calendario.js" type="text/javascript"></script>
          <script src="../../../../sweetalert/dist/sweetalert.min.js"></script>
          <link href="../../../../sweetalert/dist/sweetalert.css" rel="stylesheet">
          <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
          <script src="estilos/js/jquery-3.4.1.js"></script>

          <script src="clases/cfdinomina.js" language="javascript"/></script>
					<script src="clases/jquery.maskedinput.js" type="text/javascript"></script>
          <link rel="stylesheet" type="text/css" href="../../../../sweetalert/dist/sweetalert.css">';

//             @import url(estilos/menus.css);

      if ($mimenuactivo=='2')
        echo '<script src="clases/js_quicktags.js" type="text/javascript"></script>';

     if (isset($miencabezadofactura) && $miencabezadofactura=='1')
        {
  ?>
		<script src="cfdi/cfdi.js" language="javascript"/></script>
		<script language="javascript"/>
			function facturar()
			{
				if(cfdi.facturar(1))	// Factura la operacion numero 1.
					document.getElementById('txtIdFolio').value = cfdi.data.idFolio;
   		}
			function global()
			{
				if(cfdi.global()) // Genera la Factura global
   			document.getElementById('txtIdFolio').value = cfdi.data.idFolio;

			}

			function pdf(idFolio)
			{
				cfdi.pdf(idFolio);	// Abre el PDF del CFDI del ID de folio ingresado de la tabla cfdifolios.
			}
			function enviar(idFolio)
			{
				cfdi.enviar(idFolio);	// Envia el XML y PDF del CFDI del ID de folio ingresado de la tabla cfdifolios. Se enviarÃÂ¡ siempre y cuando la funcion cfdi_obtener_email retorne un e-mail para el nÃÂºmero de operacion de ese ID de folio.
			}
			function cancelar(idFolio)
			{
				cfdi.cancelar(idFolio);   // Cancela el ID de folio ingresado de la tabla cfdifolios. Una vez cancelado inserta un registro en la tabla cfdicancelaciones con el UUID cancelado, asÃÂ­ como el Acuse, Digest y Certificado.
			}
		</script>
 <?php

        	}

     //if(isset($_SESSION['Focus']) && $_SESSION['Focus']!="")
     //echo ' </head> <body id="contenedor" onload="document.getElementById("'.$_SESSION['Focus'].'").focus(); document.getElementById("'.$_SESSION['Focus'].'").value="";>';
     //else
     echo "</head><body id=\"contenedor\" onload=\"document.getElementById('".$_SESSION['Focus']."').focus();\">";

  if ($mimenuactivo=='1'){

    $micadenasqlx1="SELECT ConfiguracionMenu FROM configuracion  ";
    $resultx1=mysqli_query($link,$micadenasqlx1);
    $rowx1=mysqli_fetch_array($resultx1);

    if ($rowx1["ConfiguracionMenu"]==1)
    {
    	 echo ' <link rel="stylesheet" href="estilos/css/style5Azul.css"> ';
    	 echo ' <link rel="stylesheet" href="estilos/css/font-awesome1.css"> ';
    	 echo '<script src="estilos/js/main1.js"></script>';
       echo '<header>';
       echo '<span id="button-menu" class="fa fa-caret-down"></span>';
       echo '<nav class="navegacion">';
       imprimir(0, $link);
       echo '</nav></header>';
    }
    elseif ($rowx1["ConfiguracionMenu"]==2)
    {
    	 echo ' <link rel="stylesheet" href="estilos/css/style2.css"> ';
    	 echo ' <link rel="stylesheet" href="estilos/css/font-awesome2.css"> ';
       echo '<script src="estilos/js/main2.js"></script>';
       echo '<header>';
       echo '<span id="button-menu" class="fa fa-caret-down"></span>';
       echo '<nav class="navegacion">';
       imprimir(0, $link);
       echo '</nav></header>';
    }
    else
    {
    	echo ' <link rel="stylesheet" href="estilos/css/style0.css"> ';
    	echo '  <style type="text/css" title="currentStyle" media="screen">
            @import url(estilos/css/style0.css);
            @import url(estilos/css/menu0.css);
          </style>';
    	echo ' <script src="clases/adxmenu.js" type="text/javascript"></script>' ;
  //  	echo ' <link rel="stylesheet" href="estilos/css/menus0.css">   ';
     include("mymenugen_class.php"); // incluir la clase de menus

     $myMenu = new myMenuObject;

     echo '<div id="cuerpoencabezado"><div id="cuerpoencabezadoizquierda"><img src="estilos/imagenes/sicap_logo.png" height="35"  alt="SICAP" title="Sistema Integral para Cajas de Ahorro y Prestamo"  border="0"/></div>';

  //  echo '<img src="estilos/imagenes/fondoencabezado.png" height="35" width="70%" alt="SIPCAP" title="Sistema Integral para Cajas de Ahorro y Prestamo"  border="0"/>';
     echo '<div id="cuerpoencabezadoderecha"><img id="milogoempresa" src="estilos/imagenes/logoempresa.png" height="35"  alt="CAJA" title="CAJA"  border="0"/></div> ';

     echo '<div id="cuerpoencabezadocentrado" align=center><h1>';
        if ($mimenuactivo<>'3')
   {
      $micadenasql="SELECT Mensaje FROM mensajesmotivacion WHERE Activo=1";
      $result=mysqli_query($link,$micadenasql);
      $row=mysqli_fetch_array($result);

      $Mensaje=$row['Mensaje'];

      $Mensaje=str_replace('Ñ', '&Ntilde;', $Mensaje);
	  $Mensaje=str_replace('ñ', '&ntilde;', $Mensaje);
	  $Mensaje=str_replace('Á', '&Aacute;', $Mensaje);
	  $Mensaje=str_replace('á', '&aacute;', $Mensaje);
	  $Mensaje=str_replace('É', '&Eacute;', $Mensaje);
	  $Mensaje=str_replace('é', '&eacute;', $Mensaje);
	  $Mensaje=str_replace('Í', '&Iacute;', $Mensaje);
	  $Mensaje=str_replace('í', '&iacute;', $Mensaje);
	  $Mensaje=str_replace('Ó', '&Oacute;', $Mensaje);
	  $Mensaje=str_replace('ó', '&oacute;', $Mensaje);
	  $Mensaje=str_replace('Ú', '&Uacute;', $Mensaje);
	  $Mensaje=str_replace('ú', '&uacute;', $Mensaje);

      echo $Mensaje;
    }

     echo '</h1></div></div>';
     echo '<div id="Menu" align=center>';
     echo '<ul class="menu">';
     echo '<li><span class="menuImg"></span>';
     echo '<a title="Principal" href="caja.php">Principal</a></li>';
     echo $myMenu->generateMenuStructure();
     echo '<li><span class="menuImg"><img src="imagenes/salir.gif" /></span>';
     echo '<a title="Salir" href="Logout.php">Salir</a></li><font color=#454c53>'.$row["Nivel"]."";
     echo '</font></ul>';
     echo "</div>\r";
   }

   $micadenasql="SELECT COUNT(*) AS Cuantos FROM chat AS ch, usuarios AS us WHERE ch.Destino IN ('".$_SESSION["miSessionNombre"]."','Todos') AND us.Usuario='".$_SESSION["miSessionNombre"]."' AND ch.IdChat>us.IdChat ";

 //  echo $micadenasql;

   $result=mysqli_query($link,$micadenasql);
   $row=mysqli_fetch_array($result);
   if ($row["Cuantos"]>0)
   {

    echo " <script> swal('Atencion!!', 'Tiene mensajes pendientes por leer Ayuda -> Chat para leerlos..!', 'info'); </script>";
      //echo "<script>alert('Tiene mensajes pendientes de leer Ayuda -> Chat para leerlos ...');</script>"  ;
   }
 //  mysqli_free_result($result);
  // mysqli_close($link);

  }

  if ($_SESSION["miDispositivo"]==0 && ($rowx1["ConfiguracionMenu"]==1 || $rowx1["ConfiguracionMenu"]==2))
  {
    echo '<div id="cuerpoencabezado"><div id="cuerpoencabezadoizquierda"><img src="estilos/imagenes/sicap_logo.png" height="35"  alt="SICAP" title="Sistema Integral para Cajas de Ahorro y Prestamo"  border="0"/></div>';

  //  echo '<img src="estilos/imagenes/fondoencabezado.png" height="35" width="70%" alt="SIPCAP" title="Sistema Integral para Cajas de Ahorro y Prestamo"  border="0"/>';
    echo '<div id="cuerpoencabezadoderecha"><img id="milogoempresa" src="estilos/imagenes/logoempresa.png" height="35"  alt="CAJA" title="CAJA"  border="0"/></div> ';

   echo '<div id="cuerpoencabezadocentrado" align=center><h1>';
   if ($mimenuactivo<>'3')
   {
      $micadenasql="SELECT Mensaje FROM mensajesmotivacion WHERE Activo=1";
      $result=mysqli_query($link,$micadenasql);
      $row=mysqli_fetch_array($result);

      $Mensaje=$row['Mensaje'];

      $Mensaje=str_replace('Ñ', '&Ntilde;', $Mensaje);
	  $Mensaje=str_replace('ñ', '&ntilde;', $Mensaje);
	  $Mensaje=str_replace('Á', '&Aacute;', $Mensaje);
	  $Mensaje=str_replace('á', '&aacute;', $Mensaje);
	  $Mensaje=str_replace('É', '&Eacute;', $Mensaje);
	  $Mensaje=str_replace('é', '&eacute;', $Mensaje);
	  $Mensaje=str_replace('Í', '&Iacute;', $Mensaje);
	  $Mensaje=str_replace('í', '&iacute;', $Mensaje);
	  $Mensaje=str_replace('Ó', '&Oacute;', $Mensaje);
	  $Mensaje=str_replace('ó', '&oacute;', $Mensaje);
	  $Mensaje=str_replace('Ú', '&Uacute;', $Mensaje);
	  $Mensaje=str_replace('ú', '&uacute;', $Mensaje);

      echo $Mensaje;
    }
    echo '</h1></div></div>';
  }

   $link=Conectarse();
  $micadenasql="SELECT Nivel FROM configuracion";

   $result=mysqli_query($link,$micadenasql);
   $row=mysqli_fetch_array($result);


  echo "<div id=\"contenedor\"> \r";

  }

if ($_SESSION["NumeroTotalPaginas"]>1)
   {
				if(isset($_GET['PagInicio']))
					$_SESSION["PaginaActiaActual"]=1;
				if(isset($_GET['PagFin']))
					$_SESSION["PaginaActiaActual"]=$_SESSION["NumeroTotalPaginas"];
				if(isset($_POST['miPaginaIr']))
					$_SESSION["PaginaActiaActual"]=$_POST['miPaginaIr'];
			}
 // ardisopie: Pie de pagina
 function ardisopie()
 {
 echo " \r </div> </body></html> ";
 }

  function ConvertirAlert($Mensaje)
   {
      $Mensaje=str_replace('Ñ', 'u00d1', $Mensaje);
      $Mensaje=str_replace('ñ', 'u00f1', $Mensaje);
      $Mensaje=str_replace('Á', 'u00c1', $Mensaje);
      $Mensaje=str_replace('á', 'u00e1', $Mensaje);
      $Mensaje=str_replace('É', 'u00c9', $Mensaje);
      $Mensaje=str_replace('é', 'u00e9', $Mensaje);
      $Mensaje=str_replace('Í', 'u00cd', $Mensaje);
      $Mensaje=str_replace('í', 'u00ed', $Mensaje);
      $Mensaje=str_replace('Ó', 'u00d3', $Mensaje);
      $Mensaje=str_replace('ó', 'u00f3', $Mensaje);
      $Mensaje=str_replace('Ú', 'u00da', $Mensaje);
      $Mensaje=str_replace('ú', 'u00fa', $Mensaje);

      return ($Mensaje);
    }

    function AfinarDomicilio($Domicilio)
   {
      $Domicilio=str_replace('AVENIDA', 'AV.', $Domicilio);
      $Domicilio=str_replace('Avenida', 'Av.', $Domicilio);
      $Domicilio=str_replace('avenida', 'av.', $Domicilio);

      $Domicilio=str_replace('CENTRO', '', $Domicilio);
      $Domicilio=str_replace('Centro', '', $Domicilio);
      $Domicilio=str_replace('centro', '', $Domicilio);

      $Domicilio=str_replace('SEPTIEMBRE', 'SEP.', $Domicilio);
      $Domicilio=str_replace('Septiembre', 'Sep.', $Domicilio);
      $Domicilio=str_replace('septiembre', 'sep.', $Domicilio);

      $Domicilio=str_replace('FEBRERO', 'FEB.', $Domicilio);
      $Domicilio=str_replace('Febrero', 'Feb.', $Domicilio);
      $Domicilio=str_replace('febrero', 'feb.', $Domicilio);

      $Domicilio=str_replace('DICIEMBRE', 'DIC.', $Domicilio);
      $Domicilio=str_replace('Diciembre', 'Dic.', $Domicilio);
      $Domicilio=str_replace('diciembre', 'dic.', $Domicilio);

      $Domicilio=str_replace('PRIVADA', 'PRIV.', $Domicilio);
      $Domicilio=str_replace('Privada', 'Priv.', $Domicilio);
      $Domicilio=str_replace('privada', 'priv.', $Domicilio);

      $Domicilio=str_replace('TRABAJO', 'TRAB.', $Domicilio);
      $Domicilio=str_replace('Trabajo', 'Trab.', $Domicilio);
      $Domicilio=str_replace('trabajo', 'trab.', $Domicilio);

      $Domicilio=str_replace('    ', ' ', $Domicilio);
      $Domicilio=str_replace('   ', ' ', $Domicilio);
      $Domicilio=str_replace('  ', ' ', $Domicilio);

      $Domicilio=str_replace('NO.', '#', $Domicilio);
      $Domicilio=str_replace('No.', '#', $Domicilio);
      $Domicilio=str_replace('no.', '#', $Domicilio);

      $Domicilio=str_replace('NIÑOS HEROES', 'N. HEROES', $Domicilio);
      $Domicilio=str_replace('Niños Heroes', 'N. Heroes', $Domicilio);
      $Domicilio=str_replace('niños heroes', 'n. heroes', $Domicilio);

      $Domicilio=str_replace('IGNACIO', 'IGN.', $Domicilio);
      $Domicilio=str_replace('Ignacio', 'Ign.', $Domicilio);
      $Domicilio=str_replace('ignacio', 'ign.', $Domicilio);

      $Domicilio=str_replace('REVOLUCION', 'REV.', $Domicilio);
      $Domicilio=str_replace('Revolucion', 'Rev.', $Domicilio);
      $Domicilio=str_replace('revolucion', 'rev.', $Domicilio);

      $Domicilio=str_replace('MONTE DE LOS JUAREZ', 'MONTE JUAREZ', $Domicilio);
      $Domicilio=str_replace('Monte de los Juarez', 'Monte Juarez.', $Domicilio);
      $Domicilio=str_replace('monte de los juarez', 'monte juarez.', $Domicilio);

      $Domicilio=str_replace('BENITO JUAREZ ', 'B. JUAREZ ', $Domicilio);
      $Domicilio=str_replace('Benito Juarez', 'B. Juarez ', $Domicilio);
      $Domicilio=str_replace('benito juarez', 'b. juarez ', $Domicilio);

      $Domicilio=str_replace('PROLONGACION ', 'PROL. ', $Domicilio);
      $Domicilio=str_replace('Prolongacion', 'Prol. ', $Domicilio);
      $Domicilio=str_replace('prolongacion', 'prol. ', $Domicilio);

      $Domicilio=str_replace('VICENTE ', 'VTE. ', $Domicilio);
      $Domicilio=str_replace('Vicente', 'Vte. ', $Domicilio);
      $Domicilio=str_replace('vicente', 'vte. ', $Domicilio);

      $Domicilio=str_replace('VICTORIANO HUERTA ', 'V. HUERTA ', $Domicilio);
      $Domicilio=str_replace('Victoriano Huerta ', 'V. Huerta ', $Domicilio);
      $Domicilio=str_replace('victoriano huerta ', 'v. huerta ', $Domicilio);


      $Domicilio=str_replace('CURUMBATIO MOROLEON', 'CURUMBATIO ', $Domicilio);
      $Domicilio=str_replace('Curumbatio Moroleon', 'Curumbatio ', $Domicilio);
      $Domicilio=str_replace('curumbatio moroleon', 'curumbatio ', $Domicilio);

      $Domicilio=str_replace('LOMAS DEL PEDREGAL MOROLEON', 'LOMAS DEL PEDREGAL ', $Domicilio);
      $Domicilio=str_replace('Lomas del Pedregal Moroleon', 'Lomas del Pedregal ', $Domicilio);
      $Domicilio=str_replace('lomas del pedregal moroleon', 'lomas del pedregal ', $Domicilio);


      $Domicilio=str_replace('FRANCISCO I MADERO', 'FCO. I MADERO ', $Domicilio);
      $Domicilio=str_replace('Francisco I Madero', 'Fco. I Madero ', $Domicilio);
      $Domicilio=str_replace('francisco I madero', 'fco. I madero ', $Domicilio);

      $Domicilio=str_replace('EL RODEO DE LAS ROSAS', 'EL RODEO ', $Domicilio);
      $Domicilio=str_replace('El Rodeo de las Rosas', 'El Rodeo ', $Domicilio);
      $Domicilio=str_replace('el rodeo de las rosas', 'el rodeo ', $Domicilio);

      $Domicilio=str_replace('LAZARO CARDENAS PURUANDIRO', 'LAZARO CARDENAS ', $Domicilio);
      $Domicilio=str_replace('Lazaro Cardenas Puruandiro', 'Lazaro Cardenas ', $Domicilio);
      $Domicilio=str_replace('lazaro cardenas puruandiro', 'lazaro cardenas ', $Domicilio);

      $Domicilio=str_replace('INDEPENDENCIA ', 'INDEP. ', $Domicilio);
      $Domicilio=str_replace('Independencia ', 'Indep. ', $Domicilio);
      $Domicilio=str_replace('independencia ', 'indep. ', $Domicilio);

        $Domicilio=str_replace('FRANCISCO VILLA ', 'FCO. VILLA ', $Domicilio);
      $Domicilio=str_replace('Francisco Villa  ', 'Fco. Villa ', $Domicilio);
      $Domicilio=str_replace('francisco villa ', 'fco. villa ', $Domicilio);



      return $Domicilio;
    }

    function AfinarOcupacion($Ocupacion)
   {
      $Ocupacion=str_replace('PLASTICOS', 'PLAST.', $Ocupacion);
      $Ocupacion=str_replace('Plasticos', 'Plast.', $Ocupacion);
      $Ocupacion=str_replace('plasticos', 'plast.', $Ocupacion);

      $Ocupacion=str_replace('DESECHABLES', 'DESECH.', $Ocupacion);
      $Ocupacion=str_replace('Desechables', 'Desech.', $Ocupacion);
      $Ocupacion=str_replace('desechables', 'desech.', $Ocupacion);

      $Ocupacion=str_replace('COMERCIANTE DE VENTA ', 'CMTE. DE VENTA ', $Ocupacion);
      $Ocupacion=str_replace('Comerciante', 'Cmte. de Venta', $Ocupacion);
      $Ocupacion=str_replace('comerciante', 'cmte. de venta', $Ocupacion);


      return $Ocupacion;
    }


  function ConvertirHTML($Mensaje)
   {
      $Mensaje=str_replace('Ñ', '&Ntilde;', $Mensaje);
      $Mensaje=str_replace('ñ', '&ntilde;', $Mensaje);
      $Mensaje=str_replace('Á', '&Aacute;', $Mensaje);
      $Mensaje=str_replace('á', '&aacute;', $Mensaje);
      $Mensaje=str_replace('É', '&Eacute;', $Mensaje);
      $Mensaje=str_replace('é', '&eacute;', $Mensaje);
      $Mensaje=str_replace('Í', '&Iacute;', $Mensaje);
      $Mensaje=str_replace('í', '&iacute;', $Mensaje);
      $Mensaje=str_replace('Ó', '&Oacute;', $Mensaje);
      $Mensaje=str_replace('ó', '&oacute;', $Mensaje);
      $Mensaje=str_replace('Ú', '&Uacute;', $Mensaje);
      $Mensaje=str_replace('ú', '&uacute;', $Mensaje);

      return ($Mensaje);
    }
    function ConvertirPDF($Mensaje)
   {
      $Mensaje=str_replace('Ñ', 'ñ', $Mensaje);
      $Mensaje=str_replace('ñ', 'Ñ', $Mensaje);
      $Mensaje=str_replace('Á', 'á', $Mensaje);
      $Mensaje=str_replace('á', 'Á', $Mensaje);
      $Mensaje=str_replace('É', 'é', $Mensaje);
      $Mensaje=str_replace('é', 'É', $Mensaje);
      $Mensaje=str_replace('Í', 'í', $Mensaje);
      $Mensaje=str_replace('í', 'Í', $Mensaje);
      $Mensaje=str_replace('Ó', 'ó', $Mensaje);
      $Mensaje=str_replace('ó', 'Ó', $Mensaje);
      $Mensaje=str_replace('Ú', 'ú', $Mensaje);
      $Mensaje=str_replace('ú', 'Ú', $Mensaje);

      return utf8_decode($Mensaje);
    }

    function FormatoSQL($Sentencia)
   {
      $Sentencia=str_replace('SELECT ', '<font color="#265E16"><b>SELECT </b></font>', $Sentencia);
      $Sentencia=str_replace('select ', '<font color="#265E16">select <b></b></font>', $Sentencia);
      $Sentencia=str_replace(' FROM ', '<font color="#265E16"><b> FROM </b></font>', $Sentencia);
      $Sentencia=str_replace(' from ', '<font color="#265E16"><b> from </b></font>', $Sentencia);
      $Sentencia=str_replace(' WHERE ', '<font color="#265E16"><b> WHERE </b></font>', $Sentencia);
      $Sentencia=str_replace(' where ', '<font color="#265E16"><b> where </b></font>', $Sentencia);
      $Sentencia=str_replace(' AS ', '<font color="#265E16"><b> AS </b></font>', $Sentencia);
      $Sentencia=str_replace(' as ', '<font color="#265E16"><b> as </b></font>', $Sentencia);
      $Sentencia=str_replace(' IN', '<font color="#265E16"><b> IN </b></font>', $Sentencia);
      $Sentencia=str_replace(' in', '<font color="#265E16"><b> in </b></font>', $Sentencia);
      $Sentencia=str_replace(' NOT ', '<font color="#265E16"><b> NOT </b></font>', $Sentencia);
      $Sentencia=str_replace(' not ', '<font color="#265E16"><b> not </b></font>', $Sentencia);
      $Sentencia=str_replace(' INNER ', '<font color="#265E16"><b> INNER </b></font>', $Sentencia);
      $Sentencia=str_replace(' inner ', '<font color="#265E16"><b> inner </b></font>', $Sentencia);
      $Sentencia=str_replace(' JOIN ', '<font color="#265E16"><b> JOIN </b></font>', $Sentencia);
      $Sentencia=str_replace(' join ', '<font color="#265E16"><b> join </b></font>', $Sentencia);
      $Sentencia=str_replace(' ON ', '<font color="#265E16"><b> ON </b></font>', $Sentencia);
      $Sentencia=str_replace(' on ', '<font color="#265E16"><b> on </b></font>', $Sentencia);
      $Sentencia=str_replace('(', '<font color="#000000"><b>(</b></font>', $Sentencia);
      $Sentencia=str_replace(')', '<font color="#000000"><b>)</b></font>', $Sentencia);
      $Sentencia=str_replace(' AND ', '<font color="#265E16"><b> AND </b></font>', $Sentencia);
      $Sentencia=str_replace(' and ', '<font color="#265E16"><b> and </b></font>', $Sentencia);
      $Sentencia=str_replace(' OR ', '<font color="#265E16"><b> OR </b></font>', $Sentencia);
      $Sentencia=str_replace(' or ', '<font color="#265E16"><b> or </b></font>', $Sentencia);
      $Sentencia=str_replace(' IS ', '<font color="#265E16"><b> IS </b></font>', $Sentencia);
      $Sentencia=str_replace(' is ', '<font color="#265E16"><b> is </b></font>', $Sentencia);
      $Sentencia=str_replace('NULL', '<font color="#265E16"><b> NULL</b></font>', $Sentencia);
      $Sentencia=str_replace('null', '<font color="#265E16"><b> null</b></font>', $Sentencia);
      $Sentencia=str_replace('IFNULL', '<font color="#265E16"><b> IFNULL</b></font>', $Sentencia);
      $Sentencia=str_replace('ifnull', '<font color="#265E16"><b> ifnull</b></font>', $Sentencia);
      $Sentencia=str_replace('LEFT', '<font color="#265E16"><b> LEFT </b></font>', $Sentencia);
      $Sentencia=str_replace('left', '<font color="#265E16"><b> left </b></font>', $Sentencia);

      return utf8_decode($Sentencia);
    }

    function Espacios($Cantidad)
   {
      $Mensaje="";
      $Inicial=1;
      while($Inicial<=$Cantidad)
      {
	$Mensaje=$Mensaje."&nbsp;";
	$Inicial=$Inicial+1;
      }

      return utf8_decode($Mensaje);
    }

function EliminaAcentos($linea)
 {
         $linea=str_replace('Á','A', $linea);
         $linea=str_replace('á','a', $linea);
         $linea=str_replace('É','E', $linea);
         $linea=str_replace('é','e', $linea);
         $linea=str_replace('Í','I', $linea);
         $linea=str_replace('í','i', $linea);
         $linea=str_replace('Ó','O', $linea);
         $linea=str_replace('ó','o', $linea);
         $linea=str_replace('Ú','U', $linea);
         $linea=str_replace('ú','u', $linea);
				 $linea=str_replace('Ñ','N', $linea);
				 $linea=str_replace('ñ','n', $linea);

         return $linea;
 }

 function sanear_string($string)
{

    $string = trim($string);

    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );

    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );

    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );

    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );

    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );

    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );
 /*
    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
        array("\", "¨", "º", "-", "~",
             "#", "@", "|", "!", """,
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "<code>", "]",
             "+", "}", "{", "¨", "´",
             ">", "< ", ";", ",", ":",
             ".", " "),
        '',
        $string
    );
 */

    return $string;
}

function creaencabezapdf($pdf,$misLineasAumentar=1)
{
	$imprimefecha=$misLineasAumentar;
   if (isset($_SESSION["miMasCaracteresLegal"]) && $misLineasAumentar==1)
     $misLineasAumentar=$_SESSION["miMasCaracteresLegal"];

   $pdf->SetY(6);
   $pdf->Image('estilos/imagenes/logoempresa3.jpg',10,9,58);
   $pdf->SetFont('times','',8);
   $pdf->SetY(15);
   $pdf->SetX(160+$misLineasAumentar);
   //if ($_SESSION["misNumeroPaginasGlobal"]>0  && $_SESSION["misFechaPaginasGlobal"]>0 && $imprimefecha!=0)
    // $pdf->JLCell("PAGINA: ".$pdf->PageNo()."/{nb}",30,'l');

   $link=Conectarse();
   $miCadenaSWQLenca="SELECT Nombre, Direccion, RFC, TRIM(Telefonos) AS Telefonos, PaginaInternet  FROM configuracion ";
   $resultenca=mysqli_query($link,$miCadenaSWQLenca);
   $rowenca=mysqli_fetch_array($resultenca);

    $pdf->SetFont('times','',8);
   $pdf->SetY(10);
   $pdf->SetX(30);
   $pdf->JLCell(utf8_decode($rowenca["Nombre"]),170+$misLineasAumentar,'c');
   $pdf->SetY(16);
   $pdf->SetX(161+$misLineasAumentar);
 //  if ($_SESSION["misFechaPaginasGlobal"]>0 && $imprimefecha!=0)
 //    $pdf->JLCell("FECHA: ".$_SESSION["miFechaActiva"],30,'l');
   $pdf->SetY(13);
   $pdf->SetX(30);
   $pdf->JLCell($rowenca["Direccion"],170+$misLineasAumentar,'c');
   $pdf->SetY(16);
   $pdf->SetX(30);
   $pdf->JLCell("RFC: ".$rowenca["RFC"].", TEL: ".$rowenca["Telefonos"]."  ".$rowenca["PaginaInternet"],170+$misLineasAumentar,'c');
   $pdf->SetY(19);
   $pdf->SetX(162+$misLineasAumentar);
  // if ($_SESSION["misFechaPaginasGlobal"]>0 && $imprimefecha!=0)
 //    $pdf->JLCell("HORA: ".$_SESSION["miHoraActiva"],30,'l');
 //  if ($misLineasAumentar>1)
 //    $pdf->Line(5,25,200+$misLineasAumentar-10,25);
 //  else
  //   $pdf->Line(5,25,200,25);
   $pdf->SetFont('times','',7);
   $pdf->SetY(26);
   $pdf->SetX(1);
}

// Genera un encabezado para pdf y pld
function creaencabezapdfpld($pdf,$misLineasAumentar=1)
{
   if (isset($_SESSION["miMasCaracteresLegal"]) && $misLineasAumentar==1)
     $misLineasAumentar=$_SESSION["miMasCaracteresLegal"];

   $pdf->SetY(6);
   $pdf->Image('estilos/imagenes/logoempresa3.jpg',5,7,50);
   $pdf->SetFont('times','',7);

   $link=Conectarse();
   $miCadenaSWQLenca="SELECT Nombre, Direccion, RFC, TRIM(Telefonos) AS Telefonos FROM configuracion ";
   $resultenca=mysqli_query($link,$miCadenaSWQLenca);
   $rowenca=mysqli_fetch_array($resultenca);

   $pdf->SetY(13);
   $pdf->SetX(55);
   $pdf->JLCell($rowenca["Nombre"],140,'l');

   $pdf->SetY(17);
   $pdf->SetX(55);
   $pdf->JLCell($rowenca["Direccion"],140,'l');
   $pdf->SetY(21);
   $pdf->SetX(55);
   $pdf->JLCell("RFC: ".$rowenca["RFC"].",  TEL: ".$rowenca["Telefonos"],140,'l');

   if ($misLineasAumentar>1)
     $pdf->Line(5,25,200+$misLineasAumentar-10,25);
   else
     $pdf->Line(5,25,200,25);

   $pdf->SetFont('times','',7);
   $pdf->SetY(26);
   $pdf->SetX(1);
}
// solo va tener el logo
function creaencabezapdf2($pdf,$misLineasAumentar=1)
{


   $pdf->SetY(6);
   $pdf->Image('estilos/imagenes/logoempresa3.jpg',15,10,65);
    //$pdf->Image('estilos/imagenes/logoempresa3.jpg' , 80 ,22, 35 , 38,'JPG', 'http://www.sicap.mx');


} // fin crear encabeza2

function creaencabezapdfhorizontal($pdf,$misLineasAumentar=1)
{
	$imprimefecha=$misLineasAumentar;
   if (isset($_SESSION["miMasCaracteresLegal"]) && $misLineasAumentar==1)
     $misLineasAumentar=$_SESSION["miMasCaracteresLegal"];

   $pdf->SetY(6);
   $pdf->Image('estilos/imagenes/encabezadocompleto.png',10,8,35);
   $pdf->SetFont('arial','',8);
   $pdf->SetY(15);
   $pdf->SetX(160+$misLineasAumentar);

   $link=Conectarse();
   $miCadenaSWQLenca="SELECT Nombre, Direccion, RFC, TRIM(Telefonos) AS Telefonos, PaginaInternet  FROM configuracion ";
   $resultenca=mysqli_query($link,$miCadenaSWQLenca);
   $rowenca=mysqli_fetch_array($resultenca);

   $pdf->SetY(8);
   $pdf->SetX(10);
   $pdf->JLCell("[b]".utf8_decode($rowenca["Nombre"])."[/b]",260,'c');

   $pdf->SetY(11);
   $pdf->SetX(10);
   $pdf->JLCell($rowenca["Direccion"],260,'c');

   $pdf->SetY(14);
   $pdf->SetX(10);
   $pdf->JLCell("RFC: ".$rowenca["RFC"].", TEL: ".$rowenca["Telefonos"]."  ".$rowenca["PaginaInternet"],260,'c');
}


// permite conectarse al servidor y base de datos
 function Conectarse($mibasedefecto="***nombre_db***")
{
	$_SESSION["mibgUsuarioAct"]="***nombre_usuario***";
  $_SESSION["mibgClaveUsuar"]="***nombre_pass***";
  $link = mysqli_connect("localhost", $_SESSION["mibgUsuarioAct"], $_SESSION["mibgClaveUsuar"], $mibasedefecto);
  if (!$link)
  {
    echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
    echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
    echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
    exit;
  }
  return $link;
}

 function validacesion()
   {

    $ArchivoPHP=explode('/', $_SERVER['PHP_SELF']);

$link=Conectarse();

$miConsultaSQLU="SELECT external_link, Menu FROM (SELECT my.external_link, 'Permiso' AS Menu FROM mymenugenerador AS my INNER JOIN grupomenu AS gm ON gm.id=my.id INNER JOIN usuarios AS us ON us.IdGrupoUsuarios=gm.IdGrupoUsuarios WHERE my.external_link ='".$ArchivoPHP[2]."' AND us.IdUsuario='".$_SESSION["miSessionCaja"]."'
UNION
SELECT my.external_link, 'Menu' AS Menu FROM mymenugenerador AS my WHERE my.external_link ='".$ArchivoPHP[2]."') AS Temp GROUP BY Menu";
$resultU=mysqli_query($link,$miConsultaSQLU);
$rowU=mysqli_num_rows($resultU);

//echo $miConsultaSQLU;
if($rowU<>1 || $ArchivoPHP[2]=='caja.php')
{

    $miVariableArchivo=substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],'/')+1);

    $link=Conectarse();

    $miConsultaSQL="SELECT Tiempo FROM mymenugenerador WHERE external_link='".$miVariableArchivo."' ";
    $result2=mysqli_query($link,$miConsultaSQL);
    $row2=mysqli_fetch_array($result2);

    if ( mysqli_num_rows($result2)<1 || $row2['Tiempo']<5)
      $row2['Tiempo']=10;

    $miConsultaSQL="SELECT (DATE_ADD(FechaEnLinea,INTERVAL ".$row2['Tiempo']." MINUTE)<NOW()) AS Diferencia, Session FROM usuarios WHERE IdUsuario='".$_SESSION["miSessionCaja"]."' ";
    $result2=mysqli_query($link,$miConsultaSQL);
    $row3=mysqli_fetch_array($result2);

    //comparamos el tiempo transcurrido
    //wwwecho "<meta http-equiv='refresh' content='".($row2['Tiempo']*60)."'/>";
   if($row3['Diferencia']!=0 || $_SESSION["miSessionActual"]!=$row3['Session'])
    {
					$link=Conectarse();
     mysqli_query($link,"call CierraSesion('".$_SESSION["miSessionCaja"]."')");
     mysqli_close($link);
     //si pasaron 15 minutos o m?s
     //header("Location: Logout.php"); //env?o al usuario a la pag. de autenticaci?n
     //exit();
    }
    else //sino, actualizo la fecha de la sesion
    {
					$link=Conectarse();
	$miConsultaBitacora="SELECT my.menu_title FROM mymenugenerador AS my WHERE my.external_link ='".$ArchivoPHP[2]."' LIMIT 0,1";
  $resultBitacora=mysqli_query($link,$miConsultaBitacora);
  $rowBitacora=mysqli_fetch_array($resultBitacora);

  if(strlen($rowBitacora['menu_title'])>=1)
  {
	$Modulo=$rowBitacora['menu_title'];
	$Accion="Consulta";
  }
  elseif($ArchivoPHP[2]=='caja.php')
  {
	$Modulo="Ventana Principal";
	$Accion="Ninguna";
  }
  else
  {
	$Modulo="V.E.: ".$ArchivoPHP[2];
	$Accion="Consulta";
  }

      //$laCadenaSQL="CALL ValidaSesion('".$_SESSION["miSessionCaja"]."', '".$_SERVER['PHP_SELF']."') ";
						$laCadenaSQL="CALL ValidaSesion('".$_SESSION["miSessionCaja"]."', '".$_SERVER['PHP_SELF']."','".$Modulo."', '".$Accion."') ";
      $result2=mysqli_query($link,$laCadenaSQL);
    }
//      $result2=mysqli_query($link,"UPDATE usuarios SET FechaEnLinea=NOW() WHERE IdUsuario='".$_SESSION["miSessionCaja"]."' ");

}
else {
echo "<script>alert('".utf8_decode('No tiene permisos para acceder a este m\u00F3dulo, consulte al Administrador del Sistema.')."');</script>";
echo"<script>parent.location.href=\"caja.php\";</script>";
exit();
}
   mysqli_close($link);
  }

   // Regresa la direccion ip del visitante.
   function getIP()
   {
     if (isset($_SERVER))
     {
       if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
       {
          return $_SERVER['HTTP_X_FORWARDED_FOR'];
       }
       else
       {
          return $_SERVER['REMOTE_ADDR'];
       }
     }
     else
     {
       if (isset($GLOBALS['HTTP_SERVER_VARS']['HTTP_X_FORWARDER_FOR']))
       {
         return $GLOBALS['HTTP_SERVER_VARS']['HTTP_X_FORWARDED_FOR'];
       }
       else
       {
         return $GLOBALS['HTTP_SERVER_VARS']['REMOTE_ADDR'];
       }
     }
   }

// Crear un formulario


function creaformulario($nombretabla,$registroactivo=0,$registroclave="",$nombreformulario="generico",$camposocultos=array(),$camposinactivos=array(),$miaccioncomando=1,$misenlacesexternos=array(),$camposvalidar=array(),$mispantallasexternas=array(),$misdatosrelacionados=array(), $minumero=0, $mistags=array(), $misvalores=array(), $misEnumActualizar=array(), $misEnumMensajeAnexo=array(), $horasParaArreglo=array('09:00:00', '10:00:00',  '11:00:00',  '12:00:00',  '13:00:00',  '14:00:00', '15:00:00', '16:00:00', '17:00:00',  '18:00:00',  '19:00:00'), $DigitosParaHora=4)
{
	  echo "\n<script language=Javascript> \n";
     echo "function validaformulario(miformulario){\n";
     for($i=0; $i<count($camposvalidar); $i++)
     {
       if ($camposvalidar[$i][1]=="novacio")
       {
        echo "if(miformulario.".$camposvalidar[$i][0].".value.length==0) \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false); \n}\n";
       }
       if ($camposvalidar[$i][1]=="fecha")
       {
        echo "if(!ValidarFecha(miformulario.".$camposvalidar[$i][0].".value)) \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false); \n}\n";
       }
       if ($camposvalidar[$i][1]=="comparacadena")
       {
        echo "if(!ComparaCadena(miformulario.".$camposvalidar[$i][3].".value,miformulario.".$camposvalidar[$i][0].".value)) \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false); \n}\n";
       }

       if ($camposvalidar[$i][1]=="RFC")
       {
        echo "if(!ValidaRFC(miformulario.".$camposvalidar[$i][0].".value)) \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false); \n}\n";
       }
       if ($camposvalidar[$i][1]=="RFCcon")
       {
        echo "if(!ValidaRFCcon(miformulario.".$camposvalidar[$i][0].".value)) \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false); \n}\n";
       }

       if ($camposvalidar[$i][1]=="correo")
       {
        echo "if(!ValidaCorreo(miformulario.".$camposvalidar[$i][0].".value)) \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false); \n}\n";
       }
       if ($camposvalidar[$i][1]=="telefono")
       {
        echo "if(!ValidaTelefono(miformulario.".$camposvalidar[$i][0].".value)) \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false);\n }\n";
       }
       if ($camposvalidar[$i][1]=="numero")
       {
        echo "if(!ValidaNumero(miformulario.".$camposvalidar[$i][0].".value)) \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false);\n }\n";
       }
       if ($camposvalidar[$i][1]=="minimo")
       {
        echo "if(miformulario.".$camposvalidar[$i][0].".value.length<=".$camposvalidar[$i][3].") \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false); \n}\n";
       }
       if ($camposvalidar[$i][1]=="valorminimo")
       {
        echo "if(miformulario.".$camposvalidar[$i][0].".value<=".$camposvalidar[$i][3].") \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false); \n}\n";
       }
       if ($camposvalidar[$i][1]=="valormaximo")
       {
        echo "if(miformulario.".$camposvalidar[$i][0].".value>".$camposvalidar[$i][3].") \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false); \n}\n";
       }
       if ($camposvalidar[$i][1]=="menorque")
       {
        echo "if(miformulario.".$camposvalidar[$i][0].".value>=miformulario.".$camposvalidar[$i][3].".value) \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false); \n}\n";
       }
       if ($camposvalidar[$i][1]=="alfanumerico")
       {
        echo "if(!ValidaAlfanumerico(miformulario.".$camposvalidar[$i][0].".value)) \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false);\n }\n";
       }
       if ($camposvalidar[$i][1]=="alfabetico")
       {
        echo "if(!ValidaCaracteres(miformulario.".$camposvalidar[$i][0].".value)) \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false);\n }\n";
       }
       if ($camposvalidar[$i][1]=="mes")
       {
        echo "if(miformulario.".$camposvalidar[$i][0].".value<1 || miformulario.".$camposvalidar[$i][0].".value>12) \n{\n";
		    echo "miformulario.".$camposvalidar[$i][0].".focus();\n";
		    echo "alert(\"".$camposvalidar[$i][2]."\");\n";
		    echo "return (false); \n}\n";
       }

     }
     echo "\n miformulario.estavalidado.value='si'; \n return (true);\n";
     echo "}</script>\n";

   echo "<form name=\"formularioautomatico\" method=\"POST\" action=\"$nombreformulario\" onSubmit = \"return validaformulario(this)\">\n";
   echo "<table id=\"mitablaed\" border=1 cellspaceing=1 cellpadding=1 title=\"Actualice los datos y proceda a grabar o actualizar\">\n";
   $link=Conectarse();

   if ($registroactivo!=0)
   {
     $miCadenaSQLProc="SELECT * FROM $nombretabla WHERE $registroclave=$registroactivo";

     $result=mysqli_query($link,$miCadenaSQLProc);
     $rowdatos=mysqli_fetch_array($result);

   }

   $result = @mysqli_query($link,"DESCRIBE $nombretabla");
   $misColumnas=mysqli_num_rows($result);
   if ($misColumnas>18)
   {
     $esConPar=1;
     $esParAhora=0;
   }
   else
     $esConPar=0;
   $misRefrescarSiSeModifica3="";

   $ClPrHoraSeleccEsp="";
   while($row = @mysqli_fetch_array($result))
   {
     preg_match('/^([^ (]+)(\((.+)\))?([ ](.+))?$/',$row['Type'],$Datosfield);
     $esoculto=0;
      for($i=0; $i<count($camposocultos); $i++)
         {
            if ($row['Field']==$camposocultos[$i])
            {
              $esoculto=1;
              }
            }
    $michecadosi="";
    $misRefrescarSiSeModifica2="";
    $misRefrescarSiSeModifica="";

    if ($esoculto==0)
    {

   	$MiCampoDescripcionSpecial="";
   	$miSentenciaSQL="SELECT column_name, column_comment FROM information_schema.columns WHERE table_name = '$nombretabla' AND table_schema = '".$_SESSION["mibgBaseDatos"]."'";
      $resultcreme=mysqli_query($link,$miSentenciaSQL);
      while ($rowscreme=mysqli_fetch_array($resultcreme))
       {

        if ($rowscreme['column_name']==$row['Field'])
           $MiCampoDescripcionSpecial=$rowscreme['column_comment'];
      }

     $MiCampoDescripcionSpecial = (strlen($MiCampoDescripcionSpecial)>0) ? $MiCampoDescripcionSpecial : $row['Field'] ;

		 if(!empty($Datosfield[3]))
		 {
			if ($Datosfield[3]>50)
        $milargo=50;
     else
        $milargo=$Datosfield[3];
		 }

      $midefecto2="";
     if ($registroactivo!=0)
     {
       if ($Datosfield[1]=="enum")
       {
        $micampoactivo=$row['Field'];
        if ($rowdatos[$micampoactivo]!="")
         $midefecto2=$rowdatos[$micampoactivo];
       }
       else
       {
        $micampoactivo=$row['Field'];
        if ($rowdatos[$micampoactivo]!="")
        {
          if ($Datosfield[1]=="date")
          {
            $midefecto="value=\"".fechademysql($rowdatos[$micampoactivo])."\"";
            $micampoactvoespecial=$rowdatos[$micampoactivo];
          }
          elseif ($Datosfield[1]=="time")
          {
 	         $ClPrHoraSeleccEsp=$rowdatos[$micampoactivo];
            $midefecto="";
            $micampoactvoespecial=$rowdatos[$micampoactivo];
           }
           elseif ($Datosfield[1]=="tinyint" && $Datosfield[3]==1)
           {
            $midefecto="".$rowdatos[$micampoactivo];
            $micampoactvoespecial=$rowdatos[$micampoactivo];
           }
           else
           {
            $midefecto="value=\"".$rowdatos[$micampoactivo]."\"";
            $micampoactvoespecial=$rowdatos[$micampoactivo];
           }
         }
        else
        {
         $midefecto="";
         $micampoactvoespecial="";
         }
       }

     }
     else
     {

       if ($Datosfield[1]=="date")
         $midefecto="value=\"".fechaactual(2)."\"";
       elseif ($Datosfield[1]=="datetime")
         $midefecto="value=\"".fechaactual(1)."\"";
       elseif ($Datosfield[1]=="time")
       {
          $ClPrHoraSeleccEsp=$row['Default'];
          $midefecto="";
       }
       else
       {
        if ($row['Default']!="")
        {
          $midefecto="value=\"".$row['Default']."\"";
         }
        else
        {
        	 if (isset($_POST[$row['Field']]) && strlen($_POST[$row['Field']])>0)
        	 {
        	  $midefecto="value=\"".$_POST[$row['Field']]."\"";
        	  $midefecto2=$_POST[$row['Field']];
          }
        	 else
          $midefecto="";
         }
        }
     }

     if ($miaccioncomando!=3)
     {
      if ($row['Extra']=="auto_increment")
        $miactivo=" readonly ";
      else
        $miactivo=" ";
      for($i=0; $i<count($camposinactivos); $i++)
      {
       if ($row['Field']==$camposinactivos[$i])
	   {
		  $miactivo=" readonly ";
          $miactivocombo=" disabled ";
	   }
      }
     }
     else
       $miactivo=" readonly ";

     for($i=0; $i<count($misenlacesexternos); $i++)
     {
       if ($row['Field']==$misenlacesexternos[$i][0])
       {
          $Datosfield[1]="enlase";
          if (count($misenlacesexternos[$i])>3 && $misenlacesexternos[$i][2]!='->')
          {
            $cadenaelase="SELECT ".$misenlacesexternos[$i][2].",".$misenlacesexternos[$i][3]." FROM ".$misenlacesexternos[$i][1]. " ORDER BY ".$misenlacesexternos[$i][3].$misenlacesexternos[$i][4];
            if ($misenlacesexternos[$i][5]=='Actualizar')
            {
             $misRefrescarSiSeModifica=" onChange='submit();' ";
            }
          }
          elseif ($misenlacesexternos[$i][2]!='->')
          {
            $cadenaelase="SELECT ".$misenlacesexternos[$i][2].",".$misenlacesexternos[$i][3]." FROM ".$misenlacesexternos[$i][1]." ORDER BY ".$misenlacesexternos[$i][3];
            if ($misenlacesexternos[$i][4]=='Actualizar')
            {
             $misRefrescarSiSeModifica=" onChange='submit();' ";
            }
          }
          else
          {
            $cadenaelase="".$misenlacesexternos[$i][1];
            if (isset($misenlacesexternos[$i][3]) && $misenlacesexternos[$i][3]=='Actualizar')
            {
             $misRefrescarSiSeModifica=" onChange='submit();' ";
            }
          }
       }
       $misRefrescarSiSeModifica2=$misRefrescarSiSeModifica2.$misRefrescarSiSeModifica;
     }

        for($i=0; $i<count($misEnumActualizar); $i++)
        {
          if ($misEnumActualizar[$i]==$row['Field'])
          {
             $misRefrescarSiSeModifica=" onChange='submit();' ";
          }
        }

      if ( strlen($misRefrescarSiSeModifica)>0 )
      {
        $misRefrescarSiSeModifica3=$misRefrescarSiSeModifica;
      }

     $micamponoeditar="";
     $micadenanoeditar="";
     $mivalornoeditar="";

     for($jui=0;$jui<$minumero;$jui++)
      {

       if ($mistags[$jui]==$row['Field'])
       {
         $midefecto="value=\"".$misvalores[$jui]."\"";
         $midefecto2=$misvalores[$jui];
         $micampoactvoespecial=$misvalores[$jui];
       }
      }

     for($i=0; $i<count($misdatosrelacionados); $i++)
     {
    // echo $row['Field'].'  '.$misdatosrelacionados[$i][0].' '.$misdatosrelacionados[$i][3];
       if ($row['Field']==$misdatosrelacionados[$i][0])
       {
          $micamponoeditar=$misdatosrelacionados[$i][1];
          $micadenanoeditar=$misdatosrelacionados[$i][2];
          if ( strlen($misdatosrelacionados[$i][3])>0 )  // (!isset($midefecto) || strlen($midefecto)==0) &&
          {
              $midefecto="value=\"".$misdatosrelacionados[$i][3]."\"";
              $micampoactvoespecial=$misdatosrelacionados[$i][3];
              $midefecto2=$misdatosrelacionados[$i][3];
          }
        //  else
        //  {
        //   $micampoactvoespecial="";
        //  }
       }

     }
     $miEnumMensaje="";
     for($i=0; $i<count($misEnumMensajeAnexo); $i++)
     {
       if ($row['Field']==$misEnumMensajeAnexo[$i][0])
       {
          $miEnumMensaje=$misEnumMensajeAnexo[$i][1];
       }
     }


     $miTextoAdicional="";
     for($i=0; $i<count($mispantallasexternas); $i++)
     {
       if ($row['Field']==$mispantallasexternas[$i][0])
       {
          $Datosfield[1]="pantalla";
          $variable=$mispantallasexternas[$i][0];
          if (count($mispantallasexternas[$i])>4 && strlen($mispantallasexternas[$i][5])>0 && $mispantallasexternas[$i][5]!='Actualizar1')
          {
            $cadenaelase="<a href=\"buscar.php?miVariable=$variable\" target=\"blank\" onClick=\"jBusca(this); return false;\" title=\"Buscar un registro\">Buscar</a>";
            $_SESSION[$variable."1"]="SELECT ".$mispantallasexternas[$i][2].",".$mispantallasexternas[$i][3]." FROM ".$mispantallasexternas[$i][1];
            $_SESSION[$variable."2"]="SELECT ".$mispantallasexternas[$i][6].",".$mispantallasexternas[$i][7]." FROM ".$mispantallasexternas[$i][5]." ORDER BY ".$mispantallasexternas[$i][7];
            $_SESSION[$variable."3"]=$mispantallasexternas[$i][4];
            $_SESSION[$variable."4"]="function CierrajBusca(mijVariable) { window.returnValue = mijVariable; window.opener.document.formularioautomatico.".$mispantallasexternas[$i][0].".value=mijVariable;  window.close();}";
          }
          else
          {
            if ("->"==$mispantallasexternas[$i][2])
            {
              $_SESSION[$variable."1"]="".$mispantallasexternas[$i][1];
              if ($mispantallasexternas[$i][3]!="")
                $_SESSION[$variable."5"]="".$mispantallasexternas[$i][3];

               if (strlen($mispantallasexternas[$i][3])>0)
               {
									if(!isset($rowExterna))
									$rowExterna=array();
                 $miCampoField=$row['Field'];
                 $cadenaSQLExterna=$mispantallasexternas[$i][3] ;
                 $misRefrescarSiSeModifica3=" onChange='submit();' ";
                 $resultExterna=mysqli_query($link,$cadenaSQLExterna);
								 if(!empty($resultExterna))
                 $rowExterna=mysqli_fetch_array($resultExterna);
                 $miTextoAdicional="".$rowExterna['Nombre'];
                 $misRefrescarSiSeModifica=" onChange='submit();' ";
                 $MiValorActualSeleccionado=$mispantallasexternas[$i][4] ;

               }
             }
            else
              $_SESSION[$variable."1"]="SELECT ".$mispantallasexternas[$i][2].",".$mispantallasexternas[$i][3]." FROM ".$mispantallasexternas[$i][1];

            if ($mispantallasexternas[$i][0]=='IdPersona' || $mispantallasexternas[$i][0]=='IdSocio' || $mispantallasexternas[$i][0]=='IdPersonaConyuger' || $mispantallasexternas[$i][0]=='PropietarioReal' || $mispantallasexternas[$i][0]=='IdRecomendo' || $mispantallasexternas[$i][0]=='TerceroAutorizado' || $mispantallasexternas[$i][0]=='IdPersonaReferencia' || $mispantallasexternas[$i][0]=='ProveedordeRecursos' || $mispantallasexternas[$i][0]=='IdPersonaBeneficiario'  || $mispantallasexternas[$i][0]=='IdPersonaRelacionada' | $mispantallasexternas[$i][0]=='IdRiesgoComun' || $mispantallasexternas[$i][0]=='IdTutor' || $mispantallasexternas[$i][0]=='IdTutor2')
            {
            	$MiBalorTemporalPersona=$mispantallasexternas[$i][0].'1';
            	$MiBalorTemporalPersona2=$mispantallasexternas[$i][0].'2';

            	if (strlen($MiValorActualSeleccionado)>0 && (!isset($_POST[$MiBalorTemporalPersona]) || strlen($_POST[$MiBalorTemporalPersona])<1))
            	{
                 $cadenasql400="SELECT IdPersona, SocioMigrado AS Anterior FROM personas WHERE IdPersona='".$MiValorActualSeleccionado."' ";
                 $result400=mysqli_query($link,$cadenasql400);
                 $row400=mysqli_fetch_array($result400);
         	     $_POST[$MiBalorTemporalPersona]=$row400["Anterior"];
         	     $_POST[$MiBalorTemporalPersona2]=$MiValorActualSeleccionado;
         	   }

  //       	echo $MiValorActualSeleccionado.' - siiii - '.$_POST[$MiBalorTemporalPersona],' + '.$_POST[$MiBalorTemporalPersona2];

              if (isset($_POST[$MiBalorTemporalPersona]) && strlen($_POST[$MiBalorTemporalPersona])>0)
              {
            	$cadenasql400="SELECT IdPersona, SocioMigrado AS Anterior, DameNombre(IdPersona, 1) AS Nombre FROM personas WHERE SocioMigrado=NumeroCompletado('".$_POST[$MiBalorTemporalPersona]."') ";
               $result400=mysqli_query($link,$cadenasql400);
               $row400=mysqli_fetch_array($result400);

               if ($row400["IdPersona"]>0)
               {
                 $_POST[$MiBalorTemporalPersona2]=$row400["IdPersona"];
                 $_POST[$MiBalorTemporalPersona]=$row400["Anterior"];
                 $MiNombrePersonaTemporal=$row400["Nombre"];
               }
              }

                array_push($_SESSION["miArregloNoAplicar"],array($MiBalorTemporalPersona,$row['Field']));

               if ($mispantallasexternas[$i][3]=='Actualizar1' || $mispantallasexternas[$i][5]=='Actualizar1' )
                 $cadenaelase="<input type='button' value='Buscar' onclick=\"javascript:window.open('buscarpersonasolo.php?miVariable=$MiBalorTemporalPersona&miFiltroAdicioinal1=".$mispantallasexternas[$i][1]."&IdFormularioVariable= formularioautomatico' ,'','width=600,height=400,left=50,top=50,scrollbars=yes');\" />";
               else
                 $cadenaelase="<input type='button' value='Buscar' onclick=\"javascript:window.open('buscarpersona.php?miVariable=$MiBalorTemporalPersona&miFiltroAdicioinal1=".$mispantallasexternas[$i][1]."&IdFormularioVariable= formularioautomatico' ,'','width=600,height=400,left=50,top=50,scrollbars=yes');\" />";

            $EsDePersonaAmor=1;
            }
            elseif($mispantallasexternas[$i][0]=='IdListaNegra')
            {
               $cadenaelase="<a href=\"buscarpeps.php?miNombre=".$_POST['Nombre']."&miApellidoPaterno=".$_POST['ApellidoPaterno']."&miApellidoMaterno=".$_POST['ApellidoMaterno']."&miRFC=".$_POST['RFC']."\" target=\"blank\" onClick=\"jBusca(this); return false;\" title=\"Buscar un registro\">Buscar</a>";
                $EsDePersonaAmor=0;
            }
            else
            {
               $cadenaelase="<input type=\"button\" value=\"Buscar\" onclick=\"javascript:window.open('buscar.php?miVariable=$variable','','width=600,height=400,left=50,top=50,scrollbars=yes');\"  /> $miTextoAdicional ";
               $EsDePersonaAmor=0;
            }

            $_SESSION[$variable."4"]="function CierrajBusca(mijVariable) { window.returnValue = mijVariable; window.opener.document.formularioautomatico.".$mispantallasexternas[$i][0].".value=mijVariable;  window.close();}";
          }
       }
     }
     /* empieza el desplegado */

     if ($esConPar>0)
     {
       if ($esParAhora==0)
       {
         echo "<tr>";
       }
     }
     else
       echo "<tr>";

     switch($Datosfield[1])
     {
      case "time":
       {
//         echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><input type=\"text\" $miactivo name=\"".$row['Field']."\" $midefecto size=10 maxlength=".$Datosfield[3]." $misRefrescarSiSeModifica >$miEnumMensaje</td>";
            echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td>";
            echo "<select name=\"".$row['Field']."\" $misRefrescarSiSeModifica $miactivo>";

            if ($registroactivo>0)
            {
               foreach ($horasParaArreglo as $a)
               {
               	if ($DigitosParaHora==4)
               	{
               		$b=substr($a,0,5);
                 	  if (substr($ClPrHoraSeleccEsp,0,5)==$b)
                      echo "<option selected value='$b'> $b </option>";
                    else
                      echo "<option value='$b'> $b  </option>";
                  }
               	elseif ($DigitosParaHora==2)
               	{
               	  $b=substr($a,0,2);
                 	  if (substr($ClPrHoraSeleccEsp,0,2)==$b)
                      echo "<option selected value='$b'> $b </option>";
                    else
                      echo "<option value='$b'> $b  </option>";
                  }
                  else
                  {
                 	  if ($ClPrHoraSeleccEsp==$a)
                      echo "<option selected value='$a'> $a </option>";
                    else
                      echo "<option value='$a'> $a  </option>";
                  }
               }
            }
            else
            {
               foreach ($horasParaArreglo as $a)
               {
               	if ($DigitosParaHora==4)
               	{
               		$b=substr($a,0,5);
                 	  if ($midefecto2==$b)
                      echo "<option selected value='$b'> $b </option>";
                    else
                      echo "<option value='$b'> $b  </option>";
                  }
               	elseif ($DigitosParaHora==2)
               	{
               	  $b=substr($a,0,2);
                 	  if ($midefecto2==$b)
                      echo "<option selected value='$b'> $b </option>";
                    else
                      echo "<option value='$b'> $b  </option>";
                  }
                  else
                  {
                 	  if ($midefecto2==$a)
                      echo "<option selected value='$a'> $a </option>";
                    else
                      echo "<option value='$a'> $a  </option>";
                  }
               }
            }
            echo "</select>$miEnumMensaje</td>";

       }
       break;
	    case "varchar":
	    {
	    	if ($_SESSION["miMayusculasActivo"]==0)
	    	  if ($Datosfield[3]<=300)
	    	  {
                echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><input type=\"text\" $miactivo name=\"".$row['Field']."\" $midefecto size=$milargo maxlength=".$Datosfield[3]." class='textbox'  $misRefrescarSiSeModifica >$miEnumMensaje</td>";

           }
           else
           {
           	  if (!isset($micampoactvoespecial) || $micampoactvoespecial=="")
           	  {
           	  	 $micampoactvoespecial=$_POST[$row['Field']];
           	  }
           	  $mislineas=$Datosfield[3]/300;
           	  $miscolumnas=$milargo*2;

  	    	     if ($esConPar>0)
              {
                if ($esParAhora==0)
                {
                   echo "<td align=right>".$MiCampoDescripcionSpecial." </td><td colspan=3><textarea $miactivo name=\"".$row['Field']."\" cols=$miscolumnas rows=$mislineas class='textbox' >$micampoactvoespecial</textarea> $miEnumMensaje </td>";
                   $esParAhora=1;
                }
                else
                {
                   echo "<td align=right>".$MiCampoDescripcionSpecial." </td><td><textarea $miactivo name=\"".$row['Field']."\" cols=$miscolumnas rows=$mislineas class='textbox' >$micampoactvoespecial</textarea> $miEnumMensaje </td>";
                }
              }
              else
              {
               	echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><textarea $miactivo name=\"".$row['Field']."\" cols=$miscolumnas rows=$mislineas class='textbox' >$micampoactvoespecial</textarea> $miEnumMensaje </td>";
              }
           }
         else
  	    	  if ($Datosfield[3]<=300)
	    	  {
            echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><input type=\"text\" $miactivo name=\"".$row['Field']."\" $midefecto size=$milargo maxlength=".$Datosfield[3]." style='text-transform:uppercase;' class='textbox' $misRefrescarSiSeModifica >$miEnumMensaje</td>";
           }
           else
           {
           	  if (!isset($micampoactvoespecial) || $micampoactvoespecial=="")
           	  {
           	  	 $micampoactvoespecial=$_POST[$row['Field']];
           	  }
           	  $mislineas=$Datosfield[3]/300;
           	  $miscolumnas=$milargo*2;

  	    	     if ($esConPar>0)
              {
                if ($esParAhora==0)
                {
                   echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td colspan=3><textarea $miactivo name=\"".$row['Field']."\" cols=$miscolumnas rows=$mislineas class='textbox' style='text-transform:uppercase;' >$micampoactvoespecial</textarea> $miEnumMensaje</td>";               $esParAhora=1;
                   }
                else
                {
                   echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><textarea $miactivo name=\"".$row['Field']."\" cols=$miscolumnas rows=$mislineas class='textbox' style='text-transform:uppercase;' >$micampoactvoespecial</textarea> $miEnumMensaje</td>";
                }
              }
              else
              {
              	   echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><textarea $miactivo name=\"".$row['Field']."\" cols=$miscolumnas rows=$mislineas class='textbox' style='text-transform:uppercase;' >$micampoactvoespecial</textarea> $miEnumMensaje</td>";
              }
           }
           $micampoactvoespecial="";
         }
         break;
	    case "int":
       case "smallint":
       case "bigint":
       case "tinyint":
        if ($Datosfield[1]=="tinyint" && $Datosfield[3]==1)
        {
 //        echo  $registroactivo.' * '.$midefecto.' - '.$midefecto2.' '.stripos($midefecto,'=');

         echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td>";
         echo "<select name=\"".$row['Field']."\" $misRefrescarSiSeModifica $miactivo>";

  //       if ($registroactivo>0)
  //       {
  	      if ( stripos($midefecto,'=')>0 )
  	      {
           if ($midefecto2==1)
            {
             echo "<option value='1' selected>Si</option>";
             echo "<option value='0'>No</option>";
            }
            else
            {
              echo "<option value='1' >Si</option>";
              echo "<option value='0' selected>No</option>";
            }
          }
          else
          {
            if ($midefecto==1)
            {
             echo "<option value='1' selected>Si</option>";
             echo "<option value='0'>No</option>";
            }
            else
            {
              echo "<option value='1' >Si</option>";
              echo "<option value='0' selected>No</option>";
            }
          }

         echo "</select>$miEnumMensaje</td>";
        }
        else
        {
          if ($micadenanoeditar!="")
          {
            $result2=mysqli_query($link,$micadenanoeditar);
            $row3=mysqli_fetch_array($result2);
            echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><input type=\"text\" $miactivo name=\"".$row['Field']."\" $midefecto size=$milargo maxlength=".$Datosfield[3]." class='textbox' onkeypress=\"return a_numero(event);\"  $misRefrescarSiSeModifica>".$row3[$micamponoeditar]." $miEnumMensaje</td>";
          }
          else
            echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><input type=\"text\" $miactivo id=\"".$row['Field']."\" name=\"".$row['Field']."\" $midefecto size=$milargo maxlength=".$Datosfield[3]." class='textbox' onkeypress=\"return a_numero(event);\"  $misRefrescarSiSeModifica >$miEnumMensaje</td>";
        }
        break;
	    case "enum":
         echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td>";

         if ( trim($miactivo)=='readonly')
            echo "<select name=\"".$row['Field']."\" $misRefrescarSiSeModifica disabled >";
        else
            echo "<select name=\"".$row['Field']."\" $misRefrescarSiSeModifica >";
         $miarreglo=explode (",",$Datosfield[3]);
         for($i=0; $i<count($miarreglo); $i++)
         {
            $miarreglouno=substr(substr($miarreglo[$i],0,-1),1);
            if ($micampoactvoespecial==$miarreglouno || $_POST[$row['Field']]==$miarreglouno || (strlen($midefecto2)>0 && $midefecto2==$miarreglouno))
         	    echo "<option selected value=\"$miarreglouno\">$miarreglouno </option>";
            else
         	    echo "<option value=\"$miarreglouno\">$miarreglouno </option>";
            }
         echo "</select>$miEnumMensaje</td>";
         break;
	    case "enlase":
         echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td>";
         echo "<select name=\"".$row['Field']."\" $misRefrescarSiSeModifica $miactivo>";
         $result2=mysqli_query($link,$cadenaelase);
         while ($row2=mysqli_fetch_array($result2))
          {
     	      if (($micampoactvoespecial!="" && $micampoactvoespecial==$row2[0]) || ($midefecto2!="" && $midefecto2==$row2[0]))
       	      printf("<option selected value=".$row2[0].">".$row2[1]." </option>");
            else
       	      printf("<option value=".$row2[0].">".$row2[1]." </option>");
         }
         echo "</select>$miEnumMensaje</td>";
         break;
	    case "pantalla":
	      {
      	  if ($EsDePersonaAmor==1)
	      	{
              echo "<input type=\"hidden\" $miactivo name=\"".$row['Field']."\" Value=\"".$_POST[$MiBalorTemporalPersona2]."\"  >";
	           if (strlen($_POST[$MiBalorTemporalPersona])>0)
                 echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><input type=\"text\" $miactivo name=\"".$MiBalorTemporalPersona."\" Value=\"".$_POST[$MiBalorTemporalPersona]."\" size=$milargo maxlength=".$Datosfield[3]."  $misRefrescarSiSeModifica $miEnumMensaje>$cadenaelase $MiNombrePersonaTemporal </td>";
              else
                 echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><input type=\"text\" $miactivo name=\"".$MiBalorTemporalPersona."\" Value=\"".$_POST[$MiBalorTemporalPersona]."\" size=$milargo maxlength=".$Datosfield[3]."  $misRefrescarSiSeModifica $miEnumMensaje>$cadenaelase </td>";
            }
            else
            {
               echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><input type=\"text\" $miactivo name=\"".$row['Field']."\" $midefecto size=$milargo maxlength=".$Datosfield[3]." class='textbox' >$cadenaelase</td>";
            }
         }
         break;
	    case "date":
	       if ($miactivo==" readonly ")
	       {
              echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><input type=\"text\" $miactivo name=\"".$row['Field']."\" id=\"".$row['Field']."\" $midefecto  size=10 maxlength=10 select(this);\" >$miEnumMensaje</td>";
           }
           else
           {
  	           echo "\n<script language=Javascript> \n {addCalendar(\"mi".$row['Field']."\", \"Seleccione una fecha\", \"".$row['Field']."\", \"formularioautomatico\");setWidth(90, 1, 15, 1);}</script>\n";
              echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><input type=\"text\" $miactivo name=\"".$row['Field']."\" id=\"".$row['Field']."\" $midefecto size=10 maxlength=10 $misRefrescarSiSeModifica select(this);\" class='textbox'><a href=\"javascript:showCal('mi".$row['Field']."');\"><img src=\"imagenes/calendario.gif\" Style='cursor: hand;' width=22 height=20 border=0></a>$miEnumMensaje</td>";
           }
         break;
       case "text":
         if (strpos($midefecto, "value=" )===false)
         {}
         else
           $midefecto=substr(substr($midefecto,0,-1),8);
         echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><textarea $miactivo id=\"".$row['Field']."\" name=\"".$row['Field']."\" cols=50 rows=5 class='textbox' > $midefecto $misRefrescarSiSeModifica </textarea>$miEnumMensaje</td>";
         break;
	    case "datetime":
         echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><input type=\"text\" $miactivo name=\"".$row['Field']."\" id=\"".$row['Field']."\" $midefecto size=21 maxlength=21 class='textbox' $misRefrescarSiSeModifica >$miEnumMensaje</td> ";
         break;
	    case "decimal":
         echo "<td align=right> ".$MiCampoDescripcionSpecial." </td><td><input type=\"text\" $miactivo name=\"".$row['Field']."\" $midefecto size=$milargo maxlength=".$Datosfield[3]." onkeypress=\"return a_moneda(event);\" class='textbox' $misRefrescarSiSeModifica >$miEnumMensaje</td>  ";
         break;
      }
     if ($esConPar>0)
     {
       if ($esParAhora==1)
       {
         echo "</tr>";
         $esParAhora=0;
       }
       else
         $esParAhora=1;
     }
     else
       echo "</tr>";

      echo "\n";
    }
   }

     if ($esConPar>0)
       echo "\n<tr><td colspan=4 align=center>";
     else
       echo "\n<tr><td colspan=2 align=center>";

   if (strpos($misRefrescarSiSeModifica3,'submit()')>0)
      echo "<input type=\"submit\"  name=\"sisiValidar\" value='Validar' class='button' >";  //onClick='submit();'

      echo "<input type=\"submit\" name='estavalidadoCancela' value='Cancelar' class='button' >";


   if ($miaccioncomando==1)
       echo "<input type=\"submit\" name=\"sisiActualizar\" value=\"Actualizar\" class='button'></td></form>";
   if ($miaccioncomando==2)
       echo "<input type=\"submit\" name=\"sisiGravar\" value=\"Grabar\" class='button'></td></form>";
   if ($miaccioncomando==3)
       echo "<input type=\"submit\" name=\"sisiBorrar\" value=\"Borrar\" class='button'></td></form>";

   echo "</td></tr></table></form>";

   if ( strlen($misRefrescarSiSeModifica3)>0 )
      echo "<input type=\"hidden\" name='estavalidado' value='Parcial' >";
   else
      echo "<input type=\"hidden\" name='estavalidado' value='no' >";

   mysqli_free_result($result);
   mysqli_close($link);
}
//PMR
function bisiesto($anio_actual){
    $bisiesto=false;

      if (checkdate(2,29,$anio_actual))
      {
        $bisiesto=true;
    }
    return $bisiesto;
}

function anosmesesdias($fecha_actual,$fecha_de_nacimiento)
{
//$fecha_actual = date ("Y-m-d");
$array_nacimiento = explode ( "-", $fecha_de_nacimiento );
$array_actual = explode ( "-", $fecha_actual );

$anos =  $array_actual[0] - $array_nacimiento[0];
$meses = $array_actual[1] - $array_nacimiento[1];
$dias =  $array_actual[2] - $array_nacimiento[2];


if ($dias < 0)
{
    --$meses;

    switch ($array_actual[1]) {
           case 1:     $dias_mes_anterior=31; break;
           case 2:     $dias_mes_anterior=31; break;
           case 3:
                if (bisiesto($array_actual[0]))
                {
                    $dias_mes_anterior=29; break;
                } else {
                    $dias_mes_anterior=28; break;
                }
           case 4:     $dias_mes_anterior=31; break;
           case 5:     $dias_mes_anterior=30; break;
           case 6:     $dias_mes_anterior=31; break;
           case 7:     $dias_mes_anterior=30; break;
           case 8:     $dias_mes_anterior=31; break;
           case 9:     $dias_mes_anterior=31; break;
           case 10:     $dias_mes_anterior=30; break;
           case 11:     $dias_mes_anterior=31; break;
           case 12:     $dias_mes_anterior=30; break;
    }

    $dias=$dias + $dias_mes_anterior;
}


if ($meses < 0)
{
    --$anos;
    $meses=$meses + 12;
}

$array[0]=$dias;
$array[1]=$meses;
$array[2]=$anos;
return  $array;

}
   //PMR
function accionregistros($minumero,$mistags,$misvalores,$mitabla,$micampoclave)
{
	//$dbase='coroneo';
	$dbase='***nombre_db***';

		$link=Conectarse();
    $micomando="";
    $validotodo=1;
    for($i=0;$i<$minumero;$i++)
    {
      if ($mistags[$i]=="sisiActualizar")
         $micomando="Actualizar";
      if ($mistags[$i]=="sisiGravar")
         $micomando="Gravar";
      if ($mistags[$i]=="sisiBorrar")
         $micomando="Borrar";
    }

		$result=mysqli_query($link,"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$mitabla' AND table_schema = '$dbase'");
		$arrayField=array();
		while($rowField=mysqli_fetch_array($result))
		{
			array_push($arrayField,$rowField['COLUMN_NAME']);
		}

    if ($micomando=="Actualizar")
    {
      $lacadena="UPDATE ".$mitabla." SET ";
      $lacondicion="";
      for($i=0;$i<$minumero;$i++)
      {
				if(in_array($mistags[$i],$arrayField)==true)
				{
					if ($mistags[$i]!="sisiActualizar" && $mistags[$i]!="sisiGravar" && $mistags[$i]!="sisiBorrar" && $mistags[$i]!=$micampoclave && $mistags[$i]!="estavalidado")
         {
           $miValorSiseIncrusta=1;
					 /*list($dd,$mm,$yyyy) = explode('/',$misvalores[$i]);
					 if (checkdate($mm,$dd,$yyyy)==true)
					 $lacadena.=$mistags[$i]."='".fechaamysql($misvalores[$i])."',";
					 //echo "<br>".$misvalores[$i].":".strlen($misvalores[$i])." cosa: ".preg_match("'/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/i'", $misvalores[$i]);*/

					 if (preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $misvalores[$i], $matches))
					 {
								if (checkdate($matches[2], $matches[1], $matches[3])==true)
								{
										$lacadena.=$mistags[$i]."='".fechaamysql($misvalores[$i])."',";
								}

					 }
           else
           {

    	       foreach ($_SESSION["miArregloNoAplicar"] AS $miArregloParcialNoAplicar)
             {
               if ($mistags[$i]==$miArregloParcialNoAplicar[0])
              	   $miValorSiseIncrusta=0;
             }

           	 if ($miValorSiseIncrusta==1)
           	 {
           	 	 if ($misvalores[$i]=='now()' || $misvalores[$i]=='NOW()')
               {
                 $lacadenavalores.=$mistags[$i]."='".$misvalores[$i].",";
               }
               else
               {
           	    if ($_SESSION["miMayusculasActivo"]==1)
                 $lacadena.=$mistags[$i]."='".strtr(strtoupper($misvalores[$i]),"àèìòùáéíóúñäëïöü","ÀÈÌÒÙÁÉÍÓÚÑÄËÏÖÜ")."',";
                else
                 $lacadena.=$mistags[$i]."='".$misvalores[$i]."',";
               }
             }
            }
          }
        if ($mistags[$i]==$micampoclave)
          $lacondicion=$misvalores[$i];
				}

      }

      if ($validotodo==1)
        $lacadena=substr($lacadena,0,-1)." WHERE ".$micampoclave."='".$lacondicion. "' LIMIT 1";
      else
        $lacadena="";

								$ArchivoPHP=explode('/', $_SERVER['PHP_SELF']);
        $link=Conectarse();
        $miConsultaBitacora="SELECT my.menu_title FROM mymenugenerador AS my WHERE my.external_link ='".$ArchivoPHP[2]."' LIMIT 0,1";
        $resultBitacora=mysqli_query($link,$miConsultaBitacora);
        $rowBitacora=mysqli_fetch_array($resultBitacora);
        if(strlen($rowBitacora['menu_title'])>=1)
									$Modulo=$rowBitacora['menu_title'];
								else
									$Modulo="V.E.: ".$ArchivoPHP[2];
        $Accion="Modificación: ".$mitabla.", Id:".$lacondicion;
        $laCadenaSQL="CALL ValidaSesion('".$_SESSION["miSessionCaja"]."', '".$_SERVER['PHP_SELF']."','".$Modulo."', '".$Accion."') ";
        $result2=mysqli_query($link,$laCadenaSQL);
    }
    if ($micomando=="Gravar")
    {

      $lacadena="INSERT INTO ".$mitabla." ( ";
      $lacadenavalores="";
      for($i=0;$i<$minumero;$i++)
      {

				if(in_array($mistags[$i],$arrayField)==true)
				{
					if ($mistags[$i]!="sisiActualizar" && $mistags[$i]!="sisiGravar" && $mistags[$i]!="sisiBorrar" && $mistags[$i]!=$micampoclave  && $mistags[$i]!="estavalidado")
		        {

						//print_r($_SESSION["miArregloNoAplicar"]);
		          $miValorSiseIncrusta=1;

					 if (preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $misvalores[$i], $matches))
					 {
					 	 if (checkdate($matches[2], $matches[1], $matches[3])==true)
						 {

						 	 $lacadena.=$mistags[$i].",";
					       $lacadenavalores.="'".fechaamysql($misvalores[$i])."',";
					 	 }

					  }
		           else
		           {
		             foreach ($_SESSION["miArregloNoAplicar"] AS $miArregloParcialNoAplicar)
		             {
		               if ($mistags[$i]==$miArregloParcialNoAplicar[0])
		              	   $miValorSiseIncrusta=0;
		             }

		           	 if ($miValorSiseIncrusta==1)
		           	 {
		               $lacadena.=$mistags[$i].",";


		               if ($misvalores[$i]=='now()' || $misvalores[$i]=='NOW()')
		               {
		                 $lacadenavalores.=$misvalores[$i].",";
		              }
		               else
		               {
		           	     if ($_SESSION["miMayusculasActivo"]==1)
		               	$lacadenavalores.="'".strtr(strtoupper($misvalores[$i]),"àèìòùáéíóúñäëïöü","ÀÈÌÒÙÁÉÍÓÚÑÄËÏÖÜ")."',";
		                 else
		                  $lacadenavalores.="'".$misvalores[$i]."',";
		               }

		              }
		            }

		        }
				}

      }
      if ($validotodo==1)
        $lacadena=substr($lacadena,0,-1).") VALUES (".substr($lacadenavalores,0,-1).")";
      else
        $lacadena="";

//echo $lacadena;

if($mitabla!='captacion')
        {
		//$IdNuevo=explode(",'", $lacadenavalores);
        //$IdNuevo=str_replace("'","",$IdNuevo[1]);
        //$IdNuevo=str_replace(",","",$IdNuevo);

		$IdNuevo='A';

		if(is_numeric($IdNuevo)==false)
		{
			$link=Conectarse();
			$rowPrimaryKey=mysqli_fetch_array(mysqli_query($link,"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$mitabla' AND table_schema = '$dbase' AND COLUMN_KEY = 'PRI' LIMIT 0,1"));
			$rowUltimoRegistro=mysqli_fetch_array(mysqli_query($link,"SELECT (".$rowPrimaryKey['COLUMN_NAME']."+1) AS IdNuevo FROM $mitabla ORDER BY ".$rowPrimaryKey['COLUMN_NAME']." DESC LIMIT 0,1"));
			$IdNuevo=$rowUltimoRegistro['IdNuevo'];
		}
		$Cont=0;
		while(strlen($IdNuevo)==0 && $Cont<3)
		{
			$link=Conectarse();
			$rowPrimaryKey=mysqli_fetch_array(mysqli_query($link,"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$mitabla' AND table_schema = '$dbase' AND COLUMN_KEY = 'PRI' LIMIT 0,1"));
			$rowUltimoRegistro=mysqli_fetch_array(mysqli_query($link,"SELECT (".$rowPrimaryKey['COLUMN_NAME']."+1) AS IdNuevo FROM $mitabla ORDER BY ".$rowPrimaryKey['COLUMN_NAME']." DESC LIMIT 0,1"));
			$IdNuevo=$rowUltimoRegistro['IdNuevo'];
			$Cont=$Cont+1;

			if(strlen($IdNuevo)>0 && $IdNuevo>0)
				$Cont=3;
		}

        $ArchivoPHP=explode('/', $_SERVER['PHP_SELF']);
        $link=Conectarse();
        $miConsultaBitacora="SELECT my.menu_title FROM mymenugenerador AS my WHERE my.external_link ='".$ArchivoPHP[2]."' LIMIT 0,1";
        $resultBitacora=mysqli_query($link,$miConsultaBitacora);
        $rowBitacora=mysqli_fetch_array($resultBitacora);
        if(strlen($rowBitacora['menu_title'])>=1)
	$Modulo=$rowBitacora['menu_title'];
        else
	$Modulo="V.E.: ".$ArchivoPHP[2];
        $Accion="Alta: ".$mitabla.", Id:".$IdNuevo;
        $laCadenaSQL="CALL ValidaSesion('".$_SESSION["miSessionCaja"]."', '".$_SERVER['PHP_SELF']."','".$Modulo."', '".$Accion."') ";
        $result2=mysqli_query($link,$laCadenaSQL);
        }


    }

    if ($micomando=="Borrar")
    {
      $lacadena="DELETE FROM ".$mitabla." WHERE ".$micampoclave."='";
      for($i=0;$i<$minumero;$i++)
      {
        if ($mistags[$i]==$micampoclave)
		{
          $lacadena.=$misvalores[$i];
		  $IdEliminado=$misvalores[$i];
		}
      }
      $lacadena.="' LIMIT 1";

						$ArchivoPHP=explode('/', $_SERVER['PHP_SELF']);
        $link=Conectarse();
        $miConsultaBitacora="SELECT my.menu_title FROM mymenugenerador AS my WHERE my.external_link ='".$ArchivoPHP[2]."' LIMIT 0,1";
        $resultBitacora=mysqli_query($link,$miConsultaBitacora);
        $rowBitacora=mysqli_fetch_array($resultBitacora);
        if(strlen($rowBitacora['menu_title'])>=1)
	$Modulo=$rowBitacora['menu_title'];
        else
	$Modulo="V.E.: ".$ArchivoPHP[2];
        $Accion="Eliminación: ".$mitabla.", Id:".$IdEliminado;
        $laCadenaSQL="CALL ValidaSesion('".$_SESSION["miSessionCaja"]."', '".$_SERVER['PHP_SELF']."','".$Modulo."', '".$Accion."') ";
        $result2=mysqli_query($link,$laCadenaSQL);
    }

    if (isset($lacadena) && $lacadena!="")
    {
      $link=Conectarse();
      mysqli_query($link,$lacadena);

			 if (in_array($_SESSION["miSessionCaja"],array(146,8))==true){
        //echo "$lacadena";
      }

      //proceso de numero de sociomigrado
      if($mitabla=='personas')
      {
            $cadenasql="SELECT IdPersona AS vIdPersona FROM personas ORDER BY IdPersona desc LIMIT 0,1";
            $result=mysqli_query($link,$cadenasql);
            $row=mysqli_fetch_array($result);
			sleep(1);
			$cadenasql2="SELECT AplicaSocioMigrado('".$row['vIdPersona']."') AS SocioMigrado ";
            $result2=mysqli_query($link,$cadenasql2);

            $cadenasql="SELECT IdPersona AS vIdPersona FROM personas ORDER BY IdPersona desc LIMIT 1,1";
            $result=mysqli_query($link,$cadenasql);
            $row=mysqli_fetch_array($result);
			sleep(1);
			$cadenasql2="SELECT AplicaSocioMigrado('".$row['vIdPersona']."') AS SocioMigrado ";
            $result2=mysqli_query($link,$cadenasql2);
      }

						if($mitabla=='captacion')
      {
	$IdNuevo=explode(",'", $lacadenavalores);
        $IdNuevo=str_replace("'","",$IdNuevo[0]);
        $IdNuevo=str_replace(",","",$IdNuevo);

        $miCadenaSQLBit="SELECT IdCaptacion FROM captacion WHERE IdPersona='".$IdNuevo."' ORDER BY IdCaptacion DESC LIMIT 0,1";
           $resultBit=mysqli_query($link,$miCadenaSQLBit);
           $rowBit=mysqli_fetch_array($resultBit);

        RegistroBitacoraManual(utf8_encode("Alta: captación, Id: ").$rowBit["IdCaptacion"]."");
      }
     // echo $lacadena;
      mysqli_close($link);
    }

 //echo $lacadena;
}  // fin de  accion con registros


 /* Crea un grid con los registros pasados y con el formato de un arreglo */
function creatablaarreglo($vmiresultado,$campoclave,$paginaaenviar,$vmodificar=0,$vmiborrar=0, $miarreglo=array(),$texmodificar="Modificar",$textborrar="Borrar",$vAdicional=0,$textadicional="Adicional",$conPaginador=1,$mistotales=array(), $miCharEspecial=0, $misArreglosModificar=array(), $misArreglosImprimir=array())
{
   $totalcolumnas=0;
   $_SESSION["misRegistrosEnPantalla"]=0;
   echo "<form name='formularioautomat' action='".$paginaaenviar."' method=\"POST\" ><center><table id=\"mitabla\" border=1 cellspacing=1 cellpadding=1 style=\"margin: 0 auto\" bordercolor='#ecdfac'><tr>";
   if (!preg_match('/Modificar/',$_SESSION["miNivelUsuario"]) && !preg_match('/Todo/',$_SESSION["miNivelUsuario"]))
     $vmodificar=0;
   if (!preg_match('/Borrar/',$_SESSION["miNivelUsuario"]) && !preg_match('/Todo/',$_SESSION["miNivelUsuario"]))
     $vmiborrar=0;
   $miactiva=1;
   $paraencabezado=1;
   $banen=0;


   while ($row=mysqli_fetch_array($vmiresultado))
   {
     $_SESSION["misRegistrosEnPantalla"]=$_SESSION["misRegistrosEnPantalla"]+1;
   	 $miactiva=1;
   	 if ($paraencabezado==1)
   	 {
   	 	if ((preg_match('/Modificar/',$_SESSION["miNivelUsuario"]) || preg_match('/Todo/',$_SESSION["miNivelUsuario"])) && $vmodificar==1)
          {
           echo "<th align=center>".Espacios(1)."$texmodificar".Espacios(1)."</th>";
           $totalcolumnas=$totalcolumnas+1;
          }
   	 	foreach($row as $key => $value )
        {
   	      if ($miactiva==0)
   	      {
   	        echo "<th align=center>".Espacios(1).($key).Espacios(1)."</th>";
   	        $miactiva=1;
   	        $totalcolumnas=$totalcolumnas+1;
   	      }
   	      else
   	      {$miactiva=0;}
        }
        if ((preg_match('/Borrar/',$_SESSION["miNivelUsuario"]) || preg_match('/Todo/',$_SESSION["miNivelUsuario"])) && $vmiborrar==1)
          {
            echo "<th align=center>".Espacios(1)."$textborrar".Espacios(1)."</th>";
            $totalcolumnas=$totalcolumnas+1;
          }
        if ((preg_match('/Borrar/',$_SESSION["miNivelUsuario"]) || preg_match('/Todo/',$_SESSION["miNivelUsuario"])) && $vAdicional==1)
          {
            echo "<th align=center>".Espacios(1)."$textadicional".Espacios(1)."</th>";
            $totalcolumnas=$totalcolumnas;
          }

        $paraencabezado=2;
        echo "</tr> <div class=\"tablascroll\">";
   	 }
     if ($banen==1)
     {
        echo "<tr id=\"columnapar\">";
        $banen=0;
     }
     else
     {
       	$banen=1;
     	echo "<tr id=\"columnanon\">";
     }
     $eselprimero=1;
     $elvalorclave=0;
     foreach($row as $key => $value )
     {
   	   if ($miactiva==1)
   	   {
   	   	if ( $eselprimero==1 && (preg_match('/Modificar/',$_SESSION["miNivelUsuario"]) || preg_match('/Todo/',$_SESSION["miNivelUsuario"]))  && $vmodificar==1 && $value!=null)
   	     {
   	     	echo "<td align=center><a href=\"$paginaaenviar?$texmodificar=$value\" title=\"$texmodificar el registro marcado\">$texmodificar</a></td>";
            $elvalorclave=$value;
   	     	$eselprimero=2;
   	     }
  	    if ($eselprimero==1 && (preg_match('/Borrar/',$_SESSION["miNivelUsuario"]) || preg_match('/Todo/',$_SESSION["miNivelUsuario"])) && $vmodificar==0 && $vmiborrar==1)
   	     {
   	     	$elvalorclave=$value;
   	     	$eselprimero=2;
   	     }
		    if ($value==null)
		    {
   	     	$eselprimero=2;
			  }

        if (isset($miarreglo[$key]) && $miarreglo[$key]==31)
          if ($value < 0)
            echo "<td align=right ><font color='red';>".number_format ($value,2).Espacios(1)."</font> </td>";
          else
            echo "<td align=right>".number_format ($value,2).Espacios(1)." </td>";
        elseif (isset($miarreglo[$key]) && $miarreglo[$key]==32)
          echo "<td align=right>".number_format ($value,2)."%".Espacios(1)."</td>";
        elseif (isset($miarreglo[$key]) && $miarreglo[$key]==33)
          if ($value < 0)
            echo "<td align=right >".Espacios(1)."<font color='red';>$".number_format ($value,2).Espacios(1)."</font></td>";
          else
            echo "<td align=right>".Espacios(1)."$".number_format ($value,2).Espacios(1)."</td>";
								elseif (isset($miarreglo[$key]) && $miarreglo[$key]==34)
          if ($value < 0)
            echo "<td align=right ><font color='red';>".number_format ($value,4)."</font></td>";
          else
            echo "<td align=right>".number_format ($value,4).Espacios(1)."</td>";
								elseif (isset($miarreglo[$key]) && $miarreglo[$key]==35)
							{
							if($value=='Registro')
								echo "<td align=center><b>".$value."</b></td>";
							elseif($value=='Inactiva')
								echo "<td align=center><font color='#727272'><b>".$value."</b></font></td>";
							elseif($value=='Bloqueo parcial')
								echo "<td align=center><font color='#bf3f00'><b>".$value."</b></font></td>";
							elseif($value=='Bloqueo definitivo')
								echo "<td align=center><font color='#980000'><b>".$value."</b></font></td>";
							elseif($value=='Activa')
								echo "<td align=center><font color='#1b831b'><b>".$value."</b></font></td>";
							}
							elseif (isset($miarreglo[$key]) && $miarreglo[$key]==36)
							{
								echo "<td align=left width=20%>".$value."</td>";
							}
	     elseif (isset($miarreglo[$key]) && $miarreglo[$key]==21)
          echo "<td align=center>".fechademysql($value)."</td>";
	     elseif (isset($miarreglo[$key]) && $miarreglo[$key]==22)
	     {
          echo "<td align=center>".fechademysqlh($value)."</td>";
        }
	     elseif (isset($miarreglo[$key]) && $miarreglo[$key]==2)
	       if ($miCharEspecial==1)
            echo "<td align=center>".Espacios(1).utf8_decode($value).Espacios(1)."</td>";
          else
            echo "<td align=center>".Espacios(1).$value.Espacios(1)."</td>";
	  elseif (isset($miarreglo[$key]) && $miarreglo[$key]==3)
	       if ($miCharEspecial==1)
            echo "<td align=right>".utf8_decode($value)."</td>";
          else
            echo "<td align=right>".$value."</td>";
    	  elseif (isset($miarreglo[$key]) && $miarreglo[$key]==5)
        {
          if ($value==1 || $value=="Si")
	         echo "<td align=center>Si</td>";
	       else
     	      echo "<td align=center>No</td>";
        }
    	  elseif (isset($miarreglo[$key]) && $miarreglo[$key]==6)
        {
          if ($value==1 || $value=="Si")
	         echo "<td align=center><input type=\"checkbox\" value=1 checked></td>";
	       else
	         echo "<td align=center><input type=\"checkbox\" value=0></td>";
        }
        else
   	  {
   	  	 if ($miCharEspecial==1)
            echo "<td>".utf8_decode($value)."</td>";
          else
   	     echo "<td>".$value."</td>";
   	  }
   	  $miactiva=0;
        foreach($mistotales as $i=>$col2 )
        {
         	if (($key+1)==$mistotales[$i][2])
          	  $mistotales[$i][4]+=$value;
        }
   	}
   	else
   	 {
      $miactiva=1;
     }
   }
//jesus
     if ((preg_match('/Borrar/',$_SESSION["miNivelUsuario"]) || preg_match('/Todo/',$_SESSION["miNivelUsuario"])) && $vmiborrar==1)
     {
      if (count($misArreglosModificar)>0)
      {
        echo "<td align=center><select name='ArregloModificar".$_SESSION["misRegistrosEnPantalla"]."' onChange='submit();'>";
        echo "<option value=''>Seleccionar</option>";
         foreach($misArreglosModificar as $valoree)
         {
           echo "<option value='".$valoree[0]."=$elvalorclave'>".$valoree[1]."</option>";
         }
        echo "</select></td>";
      }
      else
       	echo "<td align=center><a href=\"$paginaaenviar?$textborrar=$elvalorclave\" title=\"$textborrar el registro marcado\">$textborrar</a></td>";
     }
     if ($vAdicional==1)
     {
      if (count($misArreglosImprimir)>0)
      {
        echo "<td align=center><select name='ArregloImprimir".$_SESSION["misRegistrosEnPantalla"]."' onChange='submit();' >";
        echo "<option value=''>Seleccionar</option>";
         foreach($misArreglosImprimir as $valoree)
         {
           echo "<option value='".$valoree[0]."=$elvalorclave'>".$valoree[1]."</option>";
         }
        echo "</select></td>";
      }
      else
      	echo "<td align=center><a href=\"$paginaaenviar?$textadicional=$elvalorclave\" title=\"$textadicional registro  marcado\">$textadicional</a></td>";
	   }
     echo "</tr>";
   }
   if (count($mistotales)>0)
   {
     echo "<tr id=\"columnaresaltada\">";

    foreach($mistotales as $i=>$col2)
    {
      if ($mistotales[$i][3]>0)
      {
       for ($y=1;$y<=$mistotales[$i][3];$y++)
       {
         echo "<td></td>";
       }
      }
      if($mistotales[$i][4]>=0)
        echo "<td align=right><b>".number_format ($mistotales[$i][4],2)."</b></td>";
      else
        echo "<td align=right><font color=red><b>".number_format ($mistotales[$i][4],2)."</b></font></td>";
    }
    echo "</tr>";
   }
   echo "</tr><tr><td colspan=$totalcolumnas>";

   if ($_SESSION["NumeroTotalPaginas"]>1 && $conPaginador==1)
   {
				if(isset($_GET['PagInicio']))
					$_SESSION["PaginaActiaActual"]=1;
				if(isset($_GET['PagFin']))
					$_SESSION["PaginaActiaActual"]=$_SESSION["NumeroTotalPaginas"];
				if(isset($_POST['miPaginaIr']))
					$_SESSION["PaginaActiaActual"]=$_POST['miPaginaIr'];

     echo "<table id=\"mitabla\" border=0 cellspacing=0 cellpadding=0><tr>";
     echo ConvertirHTML("<tr><td width='25%'>Página <b>".$_SESSION["PaginaActiaActual"]."</b> de <b>".$_SESSION["NumeroTotalPaginas"]."</b>, No. registros <b>".$_SESSION["RegistrosTotalesTabla"]."</b></td>");

					if($_SESSION["NumeroTotalPaginas"]>1 && $_SESSION["PaginaActiaActual"]>1)
						echo ConvertirHTML("<td align=center width='10%'><a href=\"$paginaaenviar?PagInicio=".($_SESSION["PaginaActiaActual"])."\" title=\"Regresa al Inicio\">Inicio</a></td>");
						else
						echo "<td align=center width='10%'></td>";
     if ($_SESSION["PaginaActiaActual"]>1)
       echo ConvertirHTML("<td align=center width='20%'><a href=\"$paginaaenviar?PagAnterior=1\" title=\"Regresa una página\">Página Anterior</a></td>");
							else
							echo "<td align=center width='20%'></td>";
					if ($_SESSION["NumeroTotalPaginas"]>1)
					{
						echo ConvertirHTML("<td align=center width='10%'>Ir a Página: ");
						echo "<select name='miPaginaIr' onChange=\"submit();\">";

						$Contador=1;
						while($Contador<=$_SESSION["NumeroTotalPaginas"])
					{
							$CadenaDespliegue.="SELECT ".($Contador)." AS Pagina";
							if($Contador<$_SESSION["NumeroTotalPaginas"])
									$CadenaDespliegue.=" UNION ";
							$Contador=$Contador+1;
					}
         $link=Conectarse();
         $result=mysqli_query($link,$CadenaDespliegue);
         while ($row22=mysqli_fetch_array($result))
         {
           if ($_POST['miPaginaIr']==$row22['Pagina'])
             printf("<option selected value=".$row22['Pagina'].">".$row22['Pagina']."</option>");
           else
             printf("<option value=".$row22['Pagina'].">".$row22['Pagina']."</option>");
         }
         echo "</select>";
						echo "</td>";
					}
							else
							echo "<td align=center width='10%'></td>";
     if ($_SESSION["PaginaActiaActual"]<$_SESSION["NumeroTotalPaginas"])
       echo ConvertirHTML("<td align=center width='20%'><a href=\"$paginaaenviar?PagSiguiente=1\" title=\"Avanza una página\">Página Siguiente</a></td>");
							else
					echo "<td align=center width='20%'></td>";
					if($_SESSION["NumeroTotalPaginas"]>1 && $_SESSION["PaginaActiaActual"]<$_SESSION["NumeroTotalPaginas"])
						echo ConvertirHTML("<td align=center width='10%'><a href=\"$paginaaenviar?PagFin=".($_SESSION["NumeroTotalPaginas"])."\" title=\"Ir al Fin\">Fin</a></td>");
						else
						echo "<td align=center width='10%'></td>";
     echo "</tr></table>";
   }
   elseif ($conPaginador==1)
     echo ConvertirHTML("Página <b>".$_SESSION["PaginaActiaActual"]."</b> de <b>".$_SESSION["NumeroTotalPaginas"]."</b>, No. registros <b>".$_SESSION["RegistrosTotalesTabla"]."</b>");

   echo "</td></tr></div></table></form>";
  }

  /* Redondea un numero dependiendo del monto a entero o a .50 centavos */
  function redondeaespecial($cadenanumero, $TipoRedondeo=1)
  {
 //    if ($cadenanumero>20)
 //      return round($cadenanumero,0);
 //    else
 //    {
       $minumerodecimal=round($cadenanumero,1);
	     if (strpos($minumerodecimal, "." )>0)
	     {
	     	 if ($TipoRedondeo==1)
	     	 {
           $midecimal=round(substr($minumerodecimal, strpos($minumerodecimal, "." )+1),1);
           if ($midecimal>2 && $midecimal<8)
              $midecimalcadena=".5";
           else
              $midecimalcadena=".0";
           if ($midecimal>7)
             $miCadenaSumar=1;
           else
             $miCadenaSumar=0;

           $micadenaenteros=substr($minumerodecimal,0, strpos($minumerodecimal, "." ));
           $micadenareal=($micadenaenteros+$miCadenaSumar).$midecimalcadena;
           return $micadenareal;
          }
          else
          {
           $minumerodecimal=round($cadenanumero,2);
           $midecimal=round(substr($minumerodecimal, strpos($minumerodecimal, "." )+1),2);
           if ($midecimal>24 && $midecimal<75)
           {
              $midecimalcadena=".50";
            }
           else
              $midecimalcadena=".00";
           if ($midecimal>74)
             $miCadenaSumar=1;
           else
             $miCadenaSumar=0;

           $micadenaenteros=substr($minumerodecimal,0, strpos($minumerodecimal, "." ));
           $micadenareal=($micadenaenteros+$miCadenaSumar).$midecimalcadena;
           return $micadenareal;
          }
        }
	    else
        return round($cadenanumero,0);
  //   }
  }

  // Genera un formato de fecha utilizando funciones selectnum y selectarr
  function leefechas($mifecha)
  {
  	list ($midia,$mimes,$miano) = explode ("/",$mifecha);
  	echo "D&#237;a ";
  	selectnum('dia', 1, 31, $midia);
  	$mesArray = array(
       1 => "Enero",
       2 => "Febrero",
       3 => "Marzo",
       4 => "Abril",
       5 => "Mayo",
       6 => "Junio",
       7 => "Julio",
       8 => "Agosto",
       9 => "Septiembre",
      10 => "Octubre",
      11 => "Noviembre",
      12 => "Diciembre"
     );
    echo " Mes ";
  	selectarr('mes', $mesArray, $mimes);
  	echo " A&#241;o ";
  	selectnum('ano', 2005,2030, $miano);
  }

  // Genera un formato de hora utilizando funciones
  function leehoras($nombre, $miHora, $minimo1=0, $maximo1=24, $misalto=30)
  {

   $txt= "<select name='$nombre' id='$nombre'>";

	for ($i=$minimo1; $i<$maximo1; $i+$misalto)
	{
    	$txt .= "<option value='$i'>". $i . '</option>';
	}
	$txt .= '</select>';

	echo $txt;

	return $txt;

  }

  //Compara 2 fechas
  function fechasiguales($fecha,$fecha2)
  {
    preg_match( "/([0-9]{2,4})/([0-9]{1,2})/([0-9]{1,2})/", $fecha, $mifecha);
    $lafecha1=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
     preg_match( "/([0-9]{2,4})/([0-9]{1,2})/([0-9]{1,2})/", $fecha2, $mifecha);
    $lafecha2=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
    if ($lafecha1!=$lafecha2)
      return false;
    else
      return true;
  }

  //Compara fecha mayor que fecha2
  function fechasmayorque($fecha,$fecha2)
  {
    preg_match( "/([0-9]{2,4})/([0-9]{1,2})/([0-9]{1,2})/", $fecha, $mifecha);
    $lafecha1=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
     preg_match( "/([0-9]{2,4})/([0-9]{1,2})/([0-9]{1,2})/", $fecha2, $mifecha);
    $lafecha2=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
    if ($lafecha1<=$lafecha2)
      return false;
    else
      return true;
  }

  //Convierte fecha de mysql a normal
  function fechademysql($fecha)
  {
    preg_match( "/([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})/", $fecha, $mifecha);
    $lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
    return $lafecha;
  }
  //Convierte fecha de mysql a normal
  function fechademysqlh($fecha)
  {
    preg_match( "/([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})/", $fecha, $mifecha);
    $lahora=substr($fecha,11);
    $lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1]." ".$lahora;
    return $lafecha;
  }
  //Convierte fecha de normal a mysql
  function fechaamysql($fecha)
  {
    preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $fecha, $mifecha);
    $lafecha=$mifecha[3]."-".$mifecha[2]."-".$mifecha[1];
    return $lafecha;
  }

     //Convierte fecha de normal a mysql
     function fechaamysqlh($fecha)
     {
       preg_match( "/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $fecha, $mifecha);
       $lahora=substr($fecha,11);
       $lafecha=$mifecha[3]."-".$mifecha[2]."-".$mifecha[1];
       return $lafecha.' '.$lahora;
     }

  //Convierte y valida fecha a normal a mysql
  function fechavalidadmA($fecha)
  {
    preg_match( "/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $fecha, $mifecha);

   if (checkdate($mifecha[2],$mifecha[1],$mifecha[3]))

    $lafecha=$mifecha[3]."-".$mifecha[2]."-".$mifecha[1];
   else
    $lafecha='0000-00-00';

   return $lafecha;
  }


  //Extrae el aÃÂ±o de la fecha
  function fechaAnio($fecha)
  {
    preg_match( "/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $fecha, $mifecha);
    $lafecha=$mifecha[3];
    return $lafecha;
  }


  //Convierte fecha de mysql a normal con nombre de mes
  function fechademysqlnombre($fecha)
  {
    preg_match( "/([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})/", $fecha, $mifecha);
    $lafecha=$mifecha[3]."  ".fnombremes($mifecha[2])."  ".$mifecha[1];
    return $lafecha;
  }

// regresa el nombre del mes pasado (1-12)
function fnombremes($mnumeromes)
{
  $mesesN=array(1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio",
                 "Agosto","Septiembre","Octubre","Noviembre","Diciembre");
  if ((int)$mnumeromes>0 && (int)$mnumeromes<13)
    return $mesesN[(int)$mnumeromes];
  else
   return "No valido";
}
  // convierte a caracteres la fecha pasada
function FechaFormateada($FechaStamp)
{
  $ano = date('Y',$FechaStamp);
  $mes = date('n',$FechaStamp);
  $dia = date('d',$FechaStamp);
  $diasemana = date('w',$FechaStamp);
 // $hora= date('g:i a',$FechaStamp);
  $diassemanaN= array("Domingo","Lunes","Martes","Miercoles",
                      "Jueves","Viernes","Sabado");
  $mesesN=array(1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio",
                 "Agosto","Septiembre","Octubre","Noviembre","Diciembre");
  // $diassemanaN[$diasemana].   si quieres el nombre del dia
  return $diassemanaN[$diasemana]." $dia de ". $mesesN[$mes] ." de $ano ";
}

function FechaFormateadaSinDiaSemana($FechaStamp)
{
  $ano = date('Y',$FechaStamp);
  $mes = date('n',$FechaStamp);
  $dia = date('d',$FechaStamp);
  $diasemana = date('w',$FechaStamp);
 // $hora= date('g:i a',$FechaStamp);
  $diassemanaN= array("Domingo","Lunes","Martes","Miercoles",
                      "Jueves","Viernes","Sabado");
  $mesesN=array(1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio",
                 "Agosto","Septiembre","Octubre","Noviembre","Diciembre");
  // $diassemanaN[$diasemana].   si quieres el nombre del dia
  return " $dia dias del mes de ". $mesesN[$mes] ." del año $ano ";
}


function FechaFormateada2($FechaStamp)
{
  $ano = date('Y',$FechaStamp);
  $mes = date('n',$FechaStamp);
  $dia = date('d',$FechaStamp);
  $diasemana = date('w',$FechaStamp);
 // $hora= date('g:i a',$FechaStamp);
  $diassemanaN= array("Domingo","Lunes","Martes","Miercoles",
                      "Jueves","Viernes","Sabado");
  $mesesN=array(1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio",
                 "Agosto","Septiembre","Octubre","Noviembre","Diciembre");
  // $diassemanaN[$diasemana].   si quieres el nombre del dia
  return "a los $dia dias del mes de ".$mesesN[$mes] ." del $ano ";
}


//Convierte fecha de normal a mysql
function fechaUnDato($fecha,$tipo)
{
  preg_match( "/([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})/", $fecha, $mifecha);
  if ($tipo=="Dia")
   $lafecha=$mifecha[1];
  if ($tipo=="Mes")
   $lafecha=$mifecha[2];
  if ($tipo=="Anio")
   $lafecha=$mifecha[3];

  return $lafecha;
}


function fechaactual($tipo)
{
  if ($tipo==1)
   return date("d/m/Y H:i");
  elseif ($tipo==2)
   return date("d/m/Y");
  elseif ($tipo==3)
   return date("H:i:s");
  elseif ($tipo==4)
   return date("Y/m/d");
  elseif ($tipo==5)
   return date("Y/m/d H:i");

}
// Aplicar comillas sobre la variable para hacerla segura
function comillas_inteligentes($valor)
{
    // Retirar las barras
    if (get_magic_quotes_gpc()) {
        $valor = stripslashes($valor);
    }

    // Colocar comillas si no es entero
    if (!is_numeric($valor)) {
        $valor = "'" . mysqli_real_escape_string($link,$valor) . "'";
    }
    return $valor;
}

// Creamos la semilla para la funci?n rand()
function crear_semilla()
{
   list($usec, $sec) = explode(' ', microtime());
   return (float) $sec + ((float) $usec * 100000);
}

// convierte a numeros decimales a dos digitos
function estraedecimal($elnumero)
{
    $aux = (string) $elnumero;
	if (strpos($aux, "." )>0)
      return substr( $aux, strpos($aux, "." )+1 );
	else
      return "00";
}
function calculaelCAT($MontoPrestamo, $latasa, $Meses, $MontoExtra)
{
   if ($MontoExtra==0)
    return number_format(((pow((1+($latasa/100)),12)-1)*100),2);
   else
   {
      $latasa=$latasa+0.1;
         return number_format(((($Meses/100)+1)*$latasa),2);
  //        return number_format(((pow((1+($latasa/100)),12)-1)*100),2);

   }
}
// Funcion tir de prueba
function tir($f0,$f1,$f2,$f3,$f4,$f5,$f6,$f7,$f8,$f9,$f10,$taza)
{
  $taza_ant=0.0;
  $taza_ult=0.0;
  $vpn=($f0)+($f1/(1+$taza))+($f2/pow((1+$taza),2))+($f3/pow((1+$taza),3))+($f4/pow((1+$taza),4))+($f5/pow((1+$taza),5))+($f6/pow((1+$taza),6))+($f7/pow((1+$taza),7))+($f8/pow((1+$taza),8))+($f9/pow((1+$taza),9))+($f10/pow((1+$taza),10));
  echo $vpn."inicial <br>";

  if ($vpn>0)
  {
    echo "entro";
    for ($x=$taza;$vpn>0; $x+=.0001)
    {
      $vpn=($f0)+($f1/(1+$x))+($f2/pow((1+$x),2))+($f3/pow((1+$x),3))+($f4/pow((1+$x),4))+($f5/pow((1+$x),5))+($f6/pow((1+$x),6))+($f7/pow((1+$x),7))+($f8/pow((1+$x),8))+($f9/pow((1+$x),9))+($f10/pow((1+$x),10));
      $taza_ant=$x-.0001;
      $taza_ult=$x;
      if($x>1)
      {
        $taza_ult="NA";
        return $taza_ult;
      }
      echo $vpn."-".$x."<br>";
    }
    /*echo "taza anterior".$taza_ant."<br>";
    echo "taza final".$taza_ult."<br>";*/
    return $taza_ult*100;
  }
  else
  {
    for ($x=$taza;$vpn<0; $x-=.0001)
    {
       $vpn=($f0)+($f1/(1+$x))+($f2/pow((1+$x),2))+($f3/pow((1+$x),3))+($f4/pow((1+$x),4))+($f5/pow((1+$x),5))+($f6/pow((1+$x),6))+($f7/pow((1+$x),7))+($f8/pow((1+$x),8))+($f9/pow((1+$x),9))+($f10/pow((1+$x),10));
       $taza_ant=$x+.0001;
       $taza_ult=$x;
       if($x<-1)
       {
         $taza_ult="NA";
         return $taza_ult;
       }
       echo $vpn."-".$x."<br>";
    }
    /*echo "taza anterior".$taza_ant."<br>";
    echo "taza final".$taza_ult."<br>";*/
    // number_format(((pow((1+($latasa/100)),12)-1)*100),2)    $taza_ult*100;
    return $taza_ult*100;
  }

}//fin de la funcion


function dameclave()
{
  srand(crear_semilla());

  // Generamos la clave
  $clave="";
  $max_chars = round(rand(7,10));  // tendr? entre 7 y 10 caracteres
  $chars = array();
  for ($i="a"; $i<"z"; $i++) $chars[] = $i;  // creamos vector de letras
  $chars[] = "z";
  for ($i=0; $i<$max_chars; $i++)
  {
    $letra = round(rand(0, 1));  // primero escogemos entre letra y n?mero
    if ($letra) // es letra
	  $clave .= $chars[round(rand(0, count($chars)-1))];
    else // es numero
	  $clave .= round(rand(0, 9));
  }
  return $clave;
}
function num2letras($num=0, $fem = true, $dec = true) {
//if (strlen($num) > 14) die("El n?mero introducido es demasiado grande");
 if ($num==100)
   return 'Cien ';
 elseif ($num==100000)
   return 'Cien mil ';
 else
 {
   $matuni[2]  = "dos";
   $matuni[3]  = "tres";
   $matuni[4]  = "cuatro";
   $matuni[5]  = "cinco";
   $matuni[6]  = "seis";
   $matuni[7]  = "siete";
   $matuni[8]  = "ocho";
   $matuni[9]  = "nueve";
   $matuni[10] = "diez";
   $matuni[11] = "once";
   $matuni[12] = "doce";
   $matuni[13] = "trece";
   $matuni[14] = "catorce";
   $matuni[15] = "quince";
   $matuni[16] = "dieciseis";
   $matuni[17] = "diecisiete";
   $matuni[18] = "dieciocho";
   $matuni[19] = "diecinueve";
   $matuni[20] = "veinte";

   $matunisub[2] = "dos";
   $matunisub[3] = "tres";
   $matunisub[4] = "cuatro";
   $matunisub[5] = "quin";
   $matunisub[6] = "seis";
   $matunisub[7] = "sete";
   $matunisub[8] = "ocho";
   $matunisub[9] = "nove";

   $matdec[2] = "veint";
   $matdec[3] = "treinta";
   $matdec[4] = "cuarenta";
   $matdec[5] = "cincuenta";
   $matdec[6] = "sesenta";
   $matdec[7] = "setenta";
   $matdec[8] = "ochenta";
   $matdec[9] = "noventa";

   $matsub[3]  = 'mill';
   $matsub[5]  = 'bill';
   $matsub[7]  = 'mill';
   $matsub[9]  = 'trill';
   $matsub[11] = 'mill';
   $matsub[13] = 'bill';
   $matsub[15] = 'mill';

   $matmil[4]  = 'millones';
   $matmil[6]  = 'billones';
   $matmil[7]  = 'de billones';
   $matmil[8]  = 'millones de billones';
   $matmil[10] = 'trillones';
   $matmil[11] = 'de trillones';
   $matmil[12] = 'millones de trillones';
   $matmil[13] = 'de trillones';
   $matmil[14] = 'billones de trillones';
   $matmil[15] = 'de billones de trillones';
   $matmil[16] = 'millones de billones de trillones';

   $num = trim((string)@$num);
   if ($num[0] == '-') {
      $neg = ' ';
      $num = substr($num, 1);
   }else
      $neg = '';

	if(!empty($num))
	{
		while ($num[0] == '0') $num = substr($num, 1);
   if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
   $zeros = true;
	}

   $punt = false;
   $ent = '';
   $fra = '';
   for ($c = 0; $c < strlen($num); $c++) {
      $n = $num[$c];
      if (! (strpos(".,'''", $n) === false)) {
         if ($punt) break;
         else{
            $punt = true;
            continue;
         }

      }elseif (! (strpos('0123456789', $n) === false)) {
         if ($punt) {
            if ($n != '0') $zeros = false;
            $fra .= $n;
         }else

            $ent .= $n;
      }else

         break;

   }
   $ent = '     ' . $ent;
   if ($dec and $fra and ! $zeros) {
      $fin = ' ';
      for ($n = 0; $n < strlen($fra); $n++) {
         if (($s = $fra[$n]) == '0')
            $fin .= ' cero';
         elseif ($s == '1')
            $fin .= $fem ? ' una' : ' un';
         else
            $fin .= ' ' . $matuni[$s];
      }
   }else
      $fin = '';
   if ((int)$ent === 0) return 'Cero ' . $fin;
   $tex = '';
   $sub = 0;
   $mils = 0;
   $neutro = false;
   while ( ($num = substr($ent, -3)) != '   ') {
      $ent = substr($ent, 0, -3);
      if (++$sub < 3 and $fem) {
         $matuni[1] = 'una';
         $subcent = 'os';
      }else{
         $matuni[1] = $neutro ? 'un' : 'uno';
         $subcent = 'os';
      }
      $t = '';
      $n2 = substr($num, 1);
      if ($n2 == '00') {
      }elseif ($n2 < 21)
         $t = ' ' . $matuni[(int)$n2];
      elseif ($n2 < 30) {
         $n3 = $num[2];
         if ($n3 != 0) $t = 'i' . $matuni[$n3];
         $n2 = $num[1];
         $t = ' ' . $matdec[$n2] . $t;
      }else{
         $n3 = $num[2];
         if ($n3 != 0) $t = ' y ' . $matuni[$n3];
         $n2 = $num[1];
         $t = ' ' . $matdec[$n2] . $t;
      }
      $n = $num[0];
      if ($n == 1) {
		if($num[1]==0 && $num[2]==0)
		 $t = ' cien' . $t;
		else
         $t = ' ciento' . $t;
      }elseif ($n == 5){
         $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
      }elseif ($n != 0){
         $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
      }
      if ($sub == 1) {
      }elseif (! isset($matsub[$sub])) {
         if ($num == 1) {
            $t = ' mil';
         }elseif ($num > 1){
            $t .= ' mil';
         }
      }elseif ($num == 1) {
         $t .= ' ' . $matsub[$sub] . 'on';
      }elseif ($num > 1){
         $t .= ' ' . $matsub[$sub] . 'ones';
      }
      if ($num == '000') $mils ++;
      elseif ($mils != 0) {
         if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub];
         $mils = 0;
      }
      $neutro = true;
      $tex = $t . $tex;
   }
   $tex = $neg . substr($tex, 1) . $fin;
   return ucfirst($tex);
  }
}

// Clase para paginar
class QueryLimit{

    var $error ='';

    var $data = array(
        'total_rows'=>0,
        'page_size'=>10,
        'page'=>1
    );

    var $limit_string = '';
    var $limits = array(0,0);
    var $total_pages = 0;

    function QueryLimit($params=array()){
        foreach($params as $param=>$val){
            if(isset($this->data[$param])){
                $this->data[$param] = intval($val);
            }
        }
        $this->limits[1]=$this->data['page_size'];
    }

    function getLimitString()
    {
        if($this->data['page_size']<1)
        {
            $this->error = 'Tama&#241;o de pagina (lineas por pagina) si no tiene ingrese 0';
            return false;
        }
        $this->total_pages = ($this->data['total_rows']==0)?'1': ceil($this->data['total_rows']/$this->data['page_size']);
        $_SESSION["NumeroTotalPaginas"]=$this->total_pages;

        if($this->data['page']<1 || $this->data['page']>$this->total_pages)
        {
            $this->error = 'Numero de pagina invalido';
            return false;
        }

        $this->limits[0] = $this->data['page_size']*($this->data['page']-1);
        return ($this->limit_string = ' LIMIT '.implode(',',$this->limits).' ');
    }

    function getTotalPages(){
        return $this->$total_pages;
    }

    function setPageSize($page_size){
        $this->data['page_size']=intval($page_size);
    }

    function setPage($p){
        $this->data['page']=intval($p);
    }

    function nextPage(){
        $this->data['page']++;
    }

    function previousPage(){
        $this->data['page']--;
    }
}


//respaldar_tablas('servidor','usuario','contrasena','bd');


// para respaldar una tabla, paselo como parametro
function respaldar_tablas($tables = '*')
{


   $link=Conectarse();

   //get all of the tables
   if($tables == '*')
   {
      $tables = array();
      $result = mysqli_query($link,'SHOW TABLES');
      while($row = mysqli_fetch_row($result))
      {
         $tables[] = $row[0];
      }
   }
   else
   {
      $tables = is_array($tables) ? $tables : explode(',',$tables);
   }
   $nombreArchivo='../respaldos/dbrespaldo-'.time().'-'.(md5(implode(',',$tables))).'.sql';
echo $nombreArchivo;
   //cycle through
   foreach($tables as $table)
   {
      $result = mysqli_query($link,'SELECT * FROM '.$table);
      $num_fields = mysqli_num_fields($result);

      $return.= 'DROP TABLE '.$table.';';
      $row2 = mysqli_fetch_row(mysqli_query($link,'SHOW CREATE TABLE '.$table));
      $return.= "\n\n".$row2[1].";\n\n";

    for ($i = 0; $i < $num_fields; $i++)
      {
         while($row = mysqli_fetch_row($result))
         {
            $return.= 'INSERT INTO '.$table.' VALUES(';
            for($j=0; $j<$num_fields; $j++)
            {
               $row[$j] = addslashes($row[$j]);
               $row[$j] = preg_match("/\n/","\\n",$row[$j]);
               if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
               if ($j<($num_fields-1)) { $return.= ','; }
            }
            $return.= ");\n";
         }
      }
      $return.="\n\n\n";
   }

   //gravar archivo

   $handle = fopen($nombreArchivo,'w+');
   fwrite($handle,$return);
   fclose($handle);
}

// deja solo numeros de una cadena y puede convertirlos a una longitud especifica.
function LimpiaNumeros($lacadena = '',$numeroEspecifico=0,$lanumerodigitos=10)
{

	 $numero_limpio=preg_replace("/[^0-9]/",'',$lacadena);
	 if ($numeroEspecifico==1)
	  {
      if (strlen($numero_limpio) < $lanumerodigitos)
       {
        $relleno = str_repeat('0', $lanumerodigitos-strlen($numero_limpio));
       }
       return $relleno . $numero_limpio;
	  }
    else
    {
       return $numero_limpio;
    }
}

function LimpiaCaracterEspecial($Cadena)
{
    $Cadena=str_replace("'","",$Cadena);

    return $Cadena;
}

  function rfcset($a, $b)
    { #una manera de asignar defaults.
        if(!$a)
            return $b;
        else
            return $a;
    }

    class RFC {
        var $nombre, $apellidoPaterno, $apellidoMaterno, $fecha, $rfc, $nombreProcesado;
        function RFC($nombre, $apellidoPaterno, $apellidoMaterno, $fecha, $soloHomoclave = false)
        { #formato de fecha: YYYY-MM-DD
            $this->nombre = strtoupper(trim($nombre));


            /* Quitamos los artÃ­culos de los apellidos */
            $this->apellidoPaterno = $this->QuitarArticulos(strtoupper(trim($apellidoPaterno)));
            $this->apellidoMaterno = $this->QuitarArticulos(strtoupper(trim($apellidoMaterno)));

            $this->nombreProcesado = $this->apellidoPaterno . " " . $this->apellidoMaterno . " " . $this->QuitaNombreComun($this->nombre);

            //Agregamos el primer caracter del apellido paterno
            $this->rfc = substr($this->apellidoPaterno, 0, 1);

            //Buscamos y agregamos al rfc la primera vocal del primer apellido
            for($i = 1; $i < strlen($this->apellidoPaterno); $i++)
            {
                $c = $this->apellidoPaterno[$i];
                if ($this->EsVocal($c))
                {
                    $this->rfc .= $c;
                    break;
                }
            }

            //Agregamos el primer caracter del apellido materno
            $this->rfc .= substr($this->apellidoMaterno, 0, 1);

            //Agregamos el primer caracter del primer nombre, o los 2 primeros en caso de no haber apellido materno
            $this->rfc .= substr($this->QuitaNombreComun($this->nombre), 0, $this->apellidoMaterno ? 1 : 2);

            //Revisamos es una "palabra mala"
            $this->rfc = $this->QuitaPalabraMala($this->rfc);

            /* Agregamos la fecha yyyy-mm-dd (por ejemplo: 1968-08-25 = 25 de agosto de 1968);
               por si las dudas, ponemos un cero a la izquierda de los meses y dias < 10 */
            $efecha = explode("-", $fecha);
            $efecha[0] = substr($efecha[0], 2, 2);
            $efecha[1] = str_pad($efecha[1], 2, '0', STR_PAD_LEFT);
            $efecha[2] = str_pad($efecha[2], 2, '0', STR_PAD_LEFT);
            $fecha = implode("", $efecha);
            $this->rfc .= $fecha;

            //Le agregamos la homoclave al rfc
            $homoclave = $this->CalcularHomoclave($this->apellidoPaterno . " " . $this->apellidoMaterno . " " . $this->nombre);
            if(!$soloHomoclave)
                $this->rfc .= $homoclave;
            else
                $this->rfc = $homoclave;
        }

        function EsVocal($letra)
        {
            $letra = strtoupper($letra);
            $vocales = array("A", "E", "I", "O", "U", "Ã", "Ã", "Ã", "Ã", "Ã");
            if(in_array($letra, $vocales))
                return true;
            else
                return false;
        }

        function QuitarArticulos($palabra)
        {
            #$palabra = preg_replace('/\b(DE(L)?|LA(S)?|LOS|Y|A|MAC|VON|VAN)\s+/i', '', $palabra);
            #$palabra = preg_replace('/\bMC/', '', $palabra);
            $palabra = preg_replace('/\b(DE(L)?|LA(S)?|LOS|Y|A|VON|VAN)\s+/i', '', $palabra);
            return $palabra;
        }

        function QuitaNombreComun($palabra)
        {
            $palabra = preg_replace('/\b(J(OSE|\.)?|MA(RIA|\.)?)\s+/i', '', $palabra);
            #$palabra = preg_replace('/\b(DE(L)?|LA(S)?|LOS|Y|A|MAC|VON|VAN)\s+/i', '', $palabra);
            #$palabra = preg_replace('/\bMC/', '', $palabra);
            $palabra = preg_replace('/\b(DE(L)?|LA(S)?|LOS|Y|A|VON|VAN)\s+/i', '', $palabra);
            return $palabra;
        }

        function QuitaPalabraMala($palabra)
        {
            $posicion = 4;
            $malas = array("BUEI", "BUEY", "CACA", "CACO", "CAGA", "CAGO", "CAKA", "CAKO", "COGE", "COJA",
                           "KOGE", "KOJO", "KAKA", "KULO", "MAME", "MAMO", "MEAR", "MEAS", "MEON", "MION",
                           "COJE", "COJI", "COJO", "CULO", "FETO", "GUEY", "JOTO", "KACA", "KACO", "KAGA",
                           "KAGO", "MOCO", "MULA", "PEDA", "PEDO", "PENE", "PUTA", "PUTO", "QULO", "RATA",
                           "RUIN");
            if(in_array($palabra, $malas))
                $palabra = substr_replace($palabra, "X", $posicion - 1, 1);
            return $palabra;
        }

        function CalcularHomoclave($nombreCompleto)
        {
            $nombreEnNumero = ""; //Guardara el nombre en su correspondiente numÃ©rico
            $valorSuma = 0; //La suma de la secuencia de nÃºmeros de nombreEnNumero

            #Tablas para calcular la homoclave.
            #Estas tablas realmente no se porque son como son. solo las copie de lo que encontrÃ© en internet

            #TablaRFC 1
            $tablaRFC1 = array();
            $tablaRFC1["&"]=10;
            $tablaRFC1["Ã"]=10;
            $tablaRFC1["A"]=11;
            $tablaRFC1["B"]=12;
            $tablaRFC1["C"]=13;
            $tablaRFC1["D"]=14;
            $tablaRFC1["E"]=15;
            $tablaRFC1["F"]=16;
            $tablaRFC1["G"]=17;
            $tablaRFC1["H"]=18;
            $tablaRFC1["I"]=19;
            $tablaRFC1["J"]=21;
            $tablaRFC1["K"]=22;
            $tablaRFC1["L"]=23;
            $tablaRFC1["M"]=24;
            $tablaRFC1["N"]=25;
            $tablaRFC1["O"]=26;
            $tablaRFC1["P"]=27;
            $tablaRFC1["Q"]=28;
            $tablaRFC1["R"]=29;
            $tablaRFC1["S"]=32;
            $tablaRFC1["T"]=33;
            $tablaRFC1["U"]=34;
            $tablaRFC1["V"]=35;
            $tablaRFC1["W"]=36;
            $tablaRFC1["X"]=37;
            $tablaRFC1["Y"]=38;
            $tablaRFC1["Z"]=39;
            $tablaRFC1["0"]=0;
            $tablaRFC1["1"]=1;
            $tablaRFC1["2"]=2;
            $tablaRFC1["3"]=3;
            $tablaRFC1["4"]=4;
            $tablaRFC1["5"]=5;
            $tablaRFC1["6"]=6;
            $tablaRFC1["7"]=7;
            $tablaRFC1["8"]=8;
            $tablaRFC1["9"]=9;

            #region TablaRFC 2
            $tablaRFC2 = array();
            $tablaRFC2[0]= "1";
            $tablaRFC2[1]= "2";
            $tablaRFC2[2]= "3";
            $tablaRFC2[3]= "4";
            $tablaRFC2[4]= "5";
            $tablaRFC2[5]= "6";
            $tablaRFC2[6]= "7";
            $tablaRFC2[7]= "8";
            $tablaRFC2[8]= "9";
            $tablaRFC2[9]= "A";
            $tablaRFC2[10]= "B";
            $tablaRFC2[11]= "C";
            $tablaRFC2[12]= "D";
            $tablaRFC2[13]= "E";
            $tablaRFC2[14]= "F";
            $tablaRFC2[15]= "G";
            $tablaRFC2[16]= "H";
            $tablaRFC2[17]= "I";
            $tablaRFC2[18]= "J";
            $tablaRFC2[19]= "K";
            $tablaRFC2[20]= "L";
            $tablaRFC2[21]= "M";
            $tablaRFC2[22]= "N";
            $tablaRFC2[23]= "P";
            $tablaRFC2[24]= "Q";
            $tablaRFC2[25]= "R";
            $tablaRFC2[26]= "S";
            $tablaRFC2[27]= "T";
            $tablaRFC2[28]= "U";
            $tablaRFC2[29]= "V";
            $tablaRFC2[30]= "W";
            $tablaRFC2[31]= "X";
            $tablaRFC2[32]= "Y";

            #TablaRFC 3
            $tablaRFC3 = array();
            $tablaRFC3["A"]= 10;
            $tablaRFC3["B"]= 11;
            $tablaRFC3["C"]=12;
            $tablaRFC3["D"]= 13;
            $tablaRFC3["E"]= 14;
            $tablaRFC3["F"]= 15;
            $tablaRFC3["G"]= 16;
            $tablaRFC3["H"]= 17;
            $tablaRFC3["I"]= 18;
            $tablaRFC3["J"]= 19;
            $tablaRFC3["K"]= 20;
            $tablaRFC3["L"]= 21;
            $tablaRFC3["M"]= 22;
            $tablaRFC3["N"]= 23;
            $tablaRFC3["O"]= 25;
            $tablaRFC3["P"]= 26;
            $tablaRFC3["Q"]= 27;
            $tablaRFC3["R"]= 28;
            $tablaRFC3["S"]= 29;
            $tablaRFC3["T"]= 30;
            $tablaRFC3["U"]= 31;
            $tablaRFC3["V"]= 32;
            $tablaRFC3["W"]= 33;
            $tablaRFC3["X"]= 34;
            $tablaRFC3["Y"]= 35;
            $tablaRFC3["Z"]= 36;
            $tablaRFC3["0"]= 0;
            $tablaRFC3["1"]= 1;
            $tablaRFC3["2"]= 2;
            $tablaRFC3["3"]= 3;
            $tablaRFC3["4"]= 4;
            $tablaRFC3["5"]= 5;
            $tablaRFC3["6"]= 6;
            $tablaRFC3["7"]= 7;
            $tablaRFC3["8"]= 8;
            $tablaRFC3["9"]= 9;
            $tablaRFC3[""]= 24;
            $tablaRFC3[" "]= 37;

            //agregamos un cero al inicio de la representaciÃ³n nÃºmerica del nombre
            $nombreEnNumero = "0";

            //Recorremos el nombre y vamos convirtiendo las letras en su valor numÃ©rico
            for($i = 0; $i < strlen($nombreCompleto); $i++)
            {
                $c = $nombreCompleto[$i];
                $nombreEnNumero .= rfcset($tablaRFC1[$c], "00");
            }

            //Calculamos la suma de la secuencia de nÃºmeros calculados anteriormente.
            //la formula es:( (el caracter actual multiplicado por diez) mas el valor del caracter siguiente )
            //(y lo anterior multiplicado por el valor del caracter siguiente)
            for ($i = 0; $i < strlen($nombreEnNumero) - 1; $i++)
            {
                $i2 = $i + 1;
                $valorSuma += (($nombreEnNumero[$i] * 10) + $nombreEnNumero[$i2]) * $nombreEnNumero[$i2];
            }

            //Lo siguiente no se porque se calcula asÃ­, es parte del algoritmo.
            //Los magic numbers que aparecen por ahÃ­ deben tener algÃºn origen matemÃ¡tico
            //relacionado con el algoritmo al igual que el proceso mismo de calcular el
            //digito verificador.
            //Por esto no puedo aÃ±adir comentarios a lo que sigue, lo hice por acto de fe.

            $div = 0; $mod = 0;
            $div = $valorSuma % 1000;
            $mod = $div % 34;
            $div = ($div - $mod) / 34;

            $hc = "";  //los dos primeros caracteres de la homoclave
            $hc .= rfcset($tablaRFC2[$div], "Z");
            $hc .= rfcset($tablaRFC2[$mod], "Z");

            //Agregamos al RFC los dos primeros caracteres de la homoclave
            $rfc = $this->rfc;
            $rfc .= $hc;

            //Aqui empieza el calculo del digito verificador basado en lo que tenemos del RFC
            //En esta parte tampoco conozco el origen matemÃ¡tico del algoritmo como para dar
            //una explicaciÃ³n del proceso, asÃ­ que Â¡tengamos fe hermanos!.
            $rfcAnumeroSuma = 0;
            $sumaParcial = 0;
            for($i = 0; $i < strlen($rfc); $i++)
            {
                if($tablaRFC3[$rfc[$i]])
                {
                    $rfcAnumeroSuma = $tablaRFC3[$rfc[$i]];
                    $sumaParcial += ($rfcAnumeroSuma * (14 - ($i + 1)));
                }
            }

            $moduloVerificador = $sumaParcial % 11;
            if($moduloVerificador == 0)
                $hc .= "0";
            else
            {
                $sumaParcial = 11 - $moduloVerificador;
                if ($sumaParcial == 10)
                    $hc .= "A";
                else
                    $hc .= $sumaParcial;
            }
            return $hc;
        }
    }

set_time_limit(0);
function getCurp($primerApellido, $segundoApellido, $nombre, $diaNacimiento, $mesNaciemiento, $anioNacimiento, $sexo, $entidadNacimiento,$miCodigo)
{
$primerApellido = urlencode($primerApellido);
$segundoApellido = urlencode($segundoApellido);
$nombre = urlencode($nombre);
$aContext = array(
    'http' => array(
        'header'=>"Accept-language: es-es,es;q=0.8,en-us;q=0.5,en;q=0.3\r\n" .
              "Proxy-Connection: keep-alive\r\n" .
              "Host: consultas.curp.gob.mx\r\n" .
              "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.0; es-ES; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 (.NET CLR 3.5.30729)\r\n" .
              "Keep-Alive: 300\r\n" .
              "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n"
              //, 'proxy' => 'tcp://proxy:puerto', //Si utilizas algun proxy para salir a internet descomenta esta linea y por la direccion de tu proxy y el puerto
              //'request_fulluri' => True //Tambien esta si utilizas algun proxy

        ),
    );
$cxContext = stream_context_create($aContext);

// $url = "http://consultas.curp.gob.mx/CurpSP/curp11.do?strPrimerApellido=$primerApellido&strSegundoAplido=$segundoApellido&strNombre=$nombre&strdia=$diaNacimiento&strmes=$mesNaciemiento&stranio=$anioNacimiento&sSexoA=$sexo&sEntidadA=$entidadNacimiento&rdbBD=myoracle&strTipo=A&entfija=DF&depfija=11024&codigo=$miCodigo";
 $url1 = "http://consultas.curp.gob.mx/CurpSP";

 $url = "http://consultas.curp.gob.mx/CurpSP/curp11.do?strPrimerApellido=$primerApellido&strSegundoAplido=$segundoApellido&strNombre=$nombre&strdia=$diaNacimiento&strmes=$mesNaciemiento&stranio=$anio$anioNacimiento&sEntidadA=$entidadNacimiento&sSexoA=$sexo&strTipo=A&codigo=$miCodigo";

//echo $url;
/*$url = "http://consultas.curp.gob.mx/CurpSP/curp21.do?strCurp=TETJ700106HCSRRV02&strTipo=B&codigo=e5625";
*/
//http://consultas.curp.gob.mx/CurpSP/curp21.do?strCurp=TETJ700106HCSRRV02&strTipo=B&codigo=e5625
    $file = file_get_contents($url1, false, $cxContext);  //$cxContext
 //   preg_match_all("/var strCurp=\"(.*)\"/", $file, $curp);  //desactivado hasta probarlo bien

    var_dump($file);


//    $file = file_get_contents($url, false, $cxContext);  //$cxContext
 //   preg_match_all("/var strCurp=\"(.*)\"/", $file, $curp);  //desactivado hasta probarlo bien

 //   var_dump($file);

//$curp = $curp[1][0];

 //   if($curp){
 //       return $curp;
 //   }else{
 //       $curp = "Curp no encontrado.";
 //       return $curp;
 //   }
}

function interval_date($init,$finish,$tipo)
{
    //formateamos las fechas a segundos tipo 1374998435
    $diferencia = strtotime($finish) - strtotime($init);

    //comprobamos el tiempo que ha pasado en segundos entre las dos fechas
    //floor devuelve el número entero anterior, si es 5.7 devuelve 5    // depurar
    if($diferencia < 60){
        $tiempo = "Hace " . floor($diferencia) . " segundos";
    }else if($diferencia > 60 && $diferencia < 3600){
        $tiempo = "Hace " . floor($diferencia/60) . " minutos'";
    }else if($diferencia > 3600 && $diferencia < 86400){
        $tiempo = "Hace " . floor($diferencia/3600) . " horas";
    }else if($diferencia > 86400 && $diferencia < 2592000){
        $tiempo = "Hace " . floor($diferencia/86400) . " días";
    }else if($diferencia > 2592000 && $diferencia < 31104000){
        $tiempo = "Hace " . floor($diferencia/2592000) . " meses";
    }else if($diferencia > 31104000){
        $tiempo = "Hace " . floor($diferencia/31104000) . " años";
    }else{
        $tiempo = "Error";
    }
    return $tiempo;
}

function ValidaFecha_dmY($fecha)
{

  $date=fechaamysql($fecha);
if ((preg_match("/\d{4}\-\d{2}-\d{2}/", $date)))
   { return true; }
else
   { return false; }

/*
if (preg_match("/(0[1-9]|[12][0-9]|3[01])[/](0[1-9]|1[012])[/](19|20)[0-9]{2}/", $fecha))
{
return true;
}
else
{
return false;
}
*/

}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    //return  $d->format($format) == $date;
   return  true;
}

function validaRFC($valor, $Tipo='Fisica')
{
        $valor = str_replace("-", "", $valor);
        if ($Tipo=='Moral' )
        {
            $letras = substr($valor, 0, 3);
            $numeros = substr($valor, 3, 6);
            $homoclave = substr($valor, 9, 3);
            if (ctype_alpha($letras) && ctype_digit($numeros) && ctype_alnum($homoclave) && strlen($valor)==12)
            {
                return true;
            }
            else
            {
            	return false;
            }
        }
        else
        {
            $letras = substr($valor, 0, 4);
            $numeros = substr($valor, 4, 6);
            $homoclave = substr($valor, 10, 3);
            if (ctype_alpha($letras) && ctype_digit($numeros) && ctype_alnum($homoclave) && strlen($valor)==13)
            {
                return true;
            }
            else
            {
            	return false;
            }
        }
}


function limpiarCaracteres($valor, $miSinEspacio=1)
{

	$valor = mb_ereg_replace("[áàâãª]","A",$valor);
	$valor = mb_ereg_replace("[ÁÀÂÃ]","A",$valor);
	$valor = mb_ereg_replace("[éèê]","E",$valor);
	$valor = mb_ereg_replace("[ÉÈÊ]","E",$valor);
	$valor = mb_ereg_replace("[íìî]","I",$valor);
	$valor = mb_ereg_replace("[ÍÌÎ]","I",$valor);
	$valor = mb_ereg_replace("[óòôõº]","O",$valor);
	$valor = mb_ereg_replace("[ÓÒÔÕ]","O",$valor);
	$valor = mb_ereg_replace("[úùû]","U",$valor);
	$valor = mb_ereg_replace("[ÚÙÛ]","U",$valor);
	if ($miSinEspacio==1)
   	$valor = mb_ereg_replace(" ","-",$valor);

	$valor = mb_ereg_replace("ñ","N",$valor);
	$valor = mb_ereg_replace("Ñ","N",$valor);

   return $valor;

}


function CombierteMayusculas($valor)
{
  $valor=strtoupper($valor);

  $valor = preg_match("/á/","Á",$valor);
  $valor = preg_match("/é/","É",$valor);
  $valor = preg_match("/í/","Í",$valor);
  $valor = preg_match("/ó/","Ó",$valor);
  $valor = preg_match("/ú/","Ú",$valor);
  $valor = preg_match("/ñ/","Ñ",$valor);

   return $valor;

}

function CombierteMinusculas($valor)
{
  $valor=strtolower($valor);

  $valor = preg_match("/Á/","á",$valor);
  $valor = preg_match("/É/","é",$valor);
  $valor = preg_match("/Í/","í",$valor);
  $valor = preg_match("/Ó/","ó",$valor);
  $valor = preg_match("/Ú/","ú",$valor);
  $valor = preg_match("/Ñ/","ñ",$valor);

   return $valor;

}

function ConvertirMayusculas($valor)
{
  $valor=strtoupper($valor);

  $valor = preg_match("/á/","Á",$valor);
  $valor = preg_match("/é/","É",$valor);
  $valor = preg_match("/í/","Í",$valor);
  $valor = preg_match("/ó/","Ó",$valor);
  $valor = preg_match("/ú/","Ú",$valor);
  $valor = preg_match("/ñ/","Ñ",$valor);

   return $valor;

}

function ConvertirMinusculas($valor)
{
  $valor=strtolower($valor);

  $valor = preg_match("/Á/","á",$valor);
  $valor = preg_match("/É/","é",$valor);
  $valor = preg_match("/Í/","í",$valor);
  $valor = preg_match("/Ó/","ó",$valor);
  $valor = preg_match("/Ú/","ú",$valor);
  $valor = preg_match("/Ñ/","ñ",$valor);

   return $valor;

}

function ValidarClave($clave,&$error_clave){

    $clave=base64_decode($clave);

   if(strlen($clave) < 6){
      $error_clave = "La clave debe tener al menos 6 caracteres";
      return false;
   }
   if(strlen($clave) > 16){
      $error_clave = "La clave no puede tener más de 16 caracteres";
      return false;
   }
   if (!preg_match('`[a-z]`',$clave)){
      $error_clave = "La clave debe tener al menos una letra minúscula";
      return false;
   }
   if (!preg_match('`[A-Z]`',$clave)){
      $error_clave = "La clave debe tener al menos una letra mayúscula";
      return false;
   }
   if (!preg_match('`[0-9]`',$clave)){
      $error_clave = "La clave debe tener al menos un caracter numérico";
      return false;
   }
   $error_clave = "";
   return true;
}

function RegistroBitacoraManual($Accion)
{
        $ArchivoPHP=explode('/', $_SERVER['PHP_SELF']);
        $link=Conectarse();
        $miConsultaBitacora="SELECT my.menu_title FROM mymenugenerador AS my WHERE my.external_link ='".$ArchivoPHP[2]."' LIMIT 0,1";
        $resultBitacora=mysqli_query($link,$miConsultaBitacora);
        $rowBitacora=mysqli_fetch_array($resultBitacora);
        if(strlen($rowBitacora['menu_title'])>=1)
		$Modulo=$rowBitacora['menu_title'];
        else
		$Modulo="V.E.: ".$ArchivoPHP[2];
        $laCadenaSQL="CALL ValidaSesion('".$_SESSION["miSessionCaja"]."', '".$_SERVER['PHP_SELF']."','".$Modulo."', '".($Accion)."') ";
        $result2=mysqli_query($link,$laCadenaSQL);
}


  function ExportarExcel($excelresultado,$miArchivo, $misgrupos=array(), $misformatos=array(), $misEncabezados=array(), $interruptoralerta=1)
  {

     $excel=new ExcelEscribir($miArchivo);

      if($excel==false)
        echo $excel->error;

      $miactiva=1;
      $MiCuantasColumnas=0;
      $Rs2 = mysqli_fetch_assoc($excelresultado);
      foreach($Rs2 as $key => $value )
      {
        if ($misgrupos[1]!=$key && $misgrupos[2]!=$key)
        {
          $myArr[]=$key;
          $MiCuantasColumnas=$MiCuantasColumnas+1;
         }
      }
      if (count($misEncabezados)>0)
         {
            foreach($misEncabezados as $col)
            {
              $excel->writeCol("<tr><td class=xl26 width=64 colspan=$MiCuantasColumnas ><b><font size=2>$col</font></b></td></tr>",1);
            }
         }

      $excel->writeLine($myArr,0,1,2);

       if (count($misgrupos)>0)
         {
            $excel->writeRow(0);
            foreach($Rs2 as $miI=>$col)
            {
              $SiTieneGrupo=0;
              if ($misgrupos[1]==$miI)
               {
                   if ($misgrupos[3]!=$col)
                   {
                     $misgrupos[3]=$col;
                     if (is_string($col))
                        $col=utf8_encode($col);

                     if ($misgrupos[5]>0)
                        $miTextoLineas=" colspan=$MiCuantasColumnas";
                     else
                       $miTextoLineas=" ";

                     $excel->writeCol("<tr><td class=xl26 width=64 $miTextoLineas ><b><font size=3>$col</font></b></td></tr>",1);
                   }
                   $SiTieneGrupo=1;
                }
               if ($misgrupos[2]==$miI)
               {
                   if ($misgrupos[4]!=$col)
                   {
                     $misgrupos[4]=$col;
                     if (is_string($col))
                        $col=utf8_encode($col);

                     if ($misgrupos[6]>0)
                        $miTextoLineas=" colspan='".($MiCuantasColumnas-1)."'";
                     else
                       $miTextoLineas=" ";

                     $excel->writeCol("<tr><td class=xl26 width=64 ></td><td class=xl26 width=64 $miTextoLineas  ><b><font size=2>$col</font></b></td></tr>",1);
                   }
                   $SiTieneGrupo=1;
                }

              if ($SiTieneGrupo==0)
              {
                $miTextoLargo=64;
                $miFormato="xl24 ";
                foreach($misformatos as $i=>$col2)
                {
                  if ($col2[1]==$miI)
                  {
                    $miTextoLargo=$col2[2];
                    if ($col2[3]=='Numerico')
                    {
                      $miFormato=" xl28 ";
                    }
                  }
                 }

                if (is_string($col))
                 $col=utf8_encode($col);

                $excel->writeCol("<td class=$miFormato width=$miTextoLargo  >$col</td>",1);
               }
             }
             $excel->writeRow(1);
         }
         else if(count($misformatos)>0)
         {
          $excel->writeRow(0);
            foreach($Rs2 as $miI=>$col)
            {
               $miTextoLargo=64;
                $miFormato="xl24 ";
                foreach($misformatos as $i=>$col2)
                {
                  if ($col2[1]==$miI)
                  {
                    $miTextoLargo=$col2[2];
                    if ($col2[3]=='Numerico')
                    {
                      $miFormato=" xl28 ";
                    }
                  }
                 }

                if (is_string($col))
                 $col=utf8_encode($col);

                $excel->writeCol("<td class=$miFormato width=$miTextoLargo  >$col</td>",1);
             }
             $excel->writeRow(1);
          }
          else
           $excel->writeLine($Rs2,1,0,1);

       while($Rs2 = mysqli_fetch_assoc($excelresultado))
      {
         if (count($misgrupos)>0)
         {
            $excel->writeRow(0);
            foreach($Rs2 as $miI=>$col)
            {
              $SiTieneGrupo=0;
              if ($misgrupos[1]==$miI)
               {
                   if ($misgrupos[3]!=$col)
                   {
                     $misgrupos[3]=$col;
                     if (is_string($col))
                        $col=utf8_encode($col);

                     if ($misgrupos[5]>0)
                        $miTextoLineas=" colspan=$MiCuantasColumnas";
                     else
                       $miTextoLineas=" ";

                     $excel->writeCol("<tr><td class=xl26 width=64 $miTextoLineas><b><font size=3>$col</td></tr>",1);
                   }
                   $SiTieneGrupo=1;
                }
               if ($misgrupos[2]==$miI)
               {
                   if ($misgrupos[4]!=$col)
                   {
                     $misgrupos[4]=$col;
                     if (is_string($col))
                        $col=utf8_encode($col);

                     if ($misgrupos[6]>0)
                        $miTextoLineas=" colspan='".($MiCuantasColumnas-1)."'";
                     else
                       $miTextoLineas=" ";

                     $excel->writeCol("<tr><td class=xl26 width=64 ></td><td class=xl26 width=64 $miTextoLineas ><b><font size=2>$col</font></b></td></tr>",1);
                   }
                   $SiTieneGrupo=1;
                }

              if ($SiTieneGrupo==0)
              {
                $miTextoLargo=64;
                $miFormato="";
                foreach($misformatos as $i=>$col2)
                {
                  if ($col2[1]==$miI)
                  {
                    $miTextoLargo=$col2[2];
                    $miFormato=" xl24 ";
                    if ($col2[3]=='Numerico')
                    {
                      $miFormato=" Xl28 ";
                    }
                    if ($col2[3]=='Decimal')
                      $miFormato=" xl29 ";
                  }
                 }


                 if (is_string($col))
                   $col=utf8_encode($col);

                $excel->writeCol("<td class=$miFormato width=$miTextoLargo  >".trim($col)."</td>",1);
               }
             }
             $excel->writeRow(1);
         }
         else if(count($misformatos)>0)
         {
          $excel->writeRow(0);
            foreach($Rs2 as $miI=>$col)
            {
               $miTextoLargo=64;
                $miFormato="xl24 ";
                foreach($misformatos as $i=>$col2)
                {
                  if ($col2[1]==$miI)
                  {
                    $miTextoLargo=$col2[2];
                    if ($col2[3]=='Numerico')
                      $miFormato=" xl28 ";
                    if ($col2[3]=='Decimal')
                      $miFormato=" xl29 ";
                    if ($col2[3]=='Fecha')
                      $miFormato=" xl30 ";
                  }
                 }

                //if (is_string($col))
                 //$col=utf8_decode($col);

                $excel->writeCol("<td class=$miFormato width=$miTextoLargo  >$col</td>",1);
             }
             $excel->writeRow(1);
         }
         else
           $excel->writeLine($Rs2,1,0,1);
      }

      $excel->close();
			if($interruptoralerta==1)
      echo " <script> swal('Exito...!', 'El Archivo de Excel a sido exportado con exito...!', 'success'); </script>";
    }

    Class ExcelEscribir
    {

        var $fp=null;
        var $error;
        var $state="SICAPCERRADO";
        var $newRow=false;

        function ExcelEscribir($file="")
        {
            return $this->open($file);
        }

        function open($file)
        {
            if($this->state!="SICAPCERRADO")
            {
                $this->error="Error : El archivo no esta abierto cierrelo para guardar los valores ";
                return false;
            }

            if(!empty($file))
            {
                $this->fp=@fopen($file,"w+");
            }
            else
            {
                $this->error="Usage : No puedo crear el objeto ExcelEscribir('NombreArchivo')";
                return false;
            }
            if($this->fp==false)
            {
                $this->error="Error: No tiene permiso para abrir el archivo.";
                return false;
            }
            $this->state="SICAPABIERTO";
            fwrite($this->fp,$this->GetHeader());
            return $this->fp;
        }

        function close()
        {
            if($this->state!="SICAPABIERTO")
            {
                $this->error="Error : Por favor abra el archivo.";
                return false;
            }
            if($this->newRow)
            {
                fwrite($this->fp,"</tr>");
                $this->newRow=false;
            }

            fwrite($this->fp,$this->GetFooter());
            fclose($this->fp);
            $this->state="SICAPCERRADO";
            return ;
        }


        function GetHeader()
        {
            $header = <<<EOH
                <html xmlns:o="urn:schemas-microsoft-com:office:office"
                xmlns:x="urn:schemas-microsoft-com:office:excel"
                xmlns="http://www.w3.org/TR/REC-html40">

                <head>
                <meta http-equiv=Content-Type content="text/html; charset=utf-8">
                <meta name=ProgId content=Excel.Sheet>
                <!--[if gte mso 9]><xml>
                 <o:DocumentProperties>
                  <o:LastAuthor>Sriram</o:LastAuthor>
                  <o:LastSaved>2005-01-02T07:46:23Z</o:LastSaved>
                  <o:Version>10.2625</o:Version>
                 </o:DocumentProperties>
                 <o:OfficeDocumentSettings>
                  <o:DownloadComponents/>
                 </o:OfficeDocumentSettings>
                </xml><![endif]-->
                <style>
                <!--table
                    {mso-displayed-decimal-separator:"\.";
                    mso-displayed-thousand-separator:"\,";}
                @page
                    {margin:1.0in .75in 1.0in .75in;
                    mso-header-margin:.5in;
                    mso-footer-margin:.5in;}
                tr
                    {mso-height-source:auto;}
                col
                    {mso-width-source:auto;}
                br
                    {mso-data-placement:same-cell;}
                .style0
                    {mso-number-format:General;
                    text-align:general;
                    vertical-align:bottom;
                    white-space:nowrap;
                    mso-rotate:0;
                    mso-background-source:auto;
                    mso-pattern:auto;
                    color:windowtext;
                    font-size:10.0pt;
                    font-weight:400;
                    font-style:normal;
                    text-decoration:none;
                    font-family:Arial;
                    mso-generic-font-family:auto;
                    mso-font-charset:0;
                    border:none;
                    mso-protection:locked visible;
                    mso-style-name:Normal;
                    mso-style-id:0;}
                td
                    {mso-style-parent:style0;
                    padding-top:1px;
                    padding-right:1px;
                    padding-left:1px;
                    mso-ignore:padding;
                    color:windowtext;
                    font-size:10.0pt;
                    font-weight:400;
                    font-style:normal;
                    text-decoration:none;
                    font-family:Arial;
                    mso-generic-font-family:auto;
                    mso-font-charset:0;
                    mso-number-format:General;
                    text-align:general;
                    vertical-align:bottom;
                    border:none;
                    mso-background-source:auto;
                    mso-pattern:auto;
                    mso-protection:locked visible;
                    white-space:nowrap;
                    mso-rotate:0;}
                .xl24
                    {mso-style-parent:style0;
                    white-space:normal;}
                .xl26
                    {mso-style-parent:style0;
                    white-space:normal;}
                .xl27
                    {mso-style-parent:style0;
                    text-align:center;
                    mso-background-source: red;
                    background: red;
                    border:.5pt solid;
                    font-style: bold;
                    font-size:12.0pt;}
                .xl28
                    {mso-style-parent:style0;
                      mso-number-format:"0";
                      text-align:right;
                  }
                .xl29
                    {mso-style-parent:style0;
                    mso-number-format:"\#\,\#\#0\.00";
                    text-align:right;
                   }
                .xl30
                    {mso-style-parent:style0;
                    mso-number-format:"dd/mm/yyyy";
                   }
                -->
                </style>
                <!--[if gte mso 9]><xml>
                 <x:ExcelWorkbook>
                  <x:ExcelWorksheets>
                   <x:ExcelWorksheet>
                    <x:Name>srirmam</x:Name>
                    <x:WorksheetOptions>
                     <x:Selected/>
                     <x:ProtectContents>False</x:ProtectContents>
                     <x:ProtectObjects>False</x:ProtectObjects>
                     <x:ProtectScenarios>False</x:ProtectScenarios>
                    </x:WorksheetOptions>
                   </x:ExcelWorksheet>
                  </x:ExcelWorksheets>
                  <x:WindowHeight>10005</x:WindowHeight>
                  <x:WindowWidth>10005</x:WindowWidth>
                  <x:WindowTopX>120</x:WindowTopX>
                  <x:WindowTopY>135</x:WindowTopY>
                  <x:ProtectStructure>False</x:ProtectStructure>
                  <x:ProtectWindows>False</x:ProtectWindows>
                 </x:ExcelWorkbook>
                </xml><![endif]-->
                </head>

                <body link=blue vlink=purple>
                <table x:str border=0 cellpadding=0 cellspacing=0 style='border-collapse: collapse;table-layout:fixed;'>
EOH;
            return $header;
        }

        function GetFooter()
        {
            return "</table></body></html>";
        }

        function writeLine($line_arr,$AnalizaCampo=0,$ConLinea=0, $ConAlineado=1)
        {
            if($this->state!="SICAPABIERTO")
            {
                $this->error="Error : Por favor abra el archivo.";
                return false;
            }
            if(!is_array($line_arr))
            {
                $this->error="Error : Argumento no valido al crear el arreglo.";
                return false;
            }
            fwrite($this->fp,"<tr>");

       //     if ($ConLinea==1)
       //       $miBorde=" border: 8px solid; COLOR: black; background-color: #D8D8D8; ";
       //     else
       //       $miBorde=" ";

            if ($ConAlineado==2 || $ConLinea==1)
              $miAlineado=" xl27 ";
            else
              $miAlineado=" xl24 ";

        //    $miTextoFormato=" style=' $miBorde ' $miAlineado ";

            foreach($line_arr as $col)
            {
              if ($AnalizaCampo==1)
              {
                if (is_string($col))
                  $col=($col);
                 fwrite($this->fp,"<td class=$miAlineado width=64 >$col</td>");
               }
               else
               {
                 fwrite($this->fp,"<td class=$miAlineado width=64 >$col</td>");
               }

            }
            fwrite($this->fp,"</tr>");
        }

        function writeRow($Linea=0)
        {
            if($Linea==0)
                fwrite($this->fp,"<tr>");
            else
                fwrite($this->fp,"</tr>");

        }

        function writeCol($value,$Armar=0)
        {
           if ($Armar==1)
            fwrite($this->fp,"$value");
           else
           fwrite($this->fp,"<td class=xl24 width=64 >$value</td>");
        }
    }

?>
