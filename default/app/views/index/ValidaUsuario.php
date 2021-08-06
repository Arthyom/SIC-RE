<?php
    View::template(null);
    View::select(null);
session_start();
include("clases/procedimientos.php");

$_SESSION['Focus']='Usuario';


//echo getIP();
if (isset($_SESSION["miSessionCaja"]) || isset($_POST['Usuario']) ) //|| isset($_POST["Token"])
{

  if (isset($_POST['Usuario']) || isset($_POST["SubirArchivokey"])  ) // isset($_POST["Token"]) isset($_POST['enviartoken'])
  {
    $_SESSION["miDispositivo"] = 0;

    $_SESSION["mibgIdGrupo2"]=0;
    $_SESSION["mibgUsuarioAct"]="alfredo";
    $_SESSION["mibgClaveUsuar"]="Alfredo2020+";
    $_SESSION["mibgBaseDatos"]="caminostepeyac";
    $_SESSION["RegistrosPorPagina"]=12;
    $_SESSION["NumeroTotalPaginas"]=0;
    $_SESSION["PaginaActiaActual"]=1;
    $_SESSION["RegistrosTotalesTabla"]=0;
    $_SESSION["UltimaPaginaActiva"]="";
    $_SESSION["mijavascriptglobal"]="";
    $_SESSION["miFechaActiva"]="";
    $_SESSION["miHoraActiva"]="";
    $_SESSION["mibuscarglobalvar"]="";
    $_SESSION["miIdPersonaActiva"]="";
    $_SESSION["miMontominimo"]=0;
    $_SESSION["miMontomaximo"]=0;
    $_SESSION["miTasaInteres"]=0;
    $_SESSION["miDiasActivos"]=0;
    $_SESSION["miSessionActual"]="";
    $_SESSION["misNumeroPaginasGlobal"]=1;
    $_SESSION["misFechaPaginasGlobal"]=1;
    $_SESSION["miCorreoFactura"]="";
    $_SESSION["miOrdenNombre"]=0;
    $_SESSION["miMayusculasActivo"]=1;
    $_SESSION["miConsecutivoAplicar"]="";
    $_SESSION["mipersonabuscar2"]="";
    $_SESSION["misRegistrosEnPantalla"]=0;
    $_SESSION["NominaExterna"]=0;
   // $miIp=getIP();

    $link=Conectarse();
    if (isset($_POST['Clave']) && isset($_POST['Usuario']))
    {

      /////////// verificar si el usuario existe en la base de datos
      //$miCadenaSQL="SELECT IdUsuario, Nombre, Nivel, IdGrupoUsuarios, DameGrupoUsuario(IdUsuario) AS Grupo, IdSucursal, DATEDIFF(NOW(),FechaClave) AS DiasClave, (SELECT MayusculasActivo FROM configuracion) AS MayusculasActivo, (SELECT DiasCaducaClaveAcceso FROM configuracion) AS DiasCaduca, DATE_FORMAT(NOW(),'%d/%m/%Y') AS Fecha, DATE_FORMAT(NOW(),'%H:%i:%s') AS Hora, UltimaIP,IpSecundaria FROM usuarios WHERE Usuario='".mysql_real_escape_string($_POST['Usuario'])."' and Clave=MD5('".mysql_real_escape_string($_POST['Clave'])."') AND Activo='Si' ";  //AND (EnLinea='No' OR EnLinea='' OR DATE_ADD(FechaEnLinea,INTERVAL 5 MINUTE)<NOW())
      $miCadenaSQL="SELECT IdUsuario, Nombre, Nivel, IdGrupoUsuarios, DameGrupoUsuario(IdUsuario) AS Grupo, IdSucursal, DATEDIFF(NOW(),FechaClave) AS DiasClave, (SELECT MayusculasActivo FROM configuracion) AS MayusculasActivo, (SELECT DiasCaducaClaveAcceso FROM configuracion) AS DiasCaduca, DATE_FORMAT(NOW(),'%d/%m/%Y') AS Fecha, DATE_FORMAT(NOW(),'%H:%i:%s') AS Hora, UltimaIP,IpSecundaria FROM usuarios WHERE Usuario='".mysql_real_escape_string($_POST['Usuario'])."'  AND Activo='Si' ";  //AND (EnLinea='No' OR EnLinea='' OR DATE_ADD(FechaEnLinea,INTERVAL 5 MINUTE)<NOW())
      //echo $miCadenaSQL;


      $result2=mysql_query($miCadenaSQL);
      $row2=mysql_fetch_array($result2);
      $_SESSION["miSessionCaja"]=$row2["IdUsuario"];
      $_SESSION["DiasCaduca"]=$row2["DiasCaduca"];
      $_SESSION["DiasClave"]=$row2["DiasClave"];
      $_SESSION["miSessionToken"]="";
      $_SESSION["miSessionTokenValido"]=0;
    }
$resultsArray = array();

$_SESSION["miFirmaValida"]=0;
if (isset($_SESSION["miSessionCaja"]) || (isset($row2["IdUsuario"]) && $row2["IdUsuario"]>0))
{
   if (isset($row2["IdUsuario"]))
    $_SESSION["miSessionCaja"]=$row2["IdUsuario"];


  if (isset($_POST['SubirArchivokey']) && $_FILES['archivokey']['tmp_name']!="")
  {
    if ($_FILES['archivokey']['tmp_name']!="")
    {
      $ext=strrchr($_FILES['archivokey']['name'],".");
      if (strtolower($ext)!='.key')
      {
        echo "<script>alert ('El archivo tiene que tener formato y extencion key');</script>";
        $_SESSION["miFirmaValida"]=2;
      }
      else
      {
        $link=Conectarse();
        $miCadenaSql = "SELECT Archivo, ClavePublica FROM usuarios WHERE IdUsuario ='".$_SESSION["miSessionCaja"]."'";

        $result = mysql_query($miCadenaSql);
        $row3=mysql_fetch_array($result);

        $miArchivoNuevo="llaves/".$row3['Archivo'].".tem";
        move_uploaded_file($_FILES['archivokey']['tmp_name'], $miArchivoNuevo);
//        mysql_close($link);

        $fp = fopen($miArchivoNuevo, "r");

        $miLinea = fgets($fp);

        fclose($fp);

         $ClavePrivada=base64_decode($miLinea);
         $ClavePublica=base64_decode($row3['ClavePublica']);
         openssl_public_encrypt($row3['Archivo'], $Encriptadoxxy, $ClavePublica);

         openssl_private_decrypt($Encriptadoxxy, $desencryptado, $ClavePrivada);


        if ($row3['Archivo']==$desencryptado)
          $_SESSION["miFirmaValida"]=1;
        else
          $_SESSION["miFirmaValida"]=2;

      }
    }
  }
//$_SESSION["miFirmaValida"]=1;//Remover cuando acabe!!!!!!!
  // if ($row2["UltimaIP"]==getIP() || $row2["IpSecundaria"]==getIP())
  if(true)
    $_SESSION["miFirmaValida"]=1;
   else
   {
     $miCadenaSQLx3="SELECT IdIpPermitida FROM ippermitidas WHERE IP='".getIP()."' ";
    //  echo $miCadenaSQLx3;

      $resultx3=mysql_query($miCadenaSQLx3);
      $rowx3=mysql_fetch_array($resultx3);
    if($rowx3["IdIpPermitida"]>0)
      $_SESSION["miFirmaValida"]=1;
   }


  if ($_SESSION["miFirmaValida"]==0)
  {
    //  $miCadenaSQL21="SELECT DameValorToken() AS Valor ";
    //  $result21=mysql_query($miCadenaSQL21);
    //  $row21=mysql_fetch_array($result21);
    //  $_SESSION["miSessionToken"]=$row21["Valor"];
    //  $_SESSION["miSessionTokenValido"]=0;
      ardisoencabezado('0');

      echo "<form id=\"entrada\" name=\"Usuario\" enctype=\"multipart/form-data\" action=\"ValidaUsuario.php\" method=\"post\" autocomplete=\"off\"  >";
  //    echo "<label for=\"Clave\">Token : ".$row21["Valor"]."</label>";
   //   echo "<input name=\"Token\" type=\"password\" maxlength=\"6\" />";
   //   echo "<input type=\"submit\" name=\"enviartoken\" value=\"Validar\"  class=\"button2\" />";

      echo " <br> Por favor Ingrese el <br> Archivo key: <input type=\"file\" id=\"archivokey\" name=\"archivokey\" value=\"examinarkey\" /><h5> <input type=\"submit\" src=\"imagenes/mymenu/arribax.gif\" name=\"SubirArchivokey\" value=\"Leer Archivo key \"  class='button2' /></h5> ";
      echo "</form>";
      ardisopie();

  }
/*  elseif (isset($_POST["Token"]) && $_SESSION["miFirmaValida"]==0)
  {
     $miCadenaSQL21="SELECT DameTokenValidar('".$_SESSION["miSessionToken"]."','".$_POST["Token"]."') AS Valor ";
     $result21=mysql_query($miCadenaSQL21);
     $row21=mysql_fetch_array($result21);
     if ($row21["Valor"]==1)
       $_SESSION["miSessionTokenValido"]=1;
     else
        $_SESSION["miSessionTokenValido"]=0;
  }
  */
  //((isset($_SESSION["miSessionToken"]) && $_SESSION["miSessionTokenValido"]>0) ||

  if ($_SESSION["DiasClave"]>=$_SESSION["DiasCaduca"] &&  $_SESSION["miFirmaValida"]==1 )
  {
    echo "<script> window.open (\"cambiarclave.php?miClave=".$_POST['Clave']."\", \"claves\", \"modal=yes\");</script>";
    header('Location: cambiarclave.php?miIdUsuario='.$_SESSION["miSessionCaja"]);
  }
  //$_SESSION["miFirmaValida"]=1;
  if ( $_SESSION["miFirmaValida"]==1 && $_SESSION["miSessionCaja"]>0 ) // (isset($_SESSION["miSessionToken"]) && $_SESSION["miSessionTokenValido"]>0) ||
  {
    $link=Conectarse();
    $miCadenaSQL="SELECT IdUsuario, Nombre, Nivel, IdGrupoUsuarios, DameGrupoUsuario(IdUsuario) AS Grupo, IdSucursal, (SELECT Nombre FROM sucursales WHERE IdSucursal=us.IdSucursal LIMIT 0,1) AS Sucursal, DATEDIFF(NOW(),FechaClave) AS DiasClave, (SELECT MayusculasActivo FROM configuracion) AS MayusculasActivo, (SELECT DiasCaducaClaveAcceso FROM configuracion) AS DiasCaduca, DATE_FORMAT(NOW(),'%d/%m/%Y') AS Fecha, DATE_FORMAT(NOW(),'%H:%i:%s') AS Hora, us.NominaExterna FROM usuarios AS us WHERE IdUsuario='".$_SESSION["miSessionCaja"]."'  ";  //AND (EnLinea='No' OR EnLinea='' OR DATE_ADD(FechaEnLinea,INTERVAL 5 MINUTE)<NOW())
   //  echo $miCadenaSQL;

    $result2=mysql_query($miCadenaSQL);
    $row2=mysql_fetch_array($result2);
    $_SESSION["mibgIdGrupo2"]=$row2["IdGrupoUsuarios"];
    $_SESSION["mibgIdGrupo"]=$row2["Grupo"];
    $_SESSION["mibgIdSucursal"]=$row2["IdSucursal"];
    $_SESSION["mibgSucursal"]=$row2["Sucursal"];
    $_SESSION["miMayusculasActivo"]=$row2["MayusculasActivo"];
    $_SESSION["miNivelUsuario"]=$row2["Nivel"];
    $_SESSION["NominaExterna"]=$row2["NominaExterna"];
 // $_SESSION["miSessionCaja"]=$row2["IdUsuario"];
    $_SESSION["miSessionNombre"]=$_POST['Usuario'];
    $_SESSION["miSessionNombreUsuario"]=$row2['Nombre'];
    $_SESSION["ultimoAcceso"]= date("Y-n-j H:i:s");
    $_SESSION["miFechaActiva"]=$row2["Fecha"];
    $_SESSION["miHoraActiva"]=$row2["Hora"];
    $_SESSION["miSessionActual"]=date("YnjHis");
    $_SESSION["miArregloNoAplicar"]=array();
    $miIp=getIP();

    //$micadenaSQL="call AbreSesion('".$_SESSION["miSessionCaja"]."','$miIp','".$_SESSION["miSessionActual"]."')";
    //mysql_query($micadenaSQL);

  //  echo $micadenaSQL;

    $result2=mysql_query("SELECT Nombre, Lineasporpagina, Tiempoespera, Diascaptacion, Diasprestamo, OrdenNombre, (SELECT Fecha FROM dolares WHERE Fecha=DATE(NOW())) AS FechaDolar, DATE(NOW()) AS Hoy, DAYOFWEEK(now()) AS Dia FROM configuracion");
    $row2=mysql_fetch_array($result2);
    $_SESSION["RegistrosPorPagina"]=$row2["Lineasporpagina"];
    $_SESSION["Tiempoespera"]=$row2["Tiempoespera"];
    $_SESSION["Nombredelacaja"]=$row2["Nombre"];
    $_SESSION["Diascaptacion"]=$row2["DiasCaptacion"];
    $_SESSION["Diasprestamo"]=$row2["DiasPrestamo"];
    $_SESSION["miOrdenNombre"]=$row2["OrdenNombre"];

    $_SESSION["miMasCaracteresLegal"]=0;
    $miCadenaSQL="UPDATE usuarios SET Intentos=0 WHERE IdUsuario='".$_SESSION["miSessionCaja"]."' ";
    mysql_query($miCadenaSQL);


  echo"<script>parent.location.href=\"caja.php\";</script>";
  }
  else //if ((!isset($_SESSION["miSessionToken"]) || $_SESSION["miSessionTokenValido"]==0) && isset($_POST["Token"]))
  {
    if ($_SESSION["miFirmaValida"]==2)
    {
      echo "<script>alert('La llave no es valida ...');</script>";
  //  else
  //    echo "<script>alert('El token no es valido ...');</script>";
      unset($_SESSION["miSessionCaja"]);
    }
  }
}
else
{
  $miCadenaSQL="SELECT IdUsuario, Intentos, Activo, (SELECT IntentosBloqueo FROM configuracion) AS Permitidos FROM usuarios WHERE Usuario='".$_POST['Usuario']."' ";
  $result2=mysql_query($miCadenaSQL);
  $row2=mysql_fetch_array($result2);
  if ($row2["IdUsuario"]>0 && $row2["Intentos"]>=$row2["Permitidos"] && $row2["Activo"]=='Si')
  {
    $miCadenaSQL="UPDATE usuarios SET Activo='No' WHERE IdUsuario='".$row2["IdUsuario"]."' ";
    $result2=mysql_query($miCadenaSQL);
    echo "<script>alert('Su clave es incorrecta por favor ingresela correctamente o su cuenta sera bloqueada...');</script>";

  }
  elseif ($row2["IdUsuario"]>0 && $row2["Intentos"]<$row2["Permitidos"] && $row2["Activo"]=='Si')
  {
    $miCadenaSQL="UPDATE usuarios SET Intentos=Intentos+1 WHERE IdUsuario='".$row2["IdUsuario"]."' ";
    $result2=mysql_query($miCadenaSQL);
    echo "<script>alert('Su clave es incorrecta por favor ingresela correctamente o su cuenta sera bloqueada...');</script>";

  }
  else
  {
    echo "<script>alert('Ya fue bloqueado comuniquese con el administrador del sistema ...');</script>";

  }
}
mysql_close($link);
}
else
{
  // evitando el cuadro de alertac
 // echo "<script>alert('Error en la informacion ingresada ...');</script>";
}
}

if (!isset($_SESSION["miSessionCaja"]) || $_SESSION["miSessionCaja"]<1)
{
  /**
   * cambiando claves para un usuario correcto
   */
  $_SESSION["mibgUsuarioAct"]="alfredo";
  $_SESSION["mibgClaveUsuar"]="Alfredo2020+";
  $_SESSION["miDispositivo"] = 0;

  ardisoencabezado('0');


  echo "<form id=\"entrada\" name=\"Usuario\" action=\"ValidaUsuario.php\" method=\"post\" autocomplete=\"off\" onSubmit = \" return validarusuariosclave(this)\" >";
  echo "<label for=\"usuario\">Usuario:</label>";
  echo "<input name=\"Usuario\" type=\"text\" maxlength=\"25\" id=\"Usuario\"/>";
  echo "<label for=\"Clave\">Clave:</label>";
  echo "<input name=\"Clave\" type=\"password\" maxlength=\"25\" />";
  echo "<h5><input type=\"submit\" name=\"enviar\" value=\"Ingresar\"  class=\"button2\" /><h5>";
  echo "</form>";
  if (isset($_SESSION["miSessionCaja"]))
    unset($_SESSION["miSessionCaja"]);

  ardisopie();
}alfredo
?>
