<?php
use asignacionPuntajes\salariales\premiosDocente\Sql;

$conexion = "docencia";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );


//Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php 

if ($_REQUEST ['funcion'] == 'consultarPais') {
	$cadenaSql = $this->sql->getCadenaSql ( 'pais', $_REQUEST["valor"]);
	$datos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	echo json_encode( $datos );
}



if ($_REQUEST ['funcion'] == 'consultarCiudad') {
	$cadenaSql = $this->sql->getCadenaSql ( 'ciudad', $_REQUEST["valor"]);
	$datos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	echo json_encode( $datos );
}

?>