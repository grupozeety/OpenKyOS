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

if ($_REQUEST ['funcion'] == "consultarComisionador") {

	$cadenaSql = $this->sql->getCadenaSql ( 'consultarComisionador' );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

	foreach ( $resultadoItems as $key => $values ) {
		$keys = array (
				'value',
				'data'
		);
		$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
	}
	echo '{"suggestions":' . json_encode ( $resultado ) . '}';

} else if ($_REQUEST ['funcion'] == "consultarAgendamiento") {
	
	$manz = trim($_REQUEST ['manz']);
	$tipoA = trim($_REQUEST ['tipoA']);
	$urban = trim($_REQUEST ['urban']);
	$comis = trim($_REQUEST ['comis']);
	
	$cadenaSql = "";
	
	if(isset($tipoA) && $tipoA != ""){
		$cadenaSql .= "AND ac.tipo_agendamiento='" . $tipoA . "' ";
	}
	
	if(isset($manz) && $manz != ""){
		$cadenaSql .= "AND bp.manzana='" . $manz . "' ";
	}
	
	if(isset($urban) && $urban != ""){
		$cadenaSql .= "AND bp.id_proyecto='" . $urban . "' ";
	}
	
	if(isset($comis) && $comis != ""){
		$cadenaSql .= "AND ac.id_comisionador='" . $comis . "' ";
	}
	
	$conexion = "interoperacion";
	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarAgendamiento', $cadenaSql );
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

	if($resultado!=false){
		for($i = 0; $i < count ( $resultado ); $i ++) {
		
			
			$resultadoFinal [] = array (
					'id_agendamiento' =>  $resultado [$i] ['id_agendamiento'],
					//'identificacion_beneficiario' => $resultado [$i] ['identificacion_beneficiario'],
					'nombre_beneficiario' => $resultado [$i] ['nombre_beneficiario'],
					'id_checkbox' => array( 'value' =>  $resultado [$i] ['consecutivo'],
					'id' => codificarNombre( "checkbox_" . $i ) ),
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

}else if ($_REQUEST ['funcion'] == "consultarManzana") {

	$cadenaSql = $this->sql->getCadenaSql ( 'consultarManzana' );
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

