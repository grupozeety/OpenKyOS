<?php

namespace reportes\plantillaInfoTecnica\entidad;

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
		 * 4.
		 * Validar Existencia Beneficiarios
		 */
		
		if ($_REQUEST ['funcionalidad'] == 2) {
			$this->validarInfoExistentesRegistro ();
		} else {
			$this->validarInfoExistentes ();
		}
		
		/**
		 * 5.
		 * Procesar Información
		 */
		
		$this->procesarInformacion ();
		
		/**
		 * 6.
		 * Procesar Cabecera
		 */
		
		$this->procesarCabecera ();
		/**
		 * 7.
		 * Actualizar o Registrar beneficiarios
		 */
		
		$this->informacion ();
	}
	
	/**
	 * Funcionalidades Específicas
	 */
	public function informacion() {
		foreach ( $this->informacion_registrar as $key => $value ) {
			if ($_REQUEST ['funcionalidad'] == 3) {
				$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarInformacion', $value );
				$this->resultado = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
			} else {
				$cadenaSql = $this->miSql->getCadenaSql ( 'registrarNodo', $value );
				$this->resultado = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
			}
		}
		if ($this->resultado != true) {
			Redireccionador::redireccionar ( "ErrorActualizacion" );
			exit ();
		} else {
			Redireccionador::redireccionar ( "ExitoRegistroProceso" );
			exit ();
		}
	}
	public function procesarCabecera() {
		foreach ( $this->info_cabecera as $key => $value ) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCabecera', $value ['codigo_cabecera'] );
			$consulta = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			if ($consulta == false) {
				$cadenaSql = $this->miSql->getCadenaSql ( 'registrarCabecera', $value );
				$consulta = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
				
				if ($consulta != true) {
					Redireccionador::redireccionar ( "ErrorActualizacioncabecera" );
					exit ();
				}
			}
		}
	}
	public function procesarInformacion() {
		$a = 0;
		foreach ( $this->datos_infotecnica as $key => $value ) {
			
			$cabecera [] = array (
					'codigo_nodo' => $value ['codigo_nodo'],
					'codigo_cabecera' => $value ['codigo_cabecera'],
					'departamento' => $value ['departamento'],
					'municipio' => $value ['municipio'],
					'urbanizacion' => $value ['proyecto'],
					'id_urbanizacion' => $value ['id_proyecto'] 
			);
			
			// wMAN
			if ($_REQUEST ['tecnologia'] == 2) {
				
				$this->informacion_registrar [] = array (
						'codigo_nodo' => $value ['codigo_nodo'],
						'codigo_cabecera' => $value ['codigo_cabecera'],
						'departamento' => $value ['departamento'],
						'municipio' => $value ['municipio'],
						'urbanizacion' => $value ['proyecto'],
						'id_urbanizacion' => $value ['id_proyecto'],
						'tipo_tecnologia' => $value ['tipo_tecnologia'],
						'mac_master_eoc' => 'N/A',
						'ip_master_eoc' => 'N/A',
						'mac_onu_eoc' => 'N/A',
						'ip_onu_eoc' => 'N/A',
						'mac_hub_eoc' => 'N/A',
						'ip_hub_eoc' => 'N/A',
						'mac_cpe_eoc' => 'N/A',
						'mac_celda' => $value ['wman_maccelda'],
						'ip_celda' => $value ['wman_ipcelda'],
						'nombre_nodo' => $value ['wman_nombrenodo'],
						'nombre_sectorial' => $value ['wman_nombresectorial'],
						'ip_switch_celda' => $value ['wman_ipswitchcelda'],
						'mac_sm_celda' => $value ['wman_macsmcelda'],
						'ip_sm_celda' => $value ['wman_ipsmcelda'],
						'mac_cpe_celda' => $value ['wman_maccpecelda'],
						'latitud' => $value ['latitud'],
						'longitud' => $value ['longitud'],
						'macesclavo1' => $value ['macesclavo1'],
						'port_olt' => $value ['port'] 
				);
			} elseif ($_REQUEST ['tecnologia'] == 1) {
				$this->informacion_registrar [] = array (
						'codigo_nodo' => $value ['codigo_nodo'],
						'codigo_cabecera' => $value ['codigo_cabecera'],
						'departamento' => $value ['departamento'],
						'municipio' => $value ['municipio'],
						'urbanizacion' => $value ['proyecto'],
						'id_urbanizacion' => $value ['id_proyecto'],
						'tipo_tecnologia' => $value ['tipo_tecnologia'],
						'mac_master_eoc' => $value ['hfc_macmaster'],
						'ip_master_eoc' => $value ['hfc_ipmaster'],
						'mac_onu_eoc' => $value ['hfc_maconu'],
						'ip_onu_eoc' => $value ['hfc_iponu'],
						'mac_hub_eoc' => $value ['hfc_machub'],
						'ip_hub_eoc' => $value ['hfc_iphub'],
						'mac_cpe_eoc' => $value ['hfc_maccpe'],
						'mac_celda' => 'N/A',
						'ip_celda' => 'N/A',
						'nombre_nodo' => 'N/A',
						'nombre_sectorial' => 'N/A',
						'ip_switch_celda' => 'N/A',
						'mac_sm_celda' => 'N/A',
						'ip_sm_celda' => 'N/A',
						'mac_cpe_celda' => 'N/A',
						'latitud' => $value ['latitud'],
						'longitud' => $value ['longitud'],
						'macesclavo1' => $value ['macesclavo1'],
						'port_olt' => $value ['port'] 
				);
			}
		}
		
		$this->info_cabecera = $this->unique_multidim_array ( $cabecera, 'codigo_cabecera' );
	}
	public function validarInfoExistentes() {
		foreach ( $this->datos_infotecnica as $key => $value ) {
			
			if ($_REQUEST ['tecnologia'] == 1) {
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarExistenciaInfoHFC', $value );
				$consulta = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
				
				if (is_null ( $consulta )) {
					Redireccionador::redireccionar ( "ErrorCreacionContratos" );
					exit ();
				}
			} elseif ($_REQUEST ['tecnologia'] == 2) {
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarExistenciaInfoWMAN', $value );
				$consulta = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
				if (is_null ( $consulta )) {
					Redireccionador::redireccionar ( "ErrorCreacionContratos" );
					exit ();
				}
			} else {
				Redireccionador::redireccionar ( "ErrorCreacionContratos" );
				exit ();
			}
		}
	}
	public function validarInfoExistentesRegistro() {
		foreach ( $this->datos_infotecnica as $key => $value ) {
			if ($_REQUEST ['tecnologia'] == 1) {
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarExistenciaInfoHFC', $value );
				$consulta = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
				
				if (! is_null ( $consulta )) {
					Redireccionador::redireccionar ( "ErrorCreacionContratos" );
					exit ();
				}
			} elseif ($_REQUEST ['tecnologia'] == 2) {
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarExistenciaInfoWMAN', $value );
				$consulta = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
				if (! is_null ( $consulta )) {
					Redireccionador::redireccionar ( "ErrorCreacionContratos" );
					exit ();
				}
			} else {
				Redireccionador::redireccionar ( "ErrorCreacionContratos" );
				exit ();
			}
		}
	}
	public function validarNulo() {
		foreach ( $this->datos_infotecnica as $key => $value ) {
			// wMAN
			
			if (is_null ( $value ['id_proyecto'] ) || is_null ( $value ['proyecto'] )) {
				Redireccionador::redireccionar ( "ErrorCreacionContratos" );
				exit ();
			}
			
			if (is_null ( $value ['codigo_cabecera'] )) {
				Redireccionador::redireccionar ( "ErrorCreacionContratos" );
				exit ();
			}
			
			if (is_null ( $value ['codigo_nodo'] )) {
				Redireccionador::redireccionar ( "ErrorCreacionContratos" );
				exit ();
			}
			
			if (is_null ( $value ['tipo_tecnologia'] )) {
				Redireccionador::redireccionar ( "ErrorCreacionContratos" );
				exit ();
			} else {
				
				if ($_REQUEST ['tecnologia'] == 1) {
					if (is_null ( $value ['hfc_macmaster'] )) {
						Redireccionador::redireccionar ( "ErrorCreacionContratos" );
						exit ();
					}
					if (is_null ( $value ['hfc_ipmaster'] )) {
						Redireccionador::redireccionar ( "ErrorCreacionContratos" );
						exit ();
					}
					
					if (is_null ( $value ['hfc_maconu'] )) {
						Redireccionador::redireccionar ( "ErrorCreacionContratos" );
						exit ();
					}
					
					if (is_null ( $value ['hfc_iponu'] )) {
						Redireccionador::redireccionar ( "ErrorCreacionContratos" );
						exit ();
					}
					
					if ($value ['macesclavo1'] === 0) {
						Redireccionador::redireccionar ( "ErrorCreacionContratos" );
						exit ();
					}
				} elseif ($_REQUEST ['tecnologia'] == 2) {
					if (is_null ( $value ['wman_maccelda'] )) {
						Redireccionador::redireccionar ( "ErrorCreacionContratos" );
						exit ();
					}
					
					if (is_null ( $value ['wman_ipcelda'] )) {
						Redireccionador::redireccionar ( "ErrorCreacionContratos" );
						exit ();
					}
					
					if (is_null ( $value ['wman_nombresectorial'] )) {
						Redireccionador::redireccionar ( "ErrorCreacionContratos" );
						exit ();
					}
					
					if (is_null ( $value ['wman_ipsmcelda'] )) {
						Redireccionador::redireccionar ( "ErrorCreacionContratos" );
						exit ();
					}
					
					if (is_null ( $value ['wman_maccpecelda'] )) {
						Redireccionador::redireccionar ( "ErrorCreacionContratos" );
						exit ();
					}
					
					if ($value ['macesclavo1'] === 0) {
						Redireccionador::redireccionar ( "ErrorCreacionContratos" );
						exit ();
					}
				}
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
				$datos_beneficiario [$i] ['codigo_nodo'] = $informacion->setActiveSheetIndex ()->getCell ( 'A' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['codigo_cabecera'] = $informacion->setActiveSheetIndex ()->getCell ( 'B' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['departamento'] = $informacion->setActiveSheetIndex ()->getCell ( 'C' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['municipio'] = $informacion->setActiveSheetIndex ()->getCell ( 'D' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['proyecto'] = $informacion->setActiveSheetIndex ()->getCell ( 'E' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['id_proyecto'] = $informacion->setActiveSheetIndex ()->getCell ( 'F' . $i )->getCalculatedValue ();
				
				$datos_beneficiario [$i] ['latitud'] = (is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'G' . $i )->getCalculatedValue () )) ? 0 : str_replace ( "'", "`", $informacion->setActiveSheetIndex ()->getCell ( 'G' . $i )->getCalculatedValue () );
				
				$datos_beneficiario [$i] ['longitud'] = (is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'H' . $i )->getCalculatedValue () )) ? 0 : str_replace ( "'", "`", $informacion->setActiveSheetIndex ()->getCell ( 'H' . $i )->getCalculatedValue () );
				
				$datos_beneficiario [$i] ['macesclavo1'] = (is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'I' . $i )->getCalculatedValue () )) ? 0 : strtolower ( str_replace ( ':', '', $informacion->setActiveSheetIndex ()->getCell ( 'I' . $i )->getCalculatedValue () ) );
				
				$datos_beneficiario [$i] ['port'] = (! is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'J' . $i )->getCalculatedValue () )) ? $informacion->setActiveSheetIndex ()->getCell ( 'J' . $i )->getCalculatedValue () : 0;
				
				if ($_REQUEST ['tecnologia'] == 1) {
					
					$datos_beneficiario [$i] ['tipo_tecnologia'] = '95';
					
					$datos_beneficiario [$i] ['hfc_macmaster'] = str_replace ( ':', '', $informacion->setActiveSheetIndex ()->getCell ( 'K' . $i )->getCalculatedValue () );
					
					$datos_beneficiario [$i] ['hfc_ipmaster'] = $informacion->setActiveSheetIndex ()->getCell ( 'L' . $i )->getCalculatedValue ();
					
					$datos_beneficiario [$i] ['hfc_maconu'] = str_replace ( ':', '', $informacion->setActiveSheetIndex ()->getCell ( 'M' . $i )->getCalculatedValue () );
					
					$datos_beneficiario [$i] ['hfc_iponu'] = (! is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'N' . $i )->getCalculatedValue () )) ? $informacion->setActiveSheetIndex ()->getCell ( 'N' . $i )->getCalculatedValue () : 0;
					
					$datos_beneficiario [$i] ['hfc_machub'] = (! is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'O' . $i )->getCalculatedValue () )) ? str_replace ( ':', '', $informacion->setActiveSheetIndex ()->getCell ( 'O' . $i )->getCalculatedValue () ) : 0;
					
					$datos_beneficiario [$i] ['hfc_iphub'] = (! is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'P' . $i )->getCalculatedValue () )) ? $informacion->setActiveSheetIndex ()->getCell ( 'P' . $i )->getCalculatedValue () : 0;
					
					$datos_beneficiario [$i] ['hfc_maccpe'] = str_replace ( ':', '', $informacion->setActiveSheetIndex ()->getCell ( 'Q' . $i )->getCalculatedValue () );
				} elseif ($_REQUEST ['tecnologia'] == 2) {
					
					$datos_beneficiario [$i] ['tipo_tecnologia'] = '96';
					
					$datos_beneficiario [$i] ['wman_maccelda'] = (! is_null ( $informacion->setActiveSheetIndex ()->getCell ( 'K' . $i )->getCalculatedValue () )) ? str_replace ( ':', '', $informacion->setActiveSheetIndex ()->getCell ( 'K' . $i )->getCalculatedValue () ) : 0;
					
					$datos_beneficiario [$i] ['wman_ipcelda'] = $informacion->setActiveSheetIndex ()->getCell ( 'L' . $i )->getCalculatedValue ();
					
					$datos_beneficiario [$i] ['wman_nombrenodo'] = $informacion->setActiveSheetIndex ()->getCell ( 'M' . $i )->getCalculatedValue ();
					
					$datos_beneficiario [$i] ['wman_nombresectorial'] = $informacion->setActiveSheetIndex ()->getCell ( 'N' . $i )->getCalculatedValue ();
					
					$datos_beneficiario [$i] ['wman_ipswitchcelda'] = $informacion->setActiveSheetIndex ()->getCell ( 'O' . $i )->getCalculatedValue ();
					
					$datos_beneficiario [$i] ['wman_macsmcelda'] = str_replace ( ':', '', $informacion->setActiveSheetIndex ()->getCell ( 'P' . $i )->getCalculatedValue () );
					
					$datos_beneficiario [$i] ['wman_ipsmcelda'] = $informacion->setActiveSheetIndex ()->getCell ( 'Q' . $i )->getCalculatedValue ();
					
					$datos_beneficiario [$i] ['wman_maccpecelda'] = str_replace ( ':', '', $informacion->setActiveSheetIndex ()->getCell ( 'R' . $i )->getCalculatedValue () );
				} else {
					Redireccionador::redireccionar ( "ErrorNoCargaInformacionHojaCalculo" );
				}
			}
			
			$this->datos_infotecnica = $datos_beneficiario;
			
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
	public function unique_multidim_array($array, $key) {
		$temp_array = array ();
		$i = 0;
		$key_array = array ();
		
		foreach ( $array as $val ) {
			if (! in_array ( $val [$key], $key_array )) {
				$key_array [$i] = $val [$key];
				$temp_array [$i] = $val;
			}
			$i ++;
		}
		return $temp_array;
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );
?>

