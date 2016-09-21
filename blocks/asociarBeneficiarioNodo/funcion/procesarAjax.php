<?php

$conexion = "interoperacion";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

if ($_REQUEST ['funcion'] == "consultarNodo") {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarNodo' );
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

	for($i = 0; $i < count ( $resultado ); $i ++) {
	
		$resultadoFinal [] = array (
				'codigo_nodo' =>  $resultado [$i] ['codigo_nodo'],
				'codigo_cabecera' => $resultado [$i] ['codigo_cabecera'],
				'tipo_tecnologia' => $resultado [$i] ['tipo_tecnologia'],
				'urbanizacion' => $resultado [$i] ['urbanizacion']
		);
	}
	
	$total = count ( $resultadoFinal );
	
	$resultado = json_encode ( $resultadoFinal );
	
	$resultado = '{
                "recordsTotal":' . $total . ',
                "recordsFiltered":' . $total . ',
				"data":' . $resultado . '}';
	
	echo $resultado;
	
}else if ($_REQUEST ['funcion'] == "inhabilitarNodo"){
	
	$conexion = "interoperacion";
	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
	
	$cadenaSql = $this->sql->getCadenaSql ( 'inhabilitarNodo', $_REQUEST ['valor'] );
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
		
}else if ($_REQUEST ['funcion'] == "consultarCabecera") {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'parametroTipoTecnologia' );
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

	for($i = 0; $i < count ( $resultado ); $i ++) {
	
		$resultadoFinal [] = array("id" => $resultado [$i] ['descripcion'], "value"=>$resultado [$i] ['descripcion']);
		
	}
	
	$total = count ( $resultadoFinal );
	
	$resultado = json_encode ( $resultadoFinal );
	
	$resultado = $resultado;
	
	echo  $resultado;
	
}

	
?>