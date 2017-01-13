<?php

namespace reportes\plantillaBeneficiario\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

$ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/plugin/html2pfd/";

require_once $ruta . "/plugin/PHPExcel/Classes/PHPExcel.php";

// require_once $ruta . "/plugin/PHPExcel/Classes/PHPExcel/Reader/Excel2007.php";

require_once $ruta . "/plugin/PHPExcel/Classes/PHPExcel/IOFactory.php";

include_once 'Redireccionador.php';
class FormProcessor {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $conexion;
	public $esteRecursoDB;
	public $rutaURL;
	public $rutaAbsoluta;
	public function __construct($lenguaje, $sql) {
		date_default_timezone_set ( 'America/Bogota' );
		
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		
		$this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		
		if (! isset ( $_REQUEST ["bloqueGrupo"] ) || $_REQUEST ["bloqueGrupo"] == "") {
			$this->rutaURL .= "/blocks/" . $_REQUEST ["bloque"] . "/";
			$this->rutaAbsoluta .= "/blocks/" . $_REQUEST ["bloque"] . "/";
		} else {
			$this->rutaURL .= "/blocks/" . $_REQUEST ["bloqueGrupo"] . "/" . $_REQUEST ["bloque"] . "/";
			$this->rutaAbsoluta .= "/blocks/" . $_REQUEST ["bloqueGrupo"] . "/" . $_REQUEST ["bloque"] . "/";
		}
		// Conexion a Base de Datos
		$conexion = "interoperacion";
		$this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$_REQUEST ['tiempo'] = time ();
		
		/**
		 * 1.
		 * Cargar Archivo en el Directorio
		 */
		
		$this->cargarArchivos ();
		
		/**
		 * 2.
		 * Cargar Informacion Hoja de Calculo
		 */
		
		$this->cargarInformacionHojaCalculo ();
		
		/**
		 * 3.
		 * Validar que no hayan nulos
		 */
		
		$this->validarNulo ();
		
		/**
		 * 5.
		 * Validar Existencia Beneficiarios
		 */
		
		if ($_REQUEST ['funcionalidad'] == 3) {
			$this->validarBeneficiariosExistentesRegistro ();
		} else {
			$this->validarBeneficiariosExistentes ();
		}
		
		/**
		 * 6.
		 * Procesar Información Beneficiarios
		 */
		
		$this->procesarInformacionBeneficiario ();
		
		/**
		 * 7.
		 * Actualizar o Registrar beneficiarios
		 */
		
		$this->informacionBeneficiario ();
		
		/**
		 * 9.
		 * Registrar Tarea o Proceso Masivo
		 */
		
		$this->registroProceso ();
		exit ();
		if (isset ( $this->proceso ) && $this->proceso != null) {
			Redireccionador::redireccionar ( "ExitoRegistroProceso", $this->proceso );
		} else {
			Redireccionador::redireccionar ( "ErrorRegistroProceso" );
		}
	}
	
	/**
	 * Funcionalidades Específicas
	 */
	public function registroProceso() {
		$arreglo_registro = array (
				'nombre_archivo' => $this->archivo ['ruta_archivo'] 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'registrarProceso', $arreglo_registro );
		$this->proceso = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0] ['id_proceso'];
	}
	public function informacionBeneficiario() {
		foreach ( $this->informacion_registrar as $key => $value ) {
			if ($_REQUEST ['funcionalidad'] == 3) {
				echo $cadenaSql = $this->miSql->getCadenaSql ( 'actualizarBeneficiario', $value );
				$resultado = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
			} else {
				$cadenaSql = $this->miSql->getCadenaSql ( 'registrarBeneficiarioPotencial', $value );
				$resultado = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
			}
			
	
			if ($resultado != true) {
				Redireccionador::redireccionar ( "ErrorActualizacion" );
			}
		}

	}
	public function validarNulo() {
		foreach ( $this->datos_beneficiario as $key => $value ) {
			
			if ($value ['estrato'] == 0) {
				Redireccionador::redireccionar ( "ErrorCreacionContratos" );
				exit();
			}
			
			if (is_null ( $value ['identificacion_beneficiario'] )) {
				Redireccionador::redireccionar ( "ErrorCreacionContratos" );
				exit();
			}
		}
	}
	public function procesarInformacionBeneficiario() {
		foreach ( $this->datos_beneficiario as $key => $value ) {
			
			// Funcionalidad 3 es Actualización de Registros
			if ($_REQUEST ['funcionalidad'] == 3) {
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarInformacionBeneficiario', $value ['identificacion_beneficiario'] );
				$consulta = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
				
				$this->informacion_registrar [] = array (
						'id_beneficiario' => $consulta ['id_beneficiario'],
						'identificacion_beneficiario' => $value ['identificacion_beneficiario'],
						'tipo_beneficiario' => $value ['tipo_beneficiario'],
						'tipo_documento' => $value ['tipo_documento'],
						'nomenclatura' => $consulta ['nomenclatura'],
						'nombre_beneficiario' => $value ['nombre'],
						'primer_apellido' => $value ['primer_apellido'],
						'segundo_apellido' => $value ['segundo_apellido'],
						'genero_beneficiario' => $value ['genero'],
						'edad_beneficiario' => $value ['edad'],
						'nivel_estudio' => $value ['nivel_estudio'],
						'correo' => $value ['correo'],
						'direccion' => $value ['direccion'],
						'manzana' => $value ['manzana'],
						'torre' => $value ['torre'],
						'bloque' => $value ['bloque'],
						'interior' => $value ['interior'],
						'lote' => $value ['lote'],
						'apartamento' => $value ['casa_apto'],
						'telefono' => $value ['telefono'],
						'departamento' => $value ['departamento'],
						'municipio' => $value ['municipio'],
						'piso' => $value ['piso'],
						'minvi' => $value ['minvivienda'],
						'barrio' => $value ['barrio'],
						'id_proyecto' => $value ['id_proyecto'],
						'proyecto' => $value ['proyecto'],
						'estrato' => $value ['estrato'] 
				);
			} else {
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'codificacion', $value ['id_proyecto'] );
				$resultado = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				if ($resultado) {
					$_REQUEST ['consecutivo'] = $resultado [0] ['abr_benf'];
					$_REQUEST ['abr_urb'] = $resultado [0] ['abr_urb'];
					$_REQUEST ['abr_mun'] = $resultado [0] ['abr_mun'];
				} else {
					$_REQUEST ['consecutivo'] = "ND";
					$_REQUEST ['abr_urb'] = "ND";
					$_REQUEST ['abr_mun'] = "ND";
				}
				
				$numeroCaracteres = 5;
				$numeroBusqueda = strlen ( $_REQUEST ['consecutivo'] );
				
				$valor ['string'] = $_REQUEST ['consecutivo'];
				$valor ['longitud'] = $numeroCaracteres - $numeroBusqueda - 1;
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarConsecutivo', $valor );
				$resultado = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				if ($resultado) {
					$consecutivo = explode ( $_REQUEST ['consecutivo'], $resultado [0] ['id_beneficiario'] );
					$nuevoConsecutivo = $consecutivo [1] + 1;
					
					if (strlen ( $_REQUEST ['consecutivo'] ) == 1) {
						if ($nuevoConsecutivo < 10) {
							$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '000' . $nuevoConsecutivo;
						} else if ($nuevoConsecutivo < 100) {
							$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '00' . $nuevoConsecutivo;
						} else if ($nuevoConsecutivo < 1000) {
							$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '0' . $nuevoConsecutivo;
						} else {
							$nuevoConsecutivo = $_REQUEST ['consecutivo'] . $nuevoConsecutivo;
						}
					} else if (strlen ( $_REQUEST ['consecutivo'] ) == 2) {
						if ($nuevoConsecutivo < 10) {
							$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '00' . $nuevoConsecutivo;
						} else if ($nuevoConsecutivo < 100) {
							$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '0' . $nuevoConsecutivo;
						} else {
							$nuevoConsecutivo = $_REQUEST ['consecutivo'] . $nuevoConsecutivo;
						}
					} else if (strlen ( $_REQUEST ['consecutivo'] ) == 3) {
						if ($nuevoConsecutivo < 10) {
							$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '0' . $nuevoConsecutivo;
						} else if ($nuevoConsecutivo < 100) {
							$nuevoConsecutivo = $_REQUEST ['consecutivo'] . $nuevoConsecutivo;
						} else {
							$nuevoConsecutivo = $_REQUEST ['consecutivo'] . $nuevoConsecutivo;
						}
					} else {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . $nuevoConsecutivo;
					}
					
					$beneficiarioPotencial ['id_beneficiario'] = $nuevoConsecutivo;
				} else {
					if (strlen ( $_REQUEST ['consecutivo'] ) == 1) {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '0001';
					} else if (strlen ( $_REQUEST ['consecutivo'] ) == 2) {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '001';
					} else if (strlen ( $_REQUEST ['consecutivo'] ) == 3) {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '01';
					} else if (strlen ( $_REQUEST ['consecutivo'] ) == 4) {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '1';
					}
					
					$beneficiarioPotencial ['id_beneficiario'] = $nuevoConsecutivo;
				}
				
				$this->informacion_registrar [] = array (
						'id_beneficiario' => $beneficiarioPotencial ['id_beneficiario'],
						'tipo_beneficiario' => $value ['tipo_beneficiario'],
						'tipo_documento' => $value ['tipo_documento'],
						'identificacion_beneficiario' => $value ['identificacion_beneficiario'],
						'nombre_beneficiario' => $value ['nombre'],
						'primer_apellido' => $value ['primer_apellido'],
						'segundo_apellido' => $value ['segundo_apellido'],
						'genero_beneficiario' => $value ['genero'],
						'edad_beneficiario' => $value ['edad'],
						'nivel_estudio' => $value ['nivel_estudio'],
						'correo' => $value ['correo'],
						'direccion' => $value ['direccion'],
						'manzana' => $value ['manzana'],
						'interior' => $value ['interior'],
						'bloque' => $value ['bloque'],
						'torre' => $value ['torre'],
						'apartamento' => $value ['casa_apto'],
						'lote' => $value ['lote'],
						'telefono' => $value ['telefono'],
						'departamento' => $value ['departamento'],
						'municipio' => $value ['municipio'],
						'estrato' => $value ['estrato'],
						'id_proyecto' => $value ['id_proyecto'],
						'proyecto' => $value ['proyecto'],
						'piso' => $value ['piso'],
						'minvi' => $value ['minvivienda'],
						'barrio' => $value ['barrio'],
						'nomenclatura' => $_REQUEST ['abr_mun'] . "_" . $_REQUEST ['abr_urb'] . "_" . $value ['identificacion_beneficiario'] 
				);
			}
		}
	}
	public function validarBeneficiariosExistentes() {
		foreach ( $this->datos_beneficiario as $key => $value ) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarExitenciaBeneficiario', $value ['identificacion_beneficiario'] );
			
			$consulta = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
			
			if (is_null ( $consulta )) {
				$this->error = true;
			}
		}
	}
	public function validarBeneficiariosExistentesRegistro() {
		foreach ( $this->datos_beneficiario as $key => $value ) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarExitenciaBeneficiario', $value ['identificacion_beneficiario'] );
			$consulta = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
			
			if (! is_null ( $consulta )) {
				$this->error = true;
			}
		}
	}
	public function cargarInformacionHojaCalculo() {
		ini_set ( 'memory_limit', '1024M' );
		ini_set ( 'max_execution_time', 300 );
		
		if (file_exists ( $this->archivo ['ruta_archivo'] )) {
			
			$hojaCalculo = \PHPExcel_IOFactory::createReader ( $this->tipo_archivo );
			$informacion = $hojaCalculo->load ( $this->archivo ['ruta_archivo'] );
			
			$informacion_general = $hojaCalculo->listWorksheetInfo ( $this->archivo ['ruta_archivo'] );
			
			{
				$total_filas = $informacion_general [0] ['totalRows'];
			}
			
			if ($total_filas > 501) {
				Redireccionador::redireccionar ( "ErrorNoCargaInformacionHojaCalculo" );
			}
			
			for($i = 2; $i <= $total_filas; $i ++) {
				
				$datos_beneficiario [$i] ['departamento'] = $informacion->setActiveSheetIndex ()->getCell ( 'A' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['municipio'] = $informacion->setActiveSheetIndex ()->getCell ( 'B' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['id_proyecto'] = $informacion->setActiveSheetIndex ()->getCell ( 'C' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['proyecto'] = $informacion->setActiveSheetIndex ()->getCell ( 'D' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['tipo_beneficiario'] = $informacion->setActiveSheetIndex ()->getCell ( 'E' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['tipo_documento'] = $informacion->setActiveSheetIndex ()->getCell ( 'F' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['identificacion_beneficiario'] = $informacion->setActiveSheetIndex ()->getCell ( 'G' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['nombre'] = $informacion->setActiveSheetIndex ()->getCell ( 'H' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['primer_apellido'] = $informacion->setActiveSheetIndex ()->getCell ( 'I' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['segundo_apellido'] = $informacion->setActiveSheetIndex ()->getCell ( 'J' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['genero'] = (! is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'K' . $i )->getCalculatedValue () )) ? $informacion->setActiveSheetIndex ()->getCell ( 'K' . $i )->getCalculatedValue () : 0;
				
				$datos_beneficiario [$i] ['edad'] = (! is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'L' . $i )->getCalculatedValue () )) ? $informacion->setActiveSheetIndex ()->getCell ( 'L' . $i )->getCalculatedValue () : 0;
				
				$datos_beneficiario [$i] ['nivel_estudio'] = (! is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'M' . $i )->getCalculatedValue () )) ? $informacion->setActiveSheetIndex ()->getCell ( 'M' . $i )->getCalculatedValue () : 0;
				
				$datos_beneficiario [$i] ['correo'] = $informacion->setActiveSheetIndex ()->getCell ( 'N' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['telefono'] = (! is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'O' . $i )->getCalculatedValue () )) ? $informacion->setActiveSheetIndex ()->getCell ( 'O' . $i )->getCalculatedValue () : 0;
				
				$datos_beneficiario [$i] ['direccion'] = $informacion->setActiveSheetIndex ()->getCell ( 'P' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['manzana'] = $informacion->setActiveSheetIndex ()->getCell ( 'Q' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['bloque'] = $informacion->setActiveSheetIndex ()->getCell ( 'R' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['torre'] = $informacion->setActiveSheetIndex ()->getCell ( 'S' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['casa_apto'] = $informacion->setActiveSheetIndex ()->getCell ( 'T' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['interior'] = $informacion->setActiveSheetIndex ()->getCell ( 'U' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['lote'] = $informacion->setActiveSheetIndex ()->getCell ( 'V' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['piso'] = (! is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'W' . $i )->getCalculatedValue () )) ? $informacion->setActiveSheetIndex ()->getCell ( 'W' . $i )->getCalculatedValue () : 0;
				
				$datos_beneficiario [$i] ['minvivienda'] = (! is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'X' . $i )->getCalculatedValue () )) ? $informacion->setActiveSheetIndex ()->getCell ( 'X' . $i )->getCalculatedValue () : 'FALSE';
				
				$datos_beneficiario [$i] ['barrio'] = $informacion->setActiveSheetIndex ()->getCell ( 'Y' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['estrato'] = (! is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'Z' . $i )->getCalculatedValue () )) ? $informacion->setActiveSheetIndex ()->getCell ( 'Z' . $i )->getCalculatedValue () : 0;
			}
			
			$this->datos_beneficiario = $datos_beneficiario;
			
			unlink ( $this->archivo ['ruta_archivo'] );
		} else {
			Redireccionador::redireccionar ( "ErrorNoCargaInformacionHojaCalculo" );
		}
	}
	public function cargarArchivos() {
		$archivo_datos = '';
		$archivo = $_FILES ['archivo_informacion'];
		
		if ($archivo ['error'] == 0) {
			
			switch ($archivo ['type']) {
				case 'application/vnd.oasis.opendocument.spreadsheet' :
					$this->tipo_archivo = 'OOCalc';
					break;
				
				case 'application/vnd.ms-excel' :
					$this->tipo_archivo = 'Excel5';
					break;
				
				case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' :
					$this->tipo_archivo = 'Excel2007';
					break;
				
				default :
					Redireccionador::redireccionar ( "ErrorFormatoArchivo" );
					break;
			}
			
			$this->prefijo = substr ( md5 ( uniqid ( time () ) ), 0, 6 );
			/*
			 * obtenemos los datos del Fichero
			 */
			$tamano = $archivo ['size'];
			$tipo = $archivo ['type'];
			$nombre_archivo = str_replace ( " ", "_", $archivo ['name'] );
			/*
			 * guardamos el fichero en el Directorio
			 */
			$ruta_absoluta = $this->rutaAbsoluta . "/entidad/archivos_validar/" . $this->prefijo . "_" . $nombre_archivo;
			
			$ruta_relativa = $this->rutaURL . " /entidad/archivos_validar/" . $this->prefijo . "_" . $nombre_archivo;
			
			$archivo ['rutaDirectorio'] = $ruta_absoluta;
			
			if (! copy ( $archivo ['tmp_name'], $ruta_absoluta )) {
				
				Redireccionador::redireccionar ( "ErrorCargarArchivo" );
			}
			
			$this->archivo = array (
					'ruta_archivo' => str_replace ( "//", "/", $ruta_absoluta ),
					'nombre_archivo' => $archivo ['name'] 
			);
		} else {
			Redireccionador::redireccionar ( "ErrorArchivoNoValido" );
		}
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );
?>

