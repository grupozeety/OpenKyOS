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
		
		$this->getMetodo ();
		$this->actualizarMetodo ();
		
		exit ();
	}
	public function getMetodo() {
		$this->asociacion = array (
				'id_rol' => $_REQUEST ['rol'],
				'id_regla' => $_REQUEST ['regla'],
				'id_metodo' => $_REQUEST ['idmetodo'] 
		);
	}
	public function actualizarMetodo() {
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarMetodo', $this->asociacion );
		$registro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
		
		if ($registro == TRUE) {
			Redireccionador::redireccionar ( "UpdateInformacion" );
			exit ();
		} else {
			Redireccionador::redireccionar ( "NoUpdateInformacion" );
			exit ();
		}
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );
?>

