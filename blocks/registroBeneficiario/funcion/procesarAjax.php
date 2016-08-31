<?php

include_once ("core/builder/FormularioHtml.class.php");

$miFormulario = new \FormularioHtml();

if(!isset($_REQUEST['tiempo'])){
	$_REQUEST['tiempo']=time();
}
//Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php 

$_REQUEST['ready']= true;


if ($_REQUEST ['funcion'] == "codificar") {
	
	$codificado['identificacion'] = $miFormulario->campoSeguro("identificacion_familiar_".$_REQUEST ['valor']);
	$codificado['nombre'] = $miFormulario->campoSeguro("nombre_familiar_".$_REQUEST ['valor']);
	$codificado['genero'] = $miFormulario->campoSeguro("genero_familiar_".$_REQUEST ['valor']);
	$codificado['edad'] = $miFormulario->campoSeguro("edad_familiar_".$_REQUEST ['valor']);
	$codificado['nivel_estudio'] = $miFormulario->campoSeguro("nivel_estudio_familiar_".$_REQUEST ['valor']);
	$codificado['correo'] = $miFormulario->campoSeguro("correo_familiar_".$_REQUEST ['valor']);
	$codificado['grado'] = $miFormulario->campoSeguro("grado_familiar_".$_REQUEST ['valor']);
	$codificado['institucion_educativa'] = $miFormulario->campoSeguro("institucion_educativa_familiar_".$_REQUEST ['valor']);
	$codificado['pertenencia_etnica'] = $miFormulario->campoSeguro("pertenencia_etnica_familiar_".$_REQUEST ['valor']);
	$codificado['ocupacion'] = $miFormulario->campoSeguro("ocupacion_familiar_".$_REQUEST ['valor']);
	
	
	echo json_encode($codificado);
	
}
	

?>