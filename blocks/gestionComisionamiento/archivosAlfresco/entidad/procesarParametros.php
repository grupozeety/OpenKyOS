<?php

namespace gestionComisionamiento\archivosAlfresco\entidad;

include_once 'RestClient.class.php';
include_once 'Redireccionador.php';

class FormProcessor {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public function __construct($lenguaje, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
	}
	public function procesarAlfresco() {
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'masivosArchivos', '' );
		$informacion = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
	
		foreach ( $informacion as $key => $values ) {
			$beneficiario = $informacion[$key]['id_beneficiario'];
			
			$documento = array (
					'rutaabsoluta' => $informacion[$key]['rutaabsoluta'],
					'nombre_archivo' => $informacion[$key]['nombre_archivo'] 
			);
			
			$filename = $documento ['rutaabsoluta'];
			$mimetype = mime_content_type ( $filename );
			$postname = $documento ['nombre_archivo'];
		
			$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoDirectorio', '' );
			$directorio = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoUser', $beneficiario );
			$variable = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoLog', $beneficiario );
			$datosConexion = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			$url = "http://" . $datosConexion [0] ['host'] . "/alfresco/service/api/upload";
			
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
					'uploaddirectory' => "/" . $directorio [0] [0] . "/" . $variable [0] ['padre'] . "/" . $variable [0] ['hijo'] . "/" . $beneficiario,
					'contenttype' => 'cm:content' 
			);

			$result = RestClient::post ( $url, $archivo, $datosConexion [0] ['usuario'], $datosConexion [0] ['password'] );
			$json_decode = json_decode ( json_encode ( $result->getResponse () ), true );

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
			
			echo $beneficiario."=".$estado['estado']."<br>";

		}
		exit;
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarAlfresco ();

?>