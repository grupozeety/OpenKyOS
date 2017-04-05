<?php

namespace facturacion\pagoFactura\entidad;

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
		
		var_dump ( $_REQUEST );
		
		/**
		 * 1.
		 * Revisar Valor de Factura Coincida con el Pago
		 */
		
		$this->revisarFactura ();
		
		/**
		 * 2.
		 * Registrar Pago
		 */
		
		$resultado = $this->registrarPago ();
		
		if ($resultado == TRUE) {
			$update = $this->actualizarFactura ();
		} else {
			Redireccionador::redireccionar ( "ErrorPago" );
			exit ();
		}
		/**
		 * 3.
		 * Generar Comprobante
		 */
		
		if ($update == TRUE) {
			$this->generarComprobante ();
		} else {
			Redireccionador::redireccionar ( "ErrorUpdate" );
			exit ();
		}
		
		exit ();
	}
	public function revisarFactura() {
		$valor_recibido = $_REQUEST ['valor_recibido'];
		$valor_factura = $_REQUEST ['valor_factura'];
		
		if ($valor_recibido - $valor_factura < 0) {
			Redireccionador::redireccionar ( "ErrorValor" );
			exit ();
		}
	}
	public function registrarPago() {
		$this->asociacion = array (
				'id_factura' => $_REQUEST ['id_factura'],
				'valor_pagado' => $_REQUEST ['valor_factura'],
				'valor_recibido' => $_REQUEST ['valor_recibido'],
				'usuario' => $_REQUEST ['usuario'],
				'medio_pago' => $_REQUEST ['medio_pago'] 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'registrarPago', $this->asociacion );
		$registro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
		
		return $registro;
	}
	public function actualizarFactura() {
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarFactura', $_REQUEST ['id_factura'] );
		$update = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
		
		return $update;
	}
	
	public function generarComprobante(){
		echo "generar comprobante";
		exit;
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );
?>

