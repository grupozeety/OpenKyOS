<?php

include_once ("core/builder/FormularioHtml.class.php");

$miFormulario = new \FormularioHtml();

if(!isset($_REQUEST['tiempo'])){
	$_REQUEST['tiempo']=time();
}
//Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php 

$_REQUEST['ready']= true;

if ($_REQUEST ['funcion'] == "codificar") {
	
	$codificado['tipo_documento'] = $miFormulario->campoSeguro("tipo_documento_familiar_".$_REQUEST ['valor']);
	$codificado['identificacion'] = $miFormulario->campoSeguro("identificacion_familiar_".$_REQUEST ['valor']);
	$codificado['nombre'] = $miFormulario->campoSeguro("nombre_familiar_".$_REQUEST ['valor']);
	$codificado['primer_apellido'] = $miFormulario->campoSeguro("primer_apellido_familiar_".$_REQUEST ['valor']);
	$codificado['segundo_apellido'] = $miFormulario->campoSeguro("segundo_apellido_familiar_".$_REQUEST ['valor']);
	$codificado['parentesco'] = $miFormulario->campoSeguro("parentesco_".$_REQUEST ['valor']);
	$codificado['genero'] = $miFormulario->campoSeguro("genero_familiar_".$_REQUEST ['valor']);
	$codificado['edad'] = $miFormulario->campoSeguro("edad_familiar_".$_REQUEST ['valor']);
	$codificado['celular'] = $miFormulario->campoSeguro("celular_familiar_".$_REQUEST ['valor']);
	$codificado['nivel_estudio'] = $miFormulario->campoSeguro("nivel_estudio_familiar_".$_REQUEST ['valor']);
	$codificado['correo'] = $miFormulario->campoSeguro("correo_familiar_".$_REQUEST ['valor']);
	$codificado['grado'] = $miFormulario->campoSeguro("grado_familiar_".$_REQUEST ['valor']);
	$codificado['institucion_educativa'] = $miFormulario->campoSeguro("institucion_educativa_familiar_".$_REQUEST ['valor']);
	$codificado['pertenencia_etnica'] = $miFormulario->campoSeguro("pertenencia_etnica_familiar_".$_REQUEST ['valor']);
	$codificado['ocupacion'] = $miFormulario->campoSeguro("ocupacion_familiar_".$_REQUEST ['valor']);
	
	echo json_encode($codificado);
	
}

if ($_REQUEST ['funcion'] == "codificarSelect") {

	$codificado['parentesco'] = $miFormulario->campoSeguro("parentesco_".$_REQUEST ['valor']);
	$codificado['genero'] = $miFormulario->campoSeguro("genero_familiar_".$_REQUEST ['valor']);
	$codificado['nivel_estudio'] = $miFormulario->campoSeguro("nivel_estudio_familiar_".$_REQUEST ['valor']);
	$codificado['pertenencia_etnica'] = $miFormulario->campoSeguro("pertenencia_etnica_familiar_".$_REQUEST ['valor']);
	$codificado['ocupacion'] = $miFormulario->campoSeguro("ocupacion_familiar_".$_REQUEST ['valor']);

	echo json_encode($codificado);

}

if ($_REQUEST ['funcion'] == "consultarCodigo") {

	$conexion = "interoperacion";
	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
	
	$cadenaSql = $this->sql->getCadenaSql ( 'codificacion', $_REQUEST['urba']);
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

	echo json_encode($resultado[0]);

}

if ($_REQUEST ['funcion'] == "cargarImagen") {
	
	$prefijo = substr(md5(uniqid(time())), 0, 6);
	
	$carpetaAdjunta= $this->miConfigurador->configuracion['raizDocumento'] . "/archivos/" . $prefijo ."/";
	$rutaUrlBloque = $this->miConfigurador->configuracion['host'] . $this->miConfigurador->configuracion['site'] . "/archivos/"  . $prefijo . "/";
	// Contar envían por el plugin

// 	if(isset($_REQUEST['ruta']) && $_REQUEST['ruta'] != ''){
// 		$carpetaAdjunta = $_REQUEST['ruta'];
// 	}else{
// 		mkdir($carpetaAdjunta, 0777);
// 	}
		
	mkdir($carpetaAdjunta, 0777);
	
	// El nombre y nombre temporal del archivo que vamos para adjuntar
	$nombreArchivo=isset($_FILES[$miFormulario->campoSeguro("foto")]['name'])?$_FILES[$miFormulario->campoSeguro("foto")]['name']:null;
	$nombreTemporal=isset($_FILES[$miFormulario->campoSeguro("foto")]['tmp_name'])?$_FILES[$miFormulario->campoSeguro("foto")]['tmp_name']:null;

	
	
	$nombreArchivo = str_replace(" ", "", $nombreArchivo);
	
	$nombreFinal = $prefijo . "-" . $nombreArchivo;
	$rutaFinal = $carpetaAdjunta;
	$urlFinal = $rutaUrlBloque;
	
	$rutaArchivo=$carpetaAdjunta . $nombreFinal;
	$rutaUrlArchivo = $rutaUrlBloque . $nombreFinal;

	$dir = $carpetaAdjunta;
	$handle = opendir($dir);
	$ficherosEliminados = 0;
	while ($file = readdir($handle)) {
		if (is_file($dir.$file)) {
			if (unlink($dir.$file) ){
				$ficherosEliminados++;
			}
		}
	}
	
	move_uploaded_file($nombreTemporal,$rutaArchivo);

	$infoImagenesSubidas=array("caption"=>"$nombreArchivo","height"=>"120px","url"=> $_REQUEST['eliminar'],"key"=>$nombreArchivo);
	$ImagenesSubidas="<img  height='120px'  src='$rutaUrlArchivo' class='file-preview-image'>";
	
	$arr = array("url"=>$urlFinal, "ruta"=>$rutaFinal, "nombre"=>$nombreFinal, "file_id"=>0,"overwriteInitial"=>true,"initialPreviewConfig"=>$infoImagenesSubidas,
			"initialPreview"=>$ImagenesSubidas);

	echo json_encode($arr);

}

if ($_REQUEST ['funcion'] == "eliminarImagen") {

		$carpetaAdjunta= $this->miConfigurador->configuracion['raizDocumento'] . "/archivos/imagenes/";
		
		parse_str(file_get_contents("php://input"),$datosDELETE);

		$key= $datosDELETE['key'];

		unlink($carpetaAdjunta.$key);
			
		echo 0;
	}else if($_REQUEST['funcion'] == 'consultarProyectos') {

		include_once "consultarProyectos.php";

	}
	
	if ($_REQUEST ['funcion'] == "actualizarCampo") {
	
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
	
		$cadenaSql = $this->sql->getCadenaSql ( 'actualizarCampo', $_REQUEST);
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "actualizar" );
	
		echo $resultado;
	
	
	}
	
	if ($_REQUEST ['funcion'] == "actualizarCampoUrb") {
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
	
		$cadenaSql = $this->sql->getCadenaSql ( 'actualizarCampoUrb', $_REQUEST);
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "actualizar" );
	
		echo $resultado;
	
	
	}

?>