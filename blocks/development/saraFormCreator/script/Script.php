<?php
/**
 * Importante: Este script es invocado desde la clase ArmadorPagina. La información del bloque se encuentra
 * en el arreglo $esteBloque. Esto también aplica para todos los archivos que se incluyan.
 *
 * CUANDO SE NECESITE REGISTRAR OPCIONES PARA LA FUNCIÓN ready DE JQuery, SE DEBE DECLARAR EN ARCHIVOS DENOMINADOS
 * ready.js o ready.php. DICHOS ARCHIVOS DEBEN IR EN LA CARPETA script DE LOS BLOQUES PERO NO RELACIONARSE AQUI. 
 */
// Registrar los archvos js que deben incluirse

$indice=-1;
$funcion[$indice++]='jquery.js';
$funcion[$indice++]='bootstrap.js';
$funcion[$indice++]='jquery-sortable.js';
$funcion[$indice++]='ace/src-noconflict/ace.js';

$rutaBloque=$this->miConfigurador->getVariableConfiguracion("host");
$rutaBloque.=$this->miConfigurador->getVariableConfiguracion("site");
if($esteBloque["grupo"]==""){
	$rutaBloque.="/blocks/".$esteBloque["nombre"];
}else{
	$rutaBloque.="/blocks/".$esteBloque["grupo"]."/".$esteBloque["nombre"];
}
foreach ($funcion as $clave=>$nombre){
	if(!isset($embebido[$clave])){
		echo "\n<script type='text/javascript' src='".$rutaBloque."/script/".$nombre."'>\n</script>\n";
	}else{
		echo "\n<script type='text/javascript'>";
		include($nombre);
		echo "\n</script>\n";
	}
}

/**
 * Incluir los scripts que deben registrarse como javascript pero requieren procesamiento previo de código php
 */
//include("archivoPHP con código js embebido.php");
// Procesar las funciones requeridas en ajax
echo "\n<script type='text/javascript'>";
	include("sara.js.php");
echo "\n</script>\n";
?>