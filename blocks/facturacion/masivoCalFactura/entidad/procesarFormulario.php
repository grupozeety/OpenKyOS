<?php

namespace facturacion\masivoCalFactura\entidad;

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
	public $id_beneficiario;
	public $iterar;
	public $estado;
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
		
		$conexion2 = "otun";
		$this->esteRecursoDBOtun = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion2 );
		
		if ($_REQUEST ['beneficiario'] != '') {
			$beneficiarios = explode ( ',', preg_replace ( "/[^0-9,]/", "", $_REQUEST ['beneficiario'] ) );
			
			$string = '';
			foreach ( $beneficiarios as $key => $values ) {
				$string .= "'" . $beneficiarios [$key] . "',";
			}
			
			$filtro [0] = 'beneficiarios';
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarBeneficiariosArea', $string );
			$this->beneficiarios = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		} elseif ($_REQUEST ['urbanizacion'] != '') {
			
			$filtro = array (
					'urbanizacion' => $_REQUEST ['urbanizacion'],
					0 => $_REQUEST ['urbanizacion'] 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarBeneficiarios', $filtro );
			$this->beneficiarios = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		} elseif ($_REQUEST ['municipio'] != '') {
			$filtro = array (
					'municipio' => $_REQUEST ['municipio'],
					0 => $_REQUEST ['municipio'] 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarBeneficiarios', $filtro );
			$this->beneficiarios = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		} elseif ($_REQUEST ['departamento'] != '') {
			$filtro = array (
					'departamento' => $_REQUEST ['departamento'],
					0 => $_REQUEST ['departamento'] 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarBeneficiarios', $filtro );
			$this->beneficiarios = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		} else {
			Redireccionador::redireccionar ( "ErrorInformacion", '' );
		}
		
		$this->filtro = $filtro [0];
		
		$this->creacion_log ();
		
		/**
		 * Determinar Beneficiarios*
		 */
		
		$_REQUEST ['tiempo'] = time ();
		
		/**
		 * Determinar Roles*
		 */
		/**
		 * Aquí es dónde se especifica qué periodo aplica para cada rol*
		 */
		
		foreach ( $this->beneficiarios as $key => $values ) {
			$this->iterar = 0;
			$this->id_beneficiario = $values ['id_beneficiario'];
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarBeneficiario', $values ['id_beneficiario'] );
			$actaActiva = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			// Saber qué roles tiene asociados
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarFacturaAprobado', $values ['id_beneficiario'] );
			$resultadoAprobado = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			if ($resultadoAprobado != FALSE) {
				$mensaje = $values ['identificacion'] . "-" . $values ['id_beneficiario'] . ": Existe una factura aprobada, pendiente de pago sin vencer.";
				$this->escribir_log ( $mensaje );
			} else {
				
				if ($actaActiva != FALSE) {
					
					$roles = $this->calcularRoles ();
					
					$rolPeriodo = $this->calFechaFinal ( $roles );
					
					$resultado [$values ['id_beneficiario']] ['observaciones'] = json_decode ( $this->calcular->calcularFactura ( $values ['id_beneficiario'], $rolPeriodo, $this->estado ), true );
					
					$this->escribir_log ( $values ['identificacion'] . ':' . json_encode ( $resultado [$values ['id_beneficiario']] ['observaciones'] ['observaciones'] . ". " . $resultado [$values ['id_beneficiario']] ['observaciones'] ['cliente'] [0] . ".  " ) );
					
					// if ($this->iterar == 1) {
					do {
						$roles = $this->calcularRoles ();
						$rolPeriodo = $this->calFechaFinal ( $roles );
						$resultado [$values ['id_beneficiario']] ['observaciones'] = json_decode ( $this->calcular->calcularFactura ( $values ['id_beneficiario'], $rolPeriodo, $this->estado ), true );
						
						$this->escribir_log ( $values ['identificacion'] . ':' . json_encode ( $resultado [$values ['id_beneficiario']] ['observaciones'] ['observaciones'] . ". " . $resultado [$values ['id_beneficiario']] ['observaciones'] ['cliente'] [0] . ". " . $resultado [$values ['id_beneficiario']] ['observaciones'] ['cliente'] [1] . "." ) );
					} while ( $this->iterar == 1 );
					
					// }
					// Saber qué periodo aplica cada rol
				} else {
					$mensaje = $values ['identificacion'] . "-" . $values ['id_beneficiario'] . ": Sin factura generada. No hay Acta Entrega de Servicios subida al sistema.";
					$this->escribir_log ( $mensaje );
				}
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
		
		$this->ruta_absoluta_log = $this->rutaAbsoluta . "/entidad/logs/Log_" . $this->filtro . "_" . $prefijo . ".log";
		
		$this->ruta_relativa_log = $this->rutaURL . "/entidad/logs/Log_" . $this->filtro . "_" . $prefijo . ".log";
		
		$this->log = fopen ( $this->ruta_absoluta_log, "w" );
	}
	public function calFechaFinal($roles) {
		$this->iterar = 0;
		
		foreach ( $roles as $data => $valor ) {
			
			$array = array (
					'id_rol' => $roles [$data] ['id_rol'],
					'id_beneficiario' => $this->id_beneficiario 
			);
			
			// Saber la fecha desde de facturación
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarUsuarioRolPeriodo', $array );
			$fechaFin = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			if ($fechaFin == FALSE) {
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarFechaInicio', $this->id_beneficiario );
				$fechaFin = $this->esteRecursoDBOtun->ejecutarAcceso ( $cadenaSql, "busqueda" );
			}
			
			$fechaFinal = date ( "Y/m/d H:i:s", strtotime ( $fechaFin [0] [0] ) );
			
			$a = date ( 'Y/m/d', strtotime ( $fechaFinal . '+1 day' ) );
			$m = date ( 'm', strtotime ( $fechaFinal . '+1 day' ) );
			
			if ($a < date ( "Y/m/01" )) {
					$this->iterar = 1;
					$this->estado = 'Mora';
			} else {
				$this->iterar = 0;
				$this->estado = 'Borrador';
			}
			
			$rolPeriodo [$roles [$data] ['id_rol']] = array (
					'periodo' => 1,
					'cantidad' => 1,
					'fecha' => $fechaFinal,
					'reglas' => array () 
			);
		}
		
		return $rolPeriodo;
	}
	public function calcularRoles() {
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarUsuarioRol', $this->id_beneficiario );
		$roles = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		if ($roles === FALSE) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarUsuarioRol_predeterminado' );
			$roles = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			// Registrar Usuario-Rol
			$userrol = array (
					'id_beneficiario' => $this->id_beneficiario,
					'id_rol' => $roles [0] [0] 
			);
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarAsociacion', $userrol );
			$registro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
		}
		
		return $roles;
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );
?>

