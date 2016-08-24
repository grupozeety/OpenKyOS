<?php
$indice=0;

$estilo[$indice++]="bootstrap.min.css";
$estilo[$indice++]="font-awesome.min.css";
$estilo[$indice++]="animate.min.css";
$estilo[$indice++]="owl.carousel.css";
$estilo[$indice++]="owl.transitions.css";
$estilo[$indice++]="prettyPhoto.css";
$estilo[$indice++]="main.css";
$estilo[$indice++]="responsive.css";
$estilo[$indice++]="miestilo.css";
$estilo[$indice++]="jquery.jdigiclock.css";


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
