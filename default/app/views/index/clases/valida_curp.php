<?php
//validar curp con pagina .gob
if(isset($_POST['curp'])){
$curp = $_POST['curp'];
$curp = strtoupper($curp);
$url = "http://consultas.curp.gob.mx/CurpSP/curp2.do?strCurp=".$curp."&strTipo=B&entfija=DF&depfija=04";
$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL,$url);
curl_setopt ($ch, CURLOPT_HEADER, 0);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
$page = trim(curl_exec($ch));

$pos1 = strpos($page, 'var strCurp="')+13;
$pos2 = $pos1+18;
if(isset($pos1)){
$curpg=substr($page,$pos1,$pos2-$pos1);
if($curp==$curpg){
echo 'la curp es correcta';
}else{
echo 'porfavor verifique su curp';
}
}
}
?> 