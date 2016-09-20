<?php
if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}


$indice=0;

$estilo[$indice++]="jquery-ui.min.css";
$estilo[$indice++]="dataTables.bootstrap.min.css";
$estilo[$indice++]="select2.min.css";
$estilo[$indice++]="miestilo.css";
$estilo[$indice++]="tokenfield-typeahead.min.css";
$estilo[$indice++]="bootstrap-tokenfield.min.css";



$rutaBloque=$this->miConfigurador->getVariableConfiguracion("host");
$rutaBloque.=$this->miConfigurador->getVariableConfiguracion("site");

if($unBloque["grupo"]==""){
	$rutaBloque.="/blocks/".$unBloque["nombre"];
}else{
	$rutaBloque.="/blocks/".$unBloque["grupo"]."/".$unBloque["nombre"];
}

foreach ($estilo as $nombre){
	echo "<link rel='stylesheet' type='text/css' href='".$rutaBloque."/css/".$nombre."'>\n";

}
?>
