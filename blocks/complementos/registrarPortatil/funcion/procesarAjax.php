<?php
// Realizar la búsqueda

$conexion = 'conexionesdigitales';
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
if ($esteRecursoDB) {
	
	$cadena_sql = $this->sql->getCadenaSql ( 'totalEquipos', '' );
	$registro = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "busqueda" );
	// Armar la respuesta JSON
	if ($registro) {
		
		$total=$registro[0][0];
	}else{
		$total=0;
	}
	
	$cadena_sql = $this->sql->getCadenaSql ( 'buscarEquipos', '' );
	$registro = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "busqueda" );
	// Armar la respuesta JSON
	if ($registro) {
		$respuesta = '{';
		$respuesta .= '"draw":1,';
		$respuesta .= '"recordsTotal": '.$total.',';
		$respuesta .= '"recordsFiltered": 57,';				
		$respuesta .= '"data":[';
		foreach ( $registro as $fila ) {
			
			$respuesta .= '[';
			$respuesta .= '"' . trim($fila [0]) . '",';
			$respuesta .= '"' . trim($fila [1]) . '",';
			$respuesta .= '"' . trim($fila [2]) . '",';
			$respuesta .= '"' . trim($fila [3]) . '"';
			$respuesta .= '],';
		}
		$respuesta = substr ( $respuesta, 0, strlen ( $respuesta ) - 1 );
		$respuesta .= ']';
		$respuesta .= '}';
		echo $respuesta;
	} else {
		echo '[{"label":"No encontrado","value":"-1"}]';
	}
}else{
	echo '[{"label":"Error de Acceso a la Base de Datos","value":"-1"}]';
	
}