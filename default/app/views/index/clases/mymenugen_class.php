<?php
/*
 * MyMenuGen_class.php
 * Clase que genera el menu del sistema
 */

 
$link=Conectarse();
class myMenuObject {

// *****************************************************************
  # Objecto Constructor
  function myMenuObject(){
    // Tablas
    $this->tableName2     = 'mymenugenerador';

    // Menu Items
    $this->menu_id        = -1;
    $this->menu_title     = '';
    $this->menu_image     = '';
    $this->menu_hr        = 'no';
    $this->position       = 0;
    $this->parent_id      = -1;
    $this->external_link  = '';

    // Page Details
    $this->defaultHomepage= 100;
    $this->page_link      = '';
    $this->page_decription= '';

    // General
    $this->debug          = false;
    $this->rootDir        = '';
    $this->new_record     = true;
    $this->version        = '1.0d';           // current version number of this class
  } # function myMenuObject

// *****************************************************************
  function last_message($message){
    if($this->debug){ print "<p>$message</p>\n"; }
  } # funcion last_message($message)

// *****************************************************************
  function quote_smart($value) {
     // Stripslashes
     if (get_magic_quotes_gpc()) {
       $value = stripslashes($value);
     }
     // Quote if not integer
     if (!is_numeric($value)) {
         $value = "'" . mysqli_real_escape_string($link,$value) . "'";
     }
     return $value;
  }

// *****************************************************************
// Get all menu items on a particular level
// *****************************************************************
  function getThisLevel($pid) {
		$link=Conectarse();
    $levelArray = array();
    $FiltroAdicionalmen="";
	if ($_SESSION["miDispositivo"]>0) 
	    $FiltroAdicionalmen=" AND my.Movil>0 ";
    else
	    $FiltroAdicionalmen=" AND my.Movil=0 ";
	
    $sql = "SELECT my.id FROM mymenugenerador as my INNER JOIN grupomenu as gm ON my.id=gm.id WHERE gm.IdGrupoUsuarios='".$_SESSION["mibgIdGrupo2"]."' AND my.parent_id = $pid $FiltroAdicionalmen ORDER BY my.position";

//echo $sql;

    $this->last_message("myMenuObject: Select menu items for top level<br />$sql<br />");
    $result = mysqli_query($link,$sql) or die("Database Error:  Unable to find menu items for top level  $sql".mysqli_error($link));
    if(mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_object ($result)) {
        $levelArray[] = $row->id;
      }
    }
    return $levelArray; 
  }

//  function getThisLevel($pid) {
//    $levelArray = array();
//
//    $sql = "SELECT id FROM $this->tableName2 WHERE parent_id = $pid ORDER BY position";
//    $this->last_message("myMenuObject: Select menu items for top level<br />$sql<br />");
//    $result = mysqli_query($link,$sql) or die("Database Error:  Unable to find menu items for top level  $sql".mysqli_error($link));
//    if(mysqli_num_rows($result) > 0) {
//      while ($row = mysqli_fetch_object ($result)) {
//        $levelArray[] = $row->id;
//      }
 //   }
//    return $levelArray;
//  }

// *****************************************************************
// Get menu item details
// *****************************************************************
  function getMenuItem($id) {
		$link=Conectarse();
    $parentArray = array();

	$FiltroAdicionalmen="";
	if ($_SESSION["miDispositivo"]>0) 
	    $FiltroAdicionalmen=" AND my.Movil>0 ";
    else
	    $FiltroAdicionalmen=" AND my.Movil=0 ";
	
    if(empty($id)) {
      $this->last_message("myMenuObject: No seleccionaste ningun id");
      return false;
    } else {
      $sql = "SELECT my.menu_title,
                     my.position,
                     my.parent_id,
                     my.mensaje,
                     my.image,
                     my.hr_line,
                     my.external_link,
                     my.nuevapantalla,
                     my.Atajo
              FROM mymenugenerador as my INNER JOIN grupomenu as gm ON my.id=gm.id
              WHERE gm.IdGrupoUsuarios='".$_SESSION["mibgIdGrupo2"]."' AND my.id = '$id' $FiltroAdicionalmen LIMIT 1";
      $this->last_message("myMenuObject: Select menu item details id:$id<br />$sql<br />".mysqli_error($link)."<br />");
      $result = mysqli_query($link,$sql) or die("Database Error:  Unable to find menu item id: $id".mysqli_error($link)."");
      if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_object ($result);
        $this->menu_title = $row->menu_title;
        $this->menu_image = $row->image;
        $this->menu_hr    = $row->hr_line;
        $this->position   = $row->position;
        $this->parent_id  = $row->parent_id;
        $this->elmensaje    = $row->mensaje;
        $this->nuevapantalla = $row->nuevapantalla;
        $this->Atajo = $row->Atajo;
        $this->page_link  = empty($row->title)?$row->external_link:$row->title;
        return true;
      } else {
        $this->last_message("myMenuObject: No encontre la pagina:$id<br />$sql<br />");
        return false;
      }
    }
  }

// *****************************************************************
// Get parent of a child item
// *****************************************************************
  function getParentItem($id) {
    $parent_id = 0;
	
	$FiltroAdicionalmen="";
	if ($_SESSION["miDispositivo"]>0) 
	    $FiltroAdicionalmen=" AND my.Movil>0 ";
    else
	    $FiltroAdicionalmen=" AND my.Movil=0 ";
		
    if(empty($id)) {
      $this->last_message("myMenuObject: No page id supplied");
      return false;
    } else {
      $sql = "SELECT my.id FROM mymenugenerador as my INNER JOIN grupomenu as gm ON my.id=gm.id WHERE gm.IdGrupoUsuarios='".$_SESSION["mibgIdGrupo2"]."' AND my.id = '$id' $FiltroAdicionalmen LIMIT 1";
      $this->last_message("myMenuObject: Select parent of child: $id<br />$sql<br />");
      $result = mysqli_query($link,$sql) or die("Database Error:  Unable to find parent of child: $id");
      if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_object ($result);
        $parent_id = $row->id;
      }
      return $parent_id;
    }
  }

// *****************************************************************
// Get all child items for a particular parent
// *****************************************************************
  function getChildItems($id) {
		$link=Conectarse();
    $childArray = array();
	
	$FiltroAdicionalmen="";
	if ($_SESSION["miDispositivo"]>0) 
	    $FiltroAdicionalmen=" AND my.Movil>0 ";
    else
	    $FiltroAdicionalmen=" AND my.Movil=0 ";
		
    $sql = "SELECT my.id FROM mymenugenerador as my INNER JOIN grupomenu as gm ON my.id=gm.id WHERE gm.IdGrupoUsuarios='".$_SESSION["mibgIdGrupo2"]."' AND my.parent_id = '$id' $FiltroAdicionalmen ";
    $this->last_message("myMenuObject: Select children for id: $id<br />$sql<br />");
    $result = mysqli_query($link,$sql) or die("Database Error:  Unable to find child items for parent id: $id");
    if(mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_object ($result)) {
        $childArray[] = $row->id;
      }
    }
    return $childArray;
  }

// *****************************************************************
// Get all menu items by position order
// *****************************************************************
  function getMenuItemsOrdered() {
    $idArray = array();
	
	$FiltroAdicionalmen="";
	if ($_SESSION["miDispositivo"]>0) 
	    $FiltroAdicionalmen=" AND my.Movil>0 ";
    else
	    $FiltroAdicionalmen=" AND my.Movil=0 ";
		
    $sql = "SELECT my.id FROM mymenugenerador as my INNER JOIN grupomenu as gm ON my.id=gm.id WHERE gm.IdGrupoUsuarios='".$_SESSION["mibgIdGrupo2"]."' $FiltroAdicionalmen ORDER BY my.position";
    $this->last_message("myMenuObject: Select all menu id<br />$sql<br />");
    $result = mysqli_query($link,$sql) or die("Database Error:  Unable to connect to menu items table");
    if(mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_object ($result)) {
        $idArray[] = $row->id;
      }
    }
    return $idArray;
  }

// *****************************************************************
// Get all menu items unordered
// *****************************************************************
  function getMenuItemsID() {
    $idArray = array();
	
	$FiltroAdicionalmen="";
	if ($_SESSION["miDispositivo"]>0) 
	    $FiltroAdicionalmen=" AND my.Movil>0 ";
    else
	    $FiltroAdicionalmen=" AND my.Movil=0 ";
		
    $sql = "SELECT my.id FROM mymenugenerador as my INNER JOIN grupomenu as gm ON my.id=gm.id WHERE gm.IdGrupoUsuarios='".$_SESSION["mibgIdGrupo2"]."' $FiltroAdicionalmen ";
    $this->last_message("myMenuObject: Select all menu id<br />$sql<br />");
    $result = mysqli_query($link,$sql) or die("Database Error:  Unable to connect to menu items table");
    if(mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_object ($result)) {
        $idArray[] = $row->id;
      }
    }
    return $idArray;
  }

// *****************************************************************
// Test if menu item exists
// *****************************************************************
  function itemExists($id) {
  
  	$FiltroAdicionalmen="";
	if ($_SESSION["miDispositivo"]>0) 
	    $FiltroAdicionalmen=" AND my.Movil>0 ";
    else
	    $FiltroAdicionalmen=" AND my.Movil=0 ";
		
    if(empty($id)) {
      $this->last_message("myMenuObject: No page id supplied");
      return false;
    } else {
      $sql = "SELECT my.id FROM mymenugenerador as my INNER JOIN grupomenu as gm ON my.id=gm.id WHERE gm.IdGrupoUsuarios='".$_SESSION["mibgIdGrupo2"]."' AND my.id = '$id' $FiltroAdicionalmen LIMIT 1";
      $this->last_message("myMenuObject: Check for menu item id: $id<br />$sql<br />");
      $result = mysqli_query($link,$sql) or die("Database Error:  Unable to check for menu item id: $id");
      if(mysqli_num_rows($result) > 0) {
        return true;
      } else {
        return false;
      }
    }
  }

// *****************************************************************
// Genera el menu tomando los datos de la tabla
// *****************************************************************
  function generateMenuStructure($pid = 0, $breakAfter = 0) {
    $tStr = '';

    $topLevel = array();
    $topLevel = $this->getThisLevel($pid);
    if(count($topLevel) > 0 ) 
    {
      foreach($topLevel as $val) 
      {
        $tStr .= $this->createItems($val, $breakAfter);
      }
      return $tStr;
    } else {
      $this->last_message("myMenuObject: No puedo determinar el menu id: $pid");
    }
  }

// *****************************************************************
// Build the menu structure as a series of li items.
// Child items will be created as ul/li items
// *****************************************************************
  function createItems($pid, $breakAfter) {
    $tStr = '';
    if($this->getMenuItem($pid)) {
      // Determine if this item has children
      $childLevel = array();
      $childLevel = $this->getChildItems($pid);
      if(count($childLevel) > 0) { // Tiene sub menus
        $tStr .= '<li>';
        if(!empty($this->menu_image)) {
          $tStr .= '<span class="menuImg"><img src="'.$this->menu_image.'" /></span>';
        }
        $tStr .= '<a title="'.$this->elmensaje.'">'.$this->menu_title.'<span class="menuArrow"></span></a><ul>';
        $julie = $this->generateMenuStructure($pid, $breakAfter);
        if(!empty($julie)) {
          $tStr .= $julie.'</ul></li>';
        }
      } else {                    // No tiene sub menus
        $tStr .= '<li>';
        if (!empty($this->nuevapantalla) && $this->nuevapantalla=='Si')
           $mtpantalla="target='_blank'";
        else
         $mtpantalla="";
        if(!empty($this->menu_image)) {
          $tStr .= '<span class="menuImg"><img src="'.$this->menu_image.'" /></span>';
        }
        $tStr .= '<a title="'.$this->elmensaje.'" href=';
        if(!empty($this->page_link)) {
          $tStr .= '"'.$this->page_link.'"';
        } else {
          $tStr .= '"'.$this->rootDir.'caja.php"';
        }
        if (strlen($this->Atajo)>0)
          $tStr .= $mtpantalla .' accesskey="'.$this->Atajo.'" >'.$this->menu_title.'</a>';
        else 
          $tStr .= $mtpantalla .'  >'.$this->menu_title.'</a>';
          
        $tStr .= '</li>';
      }
    }
    return $tStr;
  }

} # class myMenuObject


?>