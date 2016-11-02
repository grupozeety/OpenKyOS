<?php

namespace gestionBeneficiarios\generacionContrato\entidad;

class procesarAjax {
	public $miConfigurador;
	public $sql;
	public function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
		
		$this->sql = $sql;
		
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		switch ($_REQUEST ['funcion']) {
			
			case 'consultaBeneficiarios' :
				
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
				
				break;
			
			case 'cargarImagen' :
				
				$prefijo = substr ( md5 ( uniqid ( time () ) ), 0, 6 );
				
				$carpetaAdjunta = $this->miConfigurador->configuracion ['raizDocumento'] . "/archivos/" . $prefijo . "/";
				$rutaUrlBloque = $this->miConfigurador->configuracion ['host'] . $this->miConfigurador->configuracion ['site'] . "/archivos/" . $prefijo . "/";
				
				mkdir ( $carpetaAdjunta, 0777 );
				
				// El nombre y nombre temporal del archivo que vamos para adjuntar
				$nombreArchivo = isset ( $_FILES [$miFormulario->campoSeguro ( "archivo" )] ['name'] ) ? $_FILES [$miFormulario->campoSeguro ( "archivo" )] ['name'] : null;
				$nombreTemporal = isset ( $_FILES [$miFormulario->campoSeguro ( "archivo" )] ['tmp_name'] ) ? $_FILES [$miFormulario->campoSeguro ( "archivo" )] ['tmp_name'] : null;
				
				$nombreArchivo = str_replace ( " ", "", $nombreArchivo );
				
				$nombreFinal = $prefijo . "-" . $nombreArchivo;
				$rutaFinal = $carpetaAdjunta;
				$urlFinal = $rutaUrlBloque;
				
				$rutaArchivo = $carpetaAdjunta . $nombreFinal;
				$rutaUrlArchivo = $rutaUrlBloque . $nombreFinal;
				
				$dir = $carpetaAdjunta;
				$handle = opendir ( $dir );
				$ficherosEliminados = 0;
				while ( $file = readdir ( $handle ) ) {
					if (is_file ( $dir . $file )) {
						if (unlink ( $dir . $file )) {
							$ficherosEliminados ++;
						}
					}
				}
				
				move_uploaded_file ( $nombreTemporal, $rutaArchivo );
				
				$infoImagenesSubidas = array (
						"caption" => "$nombreArchivo",
						"height" => "120px",
						"url" => $_REQUEST ['eliminar'],
						"key" => $nombreArchivo 
				);
				$ImagenesSubidas = "<img  height='120px'  src='$rutaUrlArchivo' class='file-preview-image'>";
				
				$arr = array (
						"url" => $urlFinal,
						"ruta" => $rutaFinal,
						"nombre" => $nombreFinal,
						"file_id" => 0,
						"overwriteInitial" => true,
						"initialPreviewConfig" => $infoImagenesSubidas,
						"initialPreview" => $ImagenesSubidas 
				);
				
				echo json_encode ( $arr );
				
				break;
		}
		
		exit ();
	}
}

$miProcesarAjax = new procesarAjax ( $this->sql );

?>
