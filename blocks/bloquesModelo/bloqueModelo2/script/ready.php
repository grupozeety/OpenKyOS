<?php
/**
 * En este archivo deben colocarse las funciones js que tengan algún tipo de preprocesamiento
 */

/**
 * Ejemplo 1: Como declarar una función cuando el nombre del campo debe ser seguro
 */
include_once ("core/builder/FormularioHtml.class.php");
$miFormulario = new \FormularioHtml ();

if(!isset($_REQUEST['tiempo'])){
	$_REQUEST['tiempo']=time();
}

$_REQUEST['ready']=true;
$miFormulario->campoSeguro('archivoDatos');
$campo=$miFormulario->atributos['id'];

?>$('#<?php 
echo $campo
?>.')keydown(function(e) {
	if (e.keyCode == 13) {
		$('#login').submit();
	}
});
