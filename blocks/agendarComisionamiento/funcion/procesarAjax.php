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
	
	$cadenaSql = "";
	
	if(isset($_REQUEST ['tipoV']) && $_REQUEST ['tipoV'] != ""){
		$cadenaSql .= "AND bp.tipo_vivienda='" . $_REQUEST ['tipoV'] . "' ";
	}
	
	if(isset($_REQUEST ['urban']) && $_REQUEST ['urban'] != ""){
		$cadenaSql .= "AND bp.id_proyecto='" . $_REQUEST ['urban'] . "' ";
	}
	
	if(isset($_REQUEST ['bloq_man']) && $_REQUEST ['bloq_man'] != ""){
		$cadenaSql .= "AND bp.bloque='" . $_REQUEST ['bloq_man'] . "' ";
	}
	
	$cadenaSql = $this->sql->getCadenaSql ('consultarBeneficiarios_comercial', $cadenaSql );
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	if($resultado!=false){
	
	for($i = 0; $i < count ( $resultado ); $i ++) {
	
		$resultadoFinal [] = array (
				'identificacion_beneficiario' => $resultado [$i] ['identificacion_beneficiario'],
				'nombre_beneficiario' => $resultado [$i] ['nombre_beneficiario'],
				'id_checkbox' => array( 'value' =>  (  
						$resultado [$i] ['urbanizacion'] . ":"
						. $resultado [$i] ['id_urbanizacion'] . ":"
						. $resultado [$i] ['codigo_nodo'] . ":"
						. $resultado [$i] ['orden_trabajo'] . ":"
						. $resultado [$i] ['manzana'] . ":"
						. $resultado [$i] ['bloque'] . ":"
						. $resultado [$i] ['torre'] . ":"
						. $resultado [$i] ['apartamento'] . ":"
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
	
	foreach ( $resultadoItems as $key => $values ) {
		$keys = array (
				'value',
				'data' 
		);
		$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
	}
	echo '{"suggestions":' . json_encode ( $resultado ) . '}';
	
}else if ($_REQUEST ['funcion'] == "consultarBloqueManzana") {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarBloqueManzana' );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	foreach ( $resultadoItems as $key => $values ) {
		$keys = array (
				'value',
				'data' 
		);
		$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
	}
	echo '{"suggestions":' . json_encode ( $resultado ) . '}';
	
}

?>