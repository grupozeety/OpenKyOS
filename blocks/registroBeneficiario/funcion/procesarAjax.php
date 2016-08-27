<?php

$conexion = "interoperacion";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

	$cadenaSql = $this->sql->getCadenaSql ( 'obtenerConsumo', $_REQUEST['valor'] );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	echo json_encode ( $resultadoItems );
?>