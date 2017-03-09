<?php

namespace facturacion\calculoFactura\entidad;

use facturacion\masivoCalFactura\entidad\Calcular;

require_once ('calcularFactura.php');
if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

include_once 'Redireccionador.php';
class FormProcessor {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $conexion;
	public $esteRecursoDB;
	public function __construct($lenguaje, $sql) {
		date_default_timezone_set ( 'America/Bogota' );
		
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		
		$this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		
		$this->calcular = new Calcular ( $lenguaje, $sql );
		
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
		

		
		if ($_REQUEST ['urbanizacion'] != '') {
			$filtro = array (
					'urbanizacion' => $_REQUEST ['urbanizacion'] ,
				        0 => $_REQUEST ['urbanizacion'] ,
			);
		} elseif ($_REQUEST ['municipio'] != '') {
			$filtro = array (
					'municipio' => $_REQUEST ['municipio'] ,
				         0 => $_REQUEST ['municipio'] 
			);
		} elseif ($_REQUEST ['departamento'] != '') {
			$filtro = array (
					'departamento' => $_REQUEST ['departamento'] ,
				        0 => $_REQUEST ['departamento'] 
			);
		} else {
			Redireccionador::redireccionar ( "ErrorInformacion", '' );
		}
		$this->filtro=$filtro[0];
		
		$this->creacion_log ();


		/**
		 * Determinar Beneficiarios*
		 */
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarBeneficiarios', $filtro );
		$this->beneficiarios = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$_REQUEST ['tiempo'] = time ();
		
		/**
		 * Determinar Roles*
		 */
		/**
		 * Aquí es dónde se especifica qué periodo aplica para cada rol*
		 */
		
		foreach ( $this->beneficiarios as $key => $values ) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarFechaInicio', $values ['id_beneficiario'] );
			$actaActiva = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			// Saber qué roles tiene asociados
			
			if ($actaActiva != FALSE) {
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarUsuarioRol', $values ['id_beneficiario'] );
				$roles = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				if ($roles === FALSE) {
					$cadenaSql = $this->miSql->getCadenaSql ( 'consultarUsuarioRol_predeterminado' );
					$roles = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
					
					// Registrar Usuario-Rol
					$userrol = array (
							'id_beneficiario' => $values ['id_beneficiario'],
							'id_rol' => $roles [0] [0] 
					);
					$cadenaSql = $this->miSql->getCadenaSql ( 'registrarAsociacion', $userrol );
					$registro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
				}
				
				// Saber la fecha desde de facturación
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarUsuarioRolPeriodo', $values ['id_beneficiario'] );
				$fechaFin = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				

				if ($fechaFin == FALSE) {
					$cadenaSql = $this->miSql->getCadenaSql ( 'consultarFechaInicio', $values ['id_beneficiario'] );
					$fechaFin = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				}
			
				foreach ( $roles as $data => $valor ) {
					$rolPeriodo [$roles [$data] ['id_rol']] = array (
							'periodo' => 1,
							'cantidad' => 1,
							'fecha' => date ( "Y/m/d H:i:s", strtotime ( $fechaFin [0] [0] ) ),
							'reglas' => array () 
					);
				}
				
				$resultado [$values ['id_beneficiario']] ['observaciones'] = $this->calcular->calcularFactura ( $values ['id_beneficiario'], $rolPeriodo );
				$this->escribir_log ( $values ['identificacion'] . ':' . json_encode ( $resultado [$values ['id_beneficiario']] ['observaciones'] ) );
				// Saber qué periodo aplica cada rol
			} else {
				$mensaje = $values ['id_beneficiario'] . ": Sin factura generada. No hay Acta Entrega de Servicios activa.";
				$this->escribir_log ( $mensaje );
			}
		}
		
		Redireccionador::redireccionar ( "Informacion", base64_encode ( $this->ruta_relativa_log ) );
	}
	public function escribir_log($mensaje) {
		fwrite ( $this->log, $mensaje . PHP_EOL );
	}
	public function cerrar_log() {
		fclose ( $this->log );
	}
	public function creacion_log() {
		$prefijo = substr ( md5 ( uniqid ( time () ) ), 0, 6 );
		
		$this->ruta_absoluta_log = $this->rutaAbsoluta . "/entidad/logs/Log_".$this->filtro."_" . $prefijo . ".log";
		
		$this->ruta_relativa_log = $this->rutaURL . "/entidad/logs/Log_".$this->filtro."_" . $prefijo . ".log";
		
		$this->log = fopen ( $this->ruta_absoluta_log, "w" );
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );
?>

