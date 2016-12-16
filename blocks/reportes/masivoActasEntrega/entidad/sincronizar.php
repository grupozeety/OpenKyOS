<?php

namespace reportes\masivoActas\entidad;

include_once ('RestClient.class.php');

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("index.php");
	exit ();
}
class Sincronizar {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miFuncion;
	var $miSql;
	var $conexion;
	function __construct($lenguaje, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
	}
	public function sincronizarAlfresco($beneficiario, $documento) {
		$_REQUEST ['tiempo'] = time ();
		$this->prefijo = substr ( md5 ( uniqid ( time () ) ), 0, 6 );
		$ruta_absoluta = $documento ['rutaabsoluta'];
		$ejecutar = 'sudo chmod 755 ' . $ruta_absoluta;
		exec ( $ejecutar );
		chmod ( $ruta_absoluta, 0755 );
		
		$filename = $ruta_absoluta;
		$mimetype = mime_content_type ( $filename );
		$postname = $documento ['nombre_archivo'];
		
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		switch ($documento ['tipo_documento']) {
			case '131' :
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCarpetaSoportes', "1" );
				break;
			case '132' :
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCarpetaSoportes', "1" );
				break;
			case '135' :
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCarpetaSoportes', "2" );
				break;
			case '137' :
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCarpetaSoportes', "3" );
				break;
			case '140' :
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCarpetaSoportes', "3" );
				break;
			case '141' :
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCarpetaSoportes', "4" );
				break;
			case '142' :
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCarpetaSoportes', "4" );
				break;
			case '133' :
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCarpetaSoportes', "2" );
				break;
			case '134' :
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCarpetaSoportes', "2" );
				break;
			case '138' :
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCarpetaSoportes', "3" );
				break;
			case '139' :
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCarpetaSoportes', "3" );
				break;
			case '136' :
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCarpetaSoportes', "2" );
				break;
		}
		$carpetaDocumentos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoDirectorio', '' );
		$directorio = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoUser', $beneficiario );
		$variable = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoLog', $beneficiario );
		$datosConexion = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$url = "http://" . $datosConexion [0] ['host'] . "/alfresco/service/api/upload";
		
		var_dump($url);
		if (! function_exists ( 'curl_file_create' )) {
			$args = "@$filename;filename=" . ($postname ?: basename ( $filename )) . ($mimetype ? ";type=$mimetype" : '');
		} else {
			$args = curl_file_create ( $filename, $mimetype, $postname );
		}
		
		$unwanted_array = array (
				'é' => '%C3%A9',
				'í' => '%C3%AD',
				'ó' => '%C3%B3',
				' ' => '%20',
				'(' => '%28',
				')' => '%29' 
		);
		
		$archivo = array (
				'filedata' => $args,
				'siteid' => $variable [0] ['site'],
				'containerid' => 'documentLibrary',
				'uploaddirectory' => "/" . $directorio [0] [0] . "/" . $variable [0] ['padre'] . "/" . $variable [0] ['hijo'] . "/" . $beneficiario . "/" . $carpetaDocumentos [0] ['descripcion'],
				'contenttype' => 'cm:content' 
		);
		
		$result = RestClient::post ( $url, $archivo, $datosConexion [0] ['usuario'], $datosConexion [0] ['password'] );
		$json_decode = json_decode ( json_encode ( $result->getResponse () ), true );
		
		var_dump($result); exit;
		$status = json_decode ( $json_decode, true );
		
		if ($status ['status'] ['code'] == 200) {
			
			$estado = array (
					'estado' => 0,
					'mensaje' => "Documento subido exitosamente en el Gestor de Documentos" 
			);
		} else {
			$estado = array (
					'estado' => 1,
					'mensaje' => "Error en la subida de documento." 
			);
		}
		
	
		return $estado;
	}
}

?>
