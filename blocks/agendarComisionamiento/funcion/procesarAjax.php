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

if ($_REQUEST ['funcion'] == "consultarBeneficiarios") {
	
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
	
	$cadenaSql = "";
	
	if(isset($_REQUEST ['tipoV']) && $_REQUEST ['tipoV'] != ""){
		$cadenaSql .= "AND bp.tipo_vivienda='" . $_REQUEST ['tipoV'] . "' ";
	}
	
	if(isset($_REQUEST ['urban']) && $_REQUEST ['urban'] != ""){
		$cadenaSql .= "AND bp.id_proyecto='" . $_REQUEST ['urban'] . "' ";
	}

	if(isset($_REQUEST ['man']) && $_REQUEST ['man'] != ""){
		$cadenaSql .= "AND bp.manzana='" . $_REQUEST ['man'] . "' ";
	}
	
	if(isset($_REQUEST ['bloq']) && $_REQUEST ['bloq'] != ""){
		$cadenaSql .= "AND bp.bloque='" . $_REQUEST ['bloq'] . "' ";
	}
	
	if(isset($_REQUEST ['torre']) && $_REQUEST ['torre'] != ""){
		$cadenaSql .= "AND bp.torre='" . $_REQUEST ['torre'] . "' ";
	}
	
	if(isset($_REQUEST ['ben']) && $_REQUEST ['ben'] != ""){
		$cadenaSql .= "AND bp.id_beneficiario='" . $_REQUEST ['ben'] . "' ";
	}
	

	if($_REQUEST ['agen'] == 1){
	$cadenaSql = $this->sql->getCadenaSql ('consultarBeneficiarios', $cadenaSql );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	}else if($_REQUEST ['agen'] == 2){
		$cadenaSql = $this->sql->getCadenaSql ('consultarBeneficiarios_comercial', $cadenaSql );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	}else{
		$resultado = false;
	}

	if($resultado!=false){
	
	for($i = 0; $i < count ( $resultado ); $i ++) {
	
		$resultadoFinal [] = array (
				'identificacion_beneficiario' => $resultado [$i] ['identificacion_beneficiario'],
				'nombre_beneficiario' => $resultado [$i] ['nombre_beneficiario'],
				'id_checkbox' => array( 'value' =>  (  
						$resultado [$i] ['id_beneficiario'] . ":"
						.$resultado [$i] ['identificacion_beneficiario'] . ":"
						.$resultado [$i] ['orden_trabajo']
						), 'id' => codificarNombre( "checkbox_" . $i ) ),
				
				
		);
	}
	
	$total = count ( $resultadoFinal );
	
	$resultado = json_encode ( $resultadoFinal );
	
	$resultado = '{
                "recordsTotal":' . $total . ',
                "recordsFiltered":' . $total . ',
				"data":' . $resultado . '}';
	}else{
		$resultado = '{
                "recordsTotal":0,
                "recordsFiltered":0,
				"data":0}';
	}

	echo $resultado;
	
}else if ($_REQUEST ['funcion'] == "inhabilitarCabecera"){
	
	$conexion = "interoperacion";
	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
	
	$cadenaSql = $this->sql->getCadenaSql ( 'inhabilitarCabecera', $_REQUEST ['valor'] );
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "actualizar" );
				
	echo $resultado;
	
}else if ($_REQUEST ['funcion'] == "redireccionar"){
	
	include_once ("core/builder/FormularioHtml.class.php");
	
	$miFormulario = new \FormularioHtml();
	
	if(!isset($_REQUEST['tiempo'])){
		$_REQUEST['tiempo']=time();
	}
	//Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php 
	
	$_REQUEST['ready']= true;
	
	$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $_REQUEST ['valor'] . $_REQUEST ['id']);
	
	$enlace = $_REQUEST ['directorio'] . '=' . $valorCodificado;
	
	echo json_encode($enlace);
		
}else if ($_REQUEST ['funcion'] == "consultarUrbanizacion") {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarUrbanizacion' );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	if($resultadoItems){
		foreach ( $resultadoItems as $key => $values ) {
			$keys = array (
					'value',
					'data' 
			);
			$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
		}
		echo '{"suggestions":' . json_encode ( $resultado ) . '}';
	}
}else if ($_REQUEST ['funcion'] == "consultarManzana") {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarManzana' );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	if($resultadoItems){
		foreach ( $resultadoItems as $key => $values ) {
			$keys = array (
					'value',
					'data' 
			);
			$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
		}
		echo '{"suggestions":' . json_encode ( $resultado ) . '}';
	}
	
}else if ($_REQUEST ['funcion'] == "consultarBloque") {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarBloque' );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	if($resultadoItems){
		foreach ( $resultadoItems as $key => $values ) {
			$keys = array (
					'value',
					'data' 
			);
			$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
		}
		echo '{"suggestions":' . json_encode ( $resultado ) . '}';
	}
	
}else if ($_REQUEST ['funcion'] == "consultarTorre") {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarTorre' );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	if($resultadoItems){
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
