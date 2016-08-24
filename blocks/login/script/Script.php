<?php

$indice=0;
$funcion[$indice++]="jquery.js";
$funcion[$indice++]="bootstrap.min.js";
$funcion[$indice++]="owl.carousel.min.js";
$funcion[$indice++]="mousescroll.js";
$funcion[$indice++]="smoothscroll.js";
$funcion[$indice++]="jquery.prettyPhoto.js";
$funcion[$indice++]="jquery.isotope.min.js";
$funcion[$indice++]="jquery.inview.min.js";
$funcion[$indice++]="wow.min.js";
$funcion[$indice++]="main.js";

$funcion[$indice++]="jquery-1.3.2.min.js";
$funcion[$indice++]="jquery.jdigiclock.js";



$rutaBloque=$this->miConfigurador->getVariableConfiguracion("host");
$rutaBloque.=$this->miConfigurador->getVariableConfiguracion("site");

if($esteBloque["grupo"]==""){
	$rutaBloque.="/blocks/".$esteBloque["nombre"];
}else{
	$rutaBloque.="/blocks/".$esteBloque["grupo"]."/".$esteBloque["nombre"];
}

if(isset($funcion[0])){
foreach ($funcion as $clave=>$nombre){
	if(!isset($embebido[$clave])){
		echo "\n<script type='text/javascript' src='".$rutaBloque."/script/".$nombre."'>\n</script>\n";
	}else{
		echo "\n<script type='text/javascript'>";
		include($nombre);
		echo "\n</script>\n";
	}
}
}

// echo '<script src="http://maps.google.com/maps/api/js?sensor=true"></script>';


?>


