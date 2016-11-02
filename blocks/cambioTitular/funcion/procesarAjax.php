<?php
$conexion = "interoperacion";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

function codificarNombre($nombre) {

	include_once "core/builder/FormularioHtml.class.php";

	$miFormulario = new \FormularioHtml();

	
	if (!isset($_REQUEST['tiempo'])) {
		$_REQUEST['tiempo'] = time();
	}
	//Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php

	$_REQUEST['ready'] = true;

	return $miFormulario->campoSeguro($nombre);

}

if ($_REQUEST ['funcion'] == "consultaBeneficiarios") {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarBeneficiariosPotenciales' );
	
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	foreach ( $resultadoItems as $key => $values ) {
		$keys = array (
				'value',
				'data' 
		);
		$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
	}
	echo '{"suggestions":' . json_encode ( $resultado ) . '}';
	
} else if ($_REQUEST ['funcion'] == "consultarCabecera") {

	$conexion = "interoperacion";
	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
	
	$cadenaSql = $this->sql->getCadenaSql ('cargarFamiliares', $_REQUEST['valor'] );
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	if ($resultado != false) {
		
		for($i = 0; $i < count ( $resultado ); $i ++) {
			
			$resultadoFinal [] = array (
					'persona' => $resultado [$i] ['nombre_familiar'],
					'id_checkbox' => array (
							'value' => ( $resultado [$i] ['identificacion_familiar']),
							'id' => codificarNombre ( "titular" ) 
					) 
			)
			;
		}
		
		$total = count ( $resultadoFinal );
		
		$resultado = json_encode ( $resultadoFinal );
		
		$resultado = '{
                "recordsTotal":' . $total . ',
                "recordsFiltered":' . $total . ',
				"data":' . $resultado . '}';
	} else {
		$resultado = '{
                "recordsTotal":0,
                "recordsFiltered":0,
				"data":0}';
	}
	
	echo $resultado;
}

?>