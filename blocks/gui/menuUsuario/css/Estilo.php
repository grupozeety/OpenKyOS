<?php
$indice=0;
// $estilo[$indice++]="bootstrap.min.css";
$estilo[$indice++]="styles.css";
// $estilo[$indice++]="bootstrap.css";
$estilo[$indice++]="elegant-icons.css";
$estilo[$indice++]="estiloBloque.css";
$estilo[$indice++]="contenido/font-awesome.min.css";
$estilo[$indice++]="AdminLTE.min.css";






$rutaBloque=$this->miConfigurador->getVariableConfiguracion("host");
$rutaBloque.=$this->miConfigurador->getVariableConfiguracion("site");

if($unBloque["grupo"]==""){
	$rutaBloque.="/blocks/".$unBloque["nombre"];
}else{
	$rutaBloque.="/blocks/".$unBloque["grupo"]."/".$unBloque["nombre"];
}
// echo '<link href="'.$rutaBloque.'/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">';
foreach ($estilo as $nombre){
	echo "<link rel='stylesheet' type='text/css' href='".$rutaBloque."/css/".$nombre."'>\n";

}
?>
