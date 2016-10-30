<?php

namespace gestionComisionamiento\archivosAlfresco\entidad;

include_once ('RestClient.class.php');
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
	public function procesarFormulario() {
		$_REQUEST ['tiempo'] = time ();
	        foreach ($_FILES as $key => $archivo) {

            $this->prefijo = substr(md5(uniqid(time())), 0, 6);
            /*
             * obtenemos los datos del Fichero
             */
            $tamano = $archivo['size'];
            $tipo = $archivo['type'];
            $nombre_archivo = str_replace(" ", "", $archivo['name']);
            /*
             * guardamos el fichero en el Directorio
             */
            $ruta_absoluta = $this->miConfigurador->configuracion['raizDocumento'] . "/archivos/" . $this->prefijo . "_" . $nombre_archivo;
            $ruta_relativa = $this->miConfigurador->configuracion['host'] . $this->miConfigurador->configuracion['site'] . "/archivos/" . $this->prefijo . "_" . $nombre_archivo;
            $archivo['rutaDirectorio'] = $ruta_absoluta;
            if (!copy($archivo['tmp_name'], $ruta_absoluta)) {
                exit;
                echo "error copiando";
            }

            $archivo_datos = array(
                'ruta_relativa' => $ruta_relativa,
                'nombre_archivo' => $archivo['name'],
                'ruta_absoluta' => $ruta_absoluta,
            	'type'=>$archivo['type'],
            );
        }
		
        var_dump($archivo_datos);
		$args = new \CURLFile ( $archivo_datos ['ruta_absoluta'], $archivo_datos['type'], $archivo_datos['nombre_archivo'] );
		// curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
		// $fp=fopen($archivo,'r');
	//	var_dump ( $args );
		
		$beneficiario = '4444';
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoDirectorio', '' );
		$directorio = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoUser', $beneficiario );
		$variable = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoLog', $beneficiario );
		$datosConexion = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$url = "http://" . $datosConexion [0] ['host'] . "/alfresco/service/api/site/folder/" . $variable [0] ['site'] . "/documentLibrary/" . $directorio [0] [0] . "/" . $variable [0] ['padre'] . "/" . $variable [0] ['hijo']; // pendiente la pagina para modificar parametro
		
		$archivo = array (
				'filedata' => json_decode(json_encode($args),true),
				'filename' => json_decode(json_encode($args),true),
				'siteid' => $variable [0] ['site'],
				'containerid' => 'documentLibrary',
				'uploaddirectory' => "/" . $directorio [0] [0] . "/" . $variable [0] ['padre'] . "/" . $variable [0] ['hijo'],
				'contenttype' => 'cm:content' 
		);
		
		$result = RestClient::post ( $url, $archivo, $datosConexion [0] ['usuario'], $datosConexion [0] ['password'] );
		var_dump ( $result );
		$json_decode = json_decode ( json_encode ( $result->getResponse () ), true );
		
		var_dump ( $json_decode );
		exit ();
		$validacion = strpos ( $json_decode, 'error' );
		if (! is_numeric ( $validacion )) {
			
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
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

?>

