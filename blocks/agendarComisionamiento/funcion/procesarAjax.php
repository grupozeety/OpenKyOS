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
	
// 	$cadenaSql = $this->sql->getCadenaSql ( 'consultarCabecera' );
// 	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

	$resultado [0] ['urbanizacion'] = "El Recuerdo";
	$resultado [0] ['id_urbanizacion'] = "3";
	$resultado [0] ['celda'] = "ND-01";
	$resultado [0] ['orden_trabajo'] = "1980";
	$resultado [0] ['manzana'] = 1;
	$resultado [0] ['bloque'] = 1;
	$resultado [0] ['torre'] = 5;
	$resultado [0] ['apartamento'] = 302;
	$resultado [0] ['identificacion_beneficiario'] = "1032418216";
	$resultado [0] ['nombre_beneficiario'] = "Emmanuel Taborda";
	
	$resultado [1] ['urbanizacion'] = "El Recuerdo";
	$resultado [1] ['id_urbanizacion'] = "3";
	$resultado [1] ['celda'] = "ND-01";
	$resultado [1] ['orden_trabajo'] = "1902";
	$resultado [1] ['manzana'] = 1;
	$resultado [1] ['bloque'] = 1;
	$resultado [1] ['torre'] = 6;
	$resultado [1] ['apartamento'] = 101;
	$resultado [1] ['identificacion_beneficiario'] = "1023452211";
	$resultado [1] ['nombre_beneficiario'] = "Stiv Verdugo";
	
	$resultado [2] ['urbanizacion'] = "La Gloria";
	$resultado [2] ['id_urbanizacion'] = "2";
	$resultado [2] ['celda'] = "CE-120";
	$resultado [2] ['orden_trabajo'] = "1921";
	$resultado [2] ['manzana'] = 5;
	$resultado [2] ['bloque'] = 2;
	$resultado [2] ['torre'] = 3;
	$resultado [2] ['apartamento'] = 505;
	$resultado [2] ['identificacion_beneficiario'] = "1025565656";
	$resultado [2] ['nombre_beneficiario'] = "Violeta Sosa";
	
	
	for($i = 0; $i < count ( $resultado ); $i ++) {
	
		
		$resultadoFinal [] = array (
				'urbanizacion' =>  $resultado [$i] ['urbanizacion'],
				'celda' => $resultado [$i] ['celda'],
				'manzana' => $resultado [$i] ['manzana'],
				'bloque' => $resultado [$i] ['bloque'],
				'torre' => $resultado [$i] ['torre'],
				'identificacion_beneficiario' => $resultado [$i] ['identificacion_beneficiario'],
				'nombre_beneficiario' => $resultado [$i] ['nombre_beneficiario'],
				'id_checkbox' => array( 'value' =>  (  
						$resultado [$i] ['urbanizacion'] . ":"
						. $resultado [$i] ['id_urbanizacion'] . ":"
						. $resultado [$i] ['celda'] . ":"
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