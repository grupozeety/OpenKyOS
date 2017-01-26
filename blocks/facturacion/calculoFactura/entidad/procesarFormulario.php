<?php

namespace facturacion\calculoFactura;

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
		
		$this->calculoFactura ();
		
		// /**
		// * 5.
		// * Guardar Conceptos de Facturación
		// */
		
		// $this->procesarInformacion ();
		
		/**
		 * 6.
		 * Revisar Resultado Proceso
		 */
		exit ();
		if ($a == 0) {
			Redireccionador::redireccionar ( "InsertoInformacionContrato" );
		} else {
			Redireccionador::redireccionar ( "NoInsertoInformacionContrato" );
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
	public function calculoFactura() {
		$total = 0;
		$vm = $this->datosContrato ['vm'];
		$dm = 0;
		
		foreach ( $this->rolesPeriodo as $key => $values ) {
			$total = 0;
			foreach ( $values ['reglas'] as $variable => $c ) {
				$a = preg_replace ( "/\bvm\b/", $vm, $c, - 1, $contar );
				$b = preg_replace ( "/\bdm\b/", $dm, $a, - 1, $contar );
				$valor = eval ( 'return (' . $b . ');' );
				$this->rolesPeriodo [$key] ['valor'] [$variable] = $valor;
				$total = $total + $this->rolesPeriodo [$key] ['valor'] [$variable];
			}
			$this->valoresFacturaRol [$key] = $total;
		}
	}
	public function multiexplode($delimiters, $string) {
		$ready = str_replace ( $delimiters, $delimiters [0], $string );
		$launch = explode ( $delimiters [0], $ready );
		return $launch;
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );
?>

