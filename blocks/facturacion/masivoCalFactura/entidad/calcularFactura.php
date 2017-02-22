<?php

namespace facturacion\masivoCalFactura\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

class Calcular {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miFuncion;
	public $miSql;
	public $conexion;
	
	public function __construct($lenguaje, $sql) {
		$this->miConfigurador = \Configurador::singleton();
		$this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
		$this->lenguaje = $lenguaje;
		$this->miSql    = $sql;
		
		$conexion = "interoperacion";
		$this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
	}
	public function calcularFactura($beneficiario, $roles) {
		/** Definir variables Gloables**/
		
		$_REQUEST['id_beneficiario']=$beneficiario;
		$_REQUEST['roles']=$roles;
		
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
			
		/**
		 * 3.
		 * Obtener Datos del Contrato
		 */
			
		$this->datosContrato ();
			
		/**
		 * 4.
		 * Calcular Valores
		 */
		$this->reducirFormula ();
			
		$this->calculoPeriodo ();
		$this->registrarPeriodo ();
			
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
					'fecha' => $f,
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
	public function registrarPeriodo() {
		foreach ( $this->rolesPeriodo as $key => $values ) {
	
			$usuariorolperiodo = array (
					'id_usuario_rol' => $this->rolesPeriodo [$key] ['id_usuario_rol'],
					'id_periodo' => $this->rolesPeriodo [$key] ['periodo'],
					'inicio_periodo' => $this->rolesPeriodo [$key] ['fecha']
			);
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarPeriodoRolUsuario', $usuariorolperiodo );
			$periodoRolUsuario = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0] ['id_usuario_rol_periodo'];
			$this->rolesPeriodo [$key] ['id_usuario_rol_periodo'] = $periodoRolUsuario;
		}
	}
	public function calculoFactura() {
		$total = 0;
		$vm = $this->datosContrato ['vm'];
		$dm = 0;
		$factura = 0;
	
		foreach ( $this->rolesPeriodo as $key => $values ) {
			$total = 0;
			foreach ( $values ['reglas'] as $variable => $c ) {
				$a = preg_replace ( "/\bvm\b/", ($vm / $values ['periodoValor']) * $values ['cantidad'], $c, - 1, $contar );
				$b = preg_replace ( "/\bdm\b/", $dm, $a, - 1, $contar );
				$valor = eval ( 'return (' . $b . ');' );
				$this->rolesPeriodo [$key] ['valor'] [$variable] = $valor;
				$total = $total + $this->rolesPeriodo [$key] ['valor'] [$variable];
			}
	
			$factura = $factura + $total;
			$this->rolesPeriodo [$key] ['valor'] ['total'] = $total;
		}
	}
	public function guardarFactura() {
		foreach ( $this->rolesPeriodo as $key => $values ) {
			$informacion_factura = array (
					'id_usuario_rol' => $this->rolesPeriodo [$key] ['id_usuario_rol'],
					'total_factura' => $this->rolesPeriodo [$key] ['valor'] ['total'],
					'id_beneficiario' => $_REQUEST ['id_beneficiario']
			);
	
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarFactura', $informacion_factura );
			$this->registroFactura [$key] ['factura'] = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0] ['id_factura'];
		}
	}
	public function guardarConceptos() {
		$this->registroConceptos ['resultado'] = 0;
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
				echo $this->registroConceptos [$key] = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
	
				if ($this->registroConceptos [$key] == false) {
					$a ++;
				}
			}
		}
		$this->registroConceptos ['resultado'] = $a;
	}
}

?>


