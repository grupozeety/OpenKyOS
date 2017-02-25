<?php

namespace facturacion\calculoFactura\entidad;

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
		 * Organizar Información por Roles
		 */
		
		$this->ordenarInfoRoles ();
		
		/**
		 * 2.
		 * Recuperar Reglas por Rol
		 */
		
		$this->reglasRol ();
		$this->consultarUsuarioRol ();
		
		$resultado = $this->verificarFactura ();
		
		if ($resultado > 0) {
			//Mensaje de factura para el ciclo generada
			Redireccionador::redireccionar ( "ErrorFactura",'');
		} else {
			$this->datosContrato ();
			
			/**
			 * 4.
			 * Calcular Valores
			 */
			$this->reducirFormula ();
			
			$this->calculoPeriodo ();
			$this->registrarPeriodo ();
			$this->calculoMora ();
			$this->calculoFactura ();
			
			/**
			 * 5.
			 * Guardar Conceptos de Facturación
			 */
			
			$this->guardarFactura ();
			$this->guardarConceptos ();
			
			/**
			 * 6.
			 * Revisar Resultado Proceso
			 */

			if ($this->registroConceptos ['resultado'] == 0) {
				Redireccionador::redireccionar ( "ExitoInformacion" );
			} else {
				Redireccionador::redireccionar ( "ErrorInformacion", $this->registroConceptos ['resultado'] );
			}
		}
	}
	public function ordenarInfoRoles() {
		$roles = json_decode ( base64_decode ( $_REQUEST ['roles'] ), true );
		
		foreach ( $roles as $data => $valor ) {
			foreach ( $_REQUEST as $key => $values ) {
				if (strncmp ( $key, "data", 4 ) === 0) {
					if (strtok ( substr ( $key, 4 ), "_" ) === $roles [$data] ['id_rol']) {
						if (strpos ( $key, 'periodo' ) !== false) {
							$p = $values;
						}
						if (strpos ( $key, 'cantidad' ) !== false) {
							$c = $values;
						}
						if (strpos ( $key, 'fecha' ) !== false) {
							$f = $values;
						}
					}
				}
			}
			
			$rolPeriodo [$roles [$data] ['id_rol']] = array (
					'periodo' => $p,
					'cantidad' => $c,
					'fecha' => date ( "Y/m/d H:i:s", strtotime ( $f ) ),
					'reglas' => array () 
			);
		}
		
		$this->rolesPeriodo = $rolPeriodo;
	}
	public function reglasRol() {
		foreach ( $this->rolesPeriodo as $key => $vales ) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarReglas', $key );
			$reglas = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			foreach ( $reglas as $a => $b ) {
				$this->rolesPeriodo [$key] ['reglas'] [$reglas [$a] ['identificador']] = $reglas [$a] ['formula'];
			}
		}
	}
	public function consultarUsuarioRol() {
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarUsuarioRol', $_REQUEST ['id_beneficiario'] );
		$this->idUsuarioRol = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		foreach ( $this->idUsuarioRol as $key => $values ) {
			foreach ( $this->rolesPeriodo as $llave => $valores ) {
				if ($this->idUsuarioRol [$key] ['id_rol'] == $llave) {
					$this->rolesPeriodo [$llave] ['id_usuario_rol'] = $this->idUsuarioRol [$key] ['id_usuario_rol'];
				}
			}
		}
	}
	public function verificarFactura() {
		$res = 0;
		foreach ( $this->rolesPeriodo as $key => $vales ) {
			foreach ( $this->rolesPeriodo as $llave => $valores ) {
				
				$ciclo = date ( "Y", strtotime ( $this->rolesPeriodo [$key] ['fecha'] ) ) . '-' . date ( "m", strtotime ( $this->rolesPeriodo [$key] ['fecha'] ) );
				$datos = array (
						'id_usuario_rol' => $this->rolesPeriodo [$llave] ['id_usuario_rol'],
						'id_ciclo' => $ciclo 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarFactura', $datos );
				$resultado = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				if ($resultado != FALSE) {
					$this->registroConceptos ['observaciones'] = 'Ya existe una factura para el ciclo ' . $ciclo;
					$res ++;
				} else {
					$res = 0;
				}
			}
		}
		
		return $res;
	}
	public function datosContrato() {
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarContrato', $_REQUEST ['id_beneficiario'] );
		$this->datosContrato = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
	}
	public function reducirFormula() {
		$contar = 0;
		$formula_base = 0;
		
		do {
			
			foreach ( $this->rolesPeriodo as $key => $values ) {
				foreach ( $values ['reglas'] as $variable => $c ) {
					foreach ( $values ['reglas'] as $incognita => $d ) {
						$incognita = preg_replace ( "/\b" . $incognita . "\b/", $d, $c, - 1, $contar );
						if ($contar == 1) {
							$this->rolesPeriodo [$key] ['reglas'] [$variable] = $incognita;
							$termina = false;
						}
					}
					$formula_base = $formula_base . "+" . $this->rolesPeriodo [$key] ['reglas'] [$variable];
				}
				$formulaRol [$key] = $formula_base;
				$formula_base = 0;
			}
			
			$termina = true;
		} while ( $termina == false );
		
		$this->formularRolGlobal = $formulaRol;
	}
	public function calculoPeriodo() {
		foreach ( $this->rolesPeriodo as $key => $values ) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarPeriodo', $values ['periodo'] );
			$periodoUnidad = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0] ['valor'];
			
			$this->rolesPeriodo [$key] ['periodoValor'] = ( double ) ($periodoUnidad);
		}
	}
		public function calculoMora() {
		foreach ( $this->rolesPeriodo as $key => $values ) {
				
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarMoras', $_REQUEST ['id_beneficiario'] );
			$facturasVencidas = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
			if ($facturasVencidas != FALSE) {
				$dm = floor ( (time () - strtotime ( $facturasVencidas [0] ['fin_periodo'] )) / 86400 );
			} else {
				$dm = 0;
			}
			$this->rolesPeriodo [$key] ['mora'] = $dm;
		}
	}
	
	// Registrar el ciclo de facturación de acuerdo al periodo seleccionado
	public function registrarPeriodo() {
		foreach ( $this->rolesPeriodo as $key => $values ) {
			
			if ($this->rolesPeriodo [$key] ['periodoValor'] == 1) {
				$fin = date ( 'Y/m/d H:i:s', strtotime ( $this->rolesPeriodo [$key] ['fecha'] . '+ 1 month' ) );
			} elseif ($this->rolesPeriodo [$key] ['periodoValor'] == 720) {
				$fin = date ( 'Y/m/d H:i:s', strtotime ( $this->rolesPeriodo [$key] ['fecha'] . '+' . $values ['cantidad'] . ' hours' ) );
			} elseif ($this->rolesPeriodo [$key] ['periodoValor'] == 30) {
				$fin = date ( 'Y/m/d H:i:s', strtotime ( $this->rolesPeriodo [$key] ['fecha'] . '+' . $values ['cantidad'] . ' days' ) );
			} else {
				$fin = date ( 'Y/m/d H:i:s', strtotime ( $this->rolesPeriodo [$key] ['fecha'] . '+ 1 month' ) );
			}
			
			// En un mundo ideal un float alcanzaría para dates basados en meses ((1 / $this->rolesPeriodo [$key] ['periodoValor']) * $values ['cantidad']);
			
			$usuariorolperiodo = array (
					'id_usuario_rol' => $this->rolesPeriodo [$key] ['id_usuario_rol'],
					'id_periodo' => $this->rolesPeriodo [$key] ['periodo'],
					'inicio_periodo' => $this->rolesPeriodo [$key] ['fecha'],
					'fin_periodo' => $fin,
					'id_ciclo' => date ( "Y", strtotime ( $this->rolesPeriodo [$key] ['fecha'] ) ) . '-' . date ( "m", strtotime ( $this->rolesPeriodo [$key] ['fecha'] ) ) 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarPeriodoRolUsuario', $usuariorolperiodo );
			$periodoRolUsuario = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0] ['id_usuario_rol_periodo'];
			$this->rolesPeriodo [$key] ['id_usuario_rol_periodo'] = $periodoRolUsuario;
		}
	}
	public function calculoFactura() {
		$total = 0;
		$vm = $this->datosContrato ['vm'];
		$factura = 0;
		
		foreach ( $this->rolesPeriodo as $key => $values ) {
			$total = 0;
			foreach ( $values ['reglas'] as $variable => $c ) {
				$a = preg_replace ( "/\bvm\b/", ($vm / $values ['periodoValor']) * $values ['cantidad'], $c, - 1, $contar );
				$b = preg_replace ( "/\bdm\b/", $values ['mora'], $a, - 1, $contar );
				$valor = eval ( 'return (' . $b . ');' );
				$this->rolesPeriodo [$key] ['valor'] [$variable] = $valor;
				$total = $total + $this->rolesPeriodo [$key] ['valor'] [$variable];
			}
			
			$factura = $factura + $total;
			$this->rolesPeriodo [$key] ['valor'] ['vm'] = $this->datosContrato ['vm'];
			$this->rolesPeriodo [$key] ['valor'] ['total'] = $total;
		}
	}
	public function guardarFactura() {
		foreach ( $this->rolesPeriodo as $key => $values ) {
			$informacion_factura = array (
					'id_usuario_rol' => $this->rolesPeriodo [$key] ['id_usuario_rol'],
					'total_factura' => $this->rolesPeriodo [$key] ['valor'] ['total'],
					'id_beneficiario' => $_REQUEST ['id_beneficiario'] ,
					'id_ciclo' => date ( "Y", strtotime ( $this->rolesPeriodo [$key] ['fecha'] ) ) . '-' . date ( "m", strtotime ( $this->rolesPeriodo [$key] ['fecha'] ) )
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarFactura', $informacion_factura );
			$this->registroFactura [$key] ['factura'] = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0] ['id_factura'];
		}
	}
	public function guardarConceptos() {
		$this->registroConceptos ['observaciones'] = '';
		$a = 0;
		
		foreach ( $this->rolesPeriodo as $key => $values ) {
			foreach ( $values ['reglas'] as $llave => $valores ) {
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarReglaID', $llave );
				$reglaid = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0] ['id_regla'];
				
				$registroConceptos = array (
						'id_factura' => $this->registroFactura [$key] ['factura'],
						'id_regla' => $reglaid,
						'valor_calculado' => $values ['valor'] [$llave],
						'id_usuario_rol_periodo' => $this->rolesPeriodo [$key] ['id_usuario_rol_periodo'] 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'registrarConceptos', $registroConceptos );
				$this->registroConceptos [$key] = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
				
				if ($this->registroConceptos [$key] == false) {
					$a ++;
				}
			}
		}
		$this->registroConceptos ['resultado'] = $a;
		
		if ($a == 0) {
			$this->registroConceptos ['observaciones'] = 'Factura Generada Exitosamente';
		} else {
			$this->registroConceptos ['observaciones'] = 'Error en la generación de la factura';
		}
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );
?>

