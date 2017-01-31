<?php

namespace facturacion\metodoFactura\entidad;

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
		 * Revisar Existencia Asociación
		 */
		

		$this->getMetodo ();
		$this->revisarExistencia ();
		
		/**
		 * 2.
		 * Registrar Asociación
		 */
		
		$this->registrarMetodo ();
		
		exit ();
	}
	public function getMetodo() {
		$this->asociacion = array (
				'id_rol' => $_REQUEST ['rol'],
				'id_regla' => $_REQUEST ['regla'] 
		);
	}
	public function revisarExistencia() {
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarAsociacion', $this->asociacion );
		$asociacion = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

		if ( $asociacion !=FALSE) {
			Redireccionador::redireccionar ( "ErrorConsulta" );
			exit ();
		}
	}
	public function registrarMetodo() {
		$cadenaSql = $this->miSql->getCadenaSql ( 'registrarMetodo', $this->asociacion );
		$registro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );

		if ($registro==TRUE) {
			Redireccionador::redireccionar ( "InsertoInformacion" );
			exit();
		} else {
			Redireccionador::redireccionar ( "NoInsertoInformacion" );
			exit();
		}
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );
?>

