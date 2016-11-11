<?php
$conexion = "interoperacion";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

$conexion = "openproject";
$esteRecursoDBOP = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
function codificarNombre($nombre) {
	include_once "core/builder/FormularioHtml.class.php";
	
	$miFormulario = new \FormularioHtml ();
	
	if (! isset ( $_REQUEST ['tiempo'] )) {
		$_REQUEST ['tiempo'] = time ();
	}
	// Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php
	
	$_REQUEST ['ready'] = true;
	
	return $miFormulario->campoSeguro ( $nombre );
}
if ($_REQUEST ['funcion'] == "consultarComisionador") {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'comisionador' );
	$resultadoItems = $esteRecursoDBOP->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	foreach ( $resultadoItems as $key => $values ) {
		$keys = array (
				'value',
				'data' 
		);
		$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
	}
	echo '{"suggestions":' . json_encode ( $resultado ) . '}';
} else if ($_REQUEST ['funcion'] == "consultarAgendamiento") {

	$tipoA = trim ( $_REQUEST ['tipoA'] );
	$urban = trim ( $_REQUEST ['urban'] );
	$comis = trim ( $_REQUEST ['comis'] );
	$ben = trim ( $_REQUEST ['ben'] );
	
	$cadenaSql = "";
	
	if (isset ( $tipoA ) && $tipoA != "") {
		$cadenaSql .= "AND ac.tipo_agendamiento='" . $tipoA . "' ";
	}
	
	if (isset ( $_REQUEST ['man'] ) && $_REQUEST ['man'] != "") {
		$cadenaSql .= "AND bp.manzana='" . $_REQUEST ['man'] . "' ";
	}
	
	if (isset ( $_REQUEST ['bloq'] ) && $_REQUEST ['bloq'] != "") {
		$cadenaSql .= "AND bp.bloque='" . $_REQUEST ['bloq'] . "' ";
	}
	
	if (isset ( $_REQUEST ['torre'] ) && $_REQUEST ['torre'] != "") {
		$cadenaSql .= "AND bp.torre='" . $_REQUEST ['torre'] . "' ";
	}
	
	if (isset ( $urban ) && $urban != "") {
		$cadenaSql .= "AND bp.id_proyecto='" . $urban . "' ";
	}
	
	if (isset ( $comis ) && $comis != "") {
		$cadenaSql .= "AND ac.id_comisionador='" . $comis . "' ";
	}
	
	if (isset ( $ben ) && $ben != "") {
		$cadenaSql .= "AND bp.id_beneficiario='" . $ben . "' ";
	}
	
	$conexion = "interoperacion";
	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
	
 	$cadenaSql = $this->sql->getCadenaSql ( 'consultarAgendaVia', $cadenaSql );
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarAgendaIns', $cadenaSql );
	$resultado2 = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	$ruta = $this->miConfigurador->getVariableConfiguracion ( 'rutaUrlBloque' );
	
	if ($resultado != false && $resultado2 != false) {
		$resultado = array_merge ( $resultado, $resultado2 );
	}
	
	if ($resultado2 != false && $resultado== false) {
		$resultado = $resultado2 ;
	}

	if ($resultado != false) {
		
		foreach ( $resultado as $i => $values ) {
			$variable = "actionBloque=registroBeneficiario";
			$variable .= "&pagina=registroBeneficiario";
			$variable .= "&bloque=registroBeneficiario";
			$variable .= "&id=" . $resultado [$i] ['id_beneficiario'];
			$url = $this->miConfigurador->configuracion ["host"] . $this->miConfigurador->configuracion ["site"] . "/index.php?";
			$enlace = $this->miConfigurador->configuracion ['enlace'];
			$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $variable );
			$_REQUEST [$enlace] = $enlace . '=' . $variable;
			$redireccion [$i] = $url . $_REQUEST [$enlace];
		}
		
		foreach ( $resultado as $i => $values ) {
			$variable = "actionBloque=registroBeneficiario";
			$variable .= "&pagina=registroBeneficiario";
			$variable .= "&bloque=registroBeneficiario";
			$variable .= "&id=" . $resultado [$i] ['id_beneficiario'];
			$url = $this->miConfigurador->configuracion ["host"] . $this->miConfigurador->configuracion ["site"] . "/index.php?";
			$enlace = $this->miConfigurador->configuracion ['enlace'];
			$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $variable );
			$_REQUEST [$enlace] = $enlace . '=' . $variable;
			$observaciones [$i] = $url . $_REQUEST [$enlace];
		}
		
		for($i = 0; $i < count ( $resultado ); $i ++) {
			
			$resultadoFinal [] = array (
					
					'id_agendamiento' => $resultado [$i] ['id_agendamiento'],
					'fecha' => $resultado [$i] ['fecha'],
					'beneficiario' => "<a href='" . $redireccion [$i] . "'>" . $resultado [$i] ['beneficiario'] . "</a>",
					'comisionador' => $resultado [$i] ['nombre_comisionador'],
					// tipo_agendamientoa' => $resultado [$i] ['tipo_agendamiento'],
					'estado_agenda' => "<img src='" . $ruta . "/css/imagenes/" . $resultado [$i] ['estado_agenda'] . ".png" . "'>   <br>   <b>" . $resultado [$i] ['etiqueta_agenda'] . "</b></img>",
					'id_checkbox' => array (
							'value' => $resultado [$i] ['consecutivo'],
							'id' => codificarNombre ( "checkbox_" . $i ) 
					) 
			);
			// 'estado_agenda'=>0
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
} elseif ($_REQUEST ['funcion'] == "redireccionar") {
	
	include_once ("core/builder/FormularioHtml.class.php");
	
	$miFormulario = new \FormularioHtml ();
	
	if (! isset ( $_REQUEST ['tiempo'] )) {
		$_REQUEST ['tiempo'] = time ();
	}
	// Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php
	
	$_REQUEST ['ready'] = true;
	
	$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $_REQUEST ['valor'] . $_REQUEST ['id'] );
	
	$enlace = $_REQUEST ['directorio'] . '=' . $valorCodificado;
	
	echo json_encode ( $enlace );
} else if ($_REQUEST ['funcion'] == "consultarUrbanizacion") {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarUrbanizacion' );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	foreach ( $resultadoItems as $key => $values ) {
		$keys = array (
				'value',
				'data' 
		);
		$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
	}
	echo '{"suggestions":' . json_encode ( $resultado ) . '}';
} elseif ($_REQUEST ['funcion'] == "consultarBeneficiarios") {
	
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
} else if ($_REQUEST ['funcion'] == "consultarManzana") {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarManzana' );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	if ($resultadoItems) {
		foreach ( $resultadoItems as $key => $values ) {
			$keys = array (
					'value',
					'data' 
			);
			$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
		}
		echo '{"suggestions":' . json_encode ( $resultado ) . '}';
	}
} else if ($_REQUEST ['funcion'] == "consultarBloque") {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarBloque' );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	if ($resultadoItems) {
		foreach ( $resultadoItems as $key => $values ) {
			$keys = array (
					'value',
					'data' 
			);
			$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
		}
		echo '{"suggestions":' . json_encode ( $resultado ) . '}';
	}
} else if ($_REQUEST ['funcion'] == "consultarTorre") {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarTorre' );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	if ($resultadoItems) {
		foreach ( $resultadoItems as $key => $values ) {
			$keys = array (
					'value',
					'data' 
			);
			$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
		}
		echo '{"suggestions":' . json_encode ( $resultado ) . '}';
	}
}
?>
