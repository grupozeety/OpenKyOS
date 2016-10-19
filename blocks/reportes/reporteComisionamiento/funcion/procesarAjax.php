<?php

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

if ($_REQUEST ['funcion'] == "consultarCabecera") {
	
	$conexion = "interoperacion";
	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarAgendamiento' );
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

	for($i = 0; $i < count ( $resultado ); $i ++) {
	
		
		$resultadoFinal [] = array (
				'id_agendamiento' =>  $resultado [$i] ['id_agendamiento'],
				'orden_trabajo' => $resultado [$i] ['orden_trabajo'],
				'urbanizacion' => $resultado [$i] ['urbanizacion'],
				'comisionador' => $resultado [$i] ['comisionador'],
				'tipo_agendamiento' => $resultado [$i] ['tipo_agendamiento'],
				'identificacion_beneficiario' => $resultado [$i] ['identificacion_beneficiario'],
				'nombre_beneficiario' => $resultado [$i] ['nombre_beneficiario'],
				'codigo_nodo' => $resultado [$i] ['codigo_nodo'],
				'id_checkbox' => array( 'value' =>  (  
						$resultado [$i] ['id_agendamiento'] . ":"
						. $resultado [$i] ['orden_trabajo'] . ":"
						. $resultado [$i] ['urbanizacion'] . ":"
						. $resultado [$i] ['comisionador'] . ":"
						. $resultado [$i] ['tipo_agendamiento'] . ":"
						. $resultado [$i] ['codigo_nodo'] . ":"
						. $resultado [$i] ['identificacion_beneficiario'] . ":"
						. $resultado [$i] ['nombre_beneficiario']
						), 'id' => codificarNombre( "checkbox_" . $i ) ),
				
				
		);
	}
	
	$total = count ( $resultadoFinal );
	
	$resultado = json_encode ( $resultadoFinal );
	
	$resultado = '{
                "recordsTotal":' . $total . ',
                "recordsFiltered":' . $total . ',
				"data":' . $resultado . '}';
	
	echo $resultado;
	
}

?>