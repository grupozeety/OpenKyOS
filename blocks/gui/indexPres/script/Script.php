<?php

$indice=0;
$funcion[$indice ++]="modernizr.custom.js";
$funcion[$indice ++]="cbpHorizontalMenu.js";
$funcion[$indice ++]="cbpHorizontalMenu.min.js";
$funcion[$indice ++]="jquery.min.js";
$funcion[$indice ++]="jssor.slider.mini.js";


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

?>