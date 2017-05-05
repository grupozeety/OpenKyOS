<?php

namespace facturacion\configuracionFactura\entidad;

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
		
		$this->actualizarRol ();

		if ($this->registro == TRUE) {
			Redireccionador::redireccionar ( "InsertoInformacion" );
			exit ();
		} else {
			Redireccionador::redireccionar ( "NoInsertoInformacion" );
			exit ();
		}
	}
	public function actualizarRol() {
	
		$variable = array (
				'rol' => $_REQUEST ['rol'],
				'diasPago' => $_REQUEST ['diasPago'],
				'cuentaDebito_erp' => $_REQUEST ['cuentaDebito_erp'],
				'cuentaCredito_erp' => $_REQUEST ['cuentaCredito_erp'],
				'grupoClientes_erp' => $_REQUEST ['grupoClientes_erp'],
				'itemName_erp' => $_REQUEST ['itemName_erp'],
				'itemCode_erp' => $_REQUEST ['itemCode_erp'],
				'stockUOM_erp' => $_REQUEST ['stockUOM_erp'] 
		);
		

		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarRol', $variable );
		$this->registro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );
?>

