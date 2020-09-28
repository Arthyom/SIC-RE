<?php
   session_start();
    include("clases/procedimientos.php"); // incluir procedimientos
    if (!isset($_SESSION["miSessionCaja"]))
    {
      echo"<script>parent.location.href=\"ValidaUsuario.php\";</script>";
      exit();
    }
    if (!isset($_SESSION["UltimaPaginaActiva"]) || $_SESSION["UltimaPaginaActiva"]!=$_SERVER['PHP_SELF'])
    {
       $_SESSION["UltimaPaginaActiva"]=$_SERVER['PHP_SELF'];
       $_SESSION["PaginaActiaActual"]=1;
      $_SESSION["mipersonabuscar2"]="";
       $_SESSION["mipersonabuscar"]="";
       $_SESSION["MontoRetirar"]="";
     $_SESSION["MontoRecibido"]=0;
    }


    validacesion();
    ardisoencabezado();



  if(true)// if(!isset($_SESSION['Bienvenida']))
      $_SESSION['Bienvenida']=1;


   if( true ) #$_SESSION['Bienvenida']==1)
   {

   $link=Conectarse();

     $cadena="SELECT  Valor FROM porcentajeinflacion WHERE DATE(Fecha)=DATE(NOW())";
        $result=mysqli_query($link,$cadena);
        $row=mysqli_fetch_array($result) ;

    if ($_SESSION["miSessionCaja"]==21 || $_SESSION["miSessionCaja"]==146 && $row['Valor']==0){
        $_SESSION['miMensajito']="Recuerda Actualizar el Porcentaje de Inflacíón";
    }else
        $_SESSION['miMensajito']="";

    echo ("<script>swal({
      title: \"Bienvenido(a)<br><h4>".$_SESSION["miSessionNombreUsuario"]." \",
      text: \"Sucursal: <h3><font color='#142c6e'>".$_SESSION["mibgSucursal"]."</font><br><br><font color='#FF0000'>".$_SESSION['miMensajito']."</font></h3>\",
      timer: 4000, showConfirmButton: false,
      html: true});</script>");
    $_SESSION['Bienvenida']=0;
   }


    $link=Conectarse();

    $cadena="SELECT ana.IdPrestamo, DameNombre(ana.IdPersona,7) AS Persona FROM prestamoanalisis AS ana INNER JOIN prestamos AS pr ON ana.IdPrestamo=pr.IdPrestamo WHERE (ana.IdAutorizar1=".$_SESSION["miSessionCaja"]." OR ana.IdAutorizar2=".$_SESSION["miSessionCaja"].") AND pr.Etapa='Autorizacion' AND ana.IdPrestamo NOT IN (SELECT IdPrestamo FROM prestamoautorizacion WHERE IdPrestamo=ana.IdPrestamo AND IdUsuario=".$_SESSION["miSessionCaja"].")";
    $result=mysqli_query($link,$cadena);

    $miCadena="";
    while($row=mysqli_fetch_array($result)) {
       $miCadena=$miCadena.$row['IdPrestamo']." del socio ".$row['Persona'].",  ";
    }

    if (strlen($miCadena)>0)
      echo "<script>alert('Tiene pendiente por autorizar el(los) credito(s) $miCadena')</script>";

    ardisopie();
?>
