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
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarBeneficiarios' );
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

	for($i = 0; $i < count ( $resultado ); $i ++) {
	
		$resultadoFinal [] = array (
				'urbanizacion' =>  $resultado [$i] ['urbanizacion'],
				'celda' => $resultado [$i] ['codigo_nodo'],
				'manzana' => $resultado [$i] ['manzana'],
				'bloque' => $resultado [$i] ['bloque'],
				'torre' => $resultado [$i] ['torre'],
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
		
}

?>