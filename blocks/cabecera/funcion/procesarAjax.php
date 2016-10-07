<?php

if ($_REQUEST ['funcion'] == "consultarCabecera") {
	
	$conexion = "interoperacion";
	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarCabecera' );
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

	for($i = 0; $i < count ( $resultado ); $i ++) {
	
		$resultadoFinal [] = array (
				'codigo_cabecera' =>  $resultado [$i] ['codigo_cabecera'],
				'descripcion' => $resultado [$i] ['descripcion'],
				'departamento' => $resultado [$i] ['departamento'],
				'municipio' => $resultado [$i] ['municipio'],
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
		
}else if($_REQUEST['funcion'] == 'consultarProyectos') {

	include_once "consultarProyectos.php";

}

	
?>