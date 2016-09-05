<?php

$conexion = "interoperacion";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

if(isset($_REQUEST['valor'])){
	$cadenaSql = $this->sql->getCadenaSql ( 'obtenerConsumo', $_REQUEST['valor'] );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	echo json_encode ( $resultadoItems );
}

?>
