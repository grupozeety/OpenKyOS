<?php

include_once ("core/builder/FormularioHtml.class.php");

$miFormulario = new \FormularioHtml();

if(!isset($_REQUEST['tiempo'])){
	$_REQUEST['tiempo']=time();
}
//Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php 

$_REQUEST['ready']= true;


if ($_REQUEST ['funcion'] == "codificarNombre") {
	
	$codificado['material'] = $miFormulario->campoSeguro("item[]");
	$codificado['unidad'] = $miFormulario->campoSeguro("unidad".$_REQUEST ['valor']);
	$codificado['cantidad'] = $miFormulario->campoSeguro("cantidad".$_REQUEST ['valor']);
	
	echo json_encode($codificado);
	
}
	

?>