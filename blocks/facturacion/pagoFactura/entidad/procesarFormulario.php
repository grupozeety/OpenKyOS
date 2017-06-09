<?php

namespace facturacion\pagoFactura\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

include_once 'Redireccionador.php';
include_once 'comprobante.php';

include_once 'sincronizarErp.php';
class FormProcessor {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $conexion;
	public $esteRecursoDB;
	public $sincronizar;
	public function __construct($lenguaje, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		
		$this->sincronizar = new sincronizarErp ( $lenguaje, $sql );
		
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		
		$this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		
		$this->comprobante = new GenerarDocumento ( $lenguaje, $sql );
		
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
		 * Revisar Valor de Factura Coincida con el Pago
		 */
		
		$this->revisarFactura ();
		
		/**
		 * 2.
		 * Registrar Pago
		 */
		
		$this->medioPago ( $_REQUEST ['medio_pago'] );
		/**
		 * 3.
		 * Revisar si tiene facturas en mora asociadas el pago
		 */
		
		$resultado = $this->registrarPago ();
		
		if ($resultado == FALSE) {
			Redireccionador::redireccionar ( "ErrorPago" );
			exit ();
		} else {
			$update = $this->actualizarFactura ( $_REQUEST ['id_factura'] );
			$this->actualizarERP($_REQUEST ['id_factura']);
		}
		/**
		 * 3.
		 * Generar Comprobante
		 */
		
		if ($update == TRUE) {
			$this->consultarMoras ();
			
			if ($_REQUEST ['estadoFactura'] == 'Mora') {
				$this->inactivarPadre ();
			}
			
			$this->generarComprobante ();
		} else {
			Redireccionador::redireccionar ( "ErrorUpdate" );
			exit ();
		}

	}
	public function revisarFactura() {
		$valor_recibido = $_REQUEST ['valor_recibido'];
		$valor_factura = $_REQUEST ['valor_factura'] + $_REQUEST ['valor_abono'];
		
		if ($valor_recibido - $valor_factura < 0) {
			Redireccionador::redireccionar ( "ErrorValor" );
			exit ();
		}
	}
	public function medioPago($id) {
		$cadenaSql = $this->miSql->getCadenaSql ( 'medioPago', $id );
		$registro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$_REQUEST ['medioTexto'] = $registro [0] [0];
		
		return $registro;
	}
	public function registrarPago() {
		$this->asociacion = array (
				'id_factura' => $_REQUEST ['id_factura'],
				'valor_pagado' => $_REQUEST ['valor_factura'],
				'valor_recibido' => $_REQUEST ['valor_recibido'],
				'usuario' => $_REQUEST ['usuario'],
				'medio_pago' => $_REQUEST ['medio_pago'],
				'abono_adicional' => $_REQUEST ['valor_abono'],
				'valor_devuelto' => $_REQUEST ['valor_recibido'] - $_REQUEST ['valor_factura'] - $_REQUEST ['valor_abono'] 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'registrarPago', $this->asociacion );
		$registro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$_REQUEST ['idPago'] = $registro [0] [0];
		$_REQUEST ['valor_devuelto'] = $_REQUEST ['valor_recibido'] - $_REQUEST ['valor_factura'] - $_REQUEST ['valor_abono'];
		
		return $registro;
	}
	
	public function actualizarFactura($idFactura) {
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarFactura', $idFactura );
		$update = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
		
		return $update;
	}
	
	public function actualizarFacturaM($idFactura) {
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarFacturaM', $idFactura );
		$update = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
	
		return $update;
	}
	
	public function consultarMoras() {
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarMoras', $_REQUEST ['id_factura'] );
		$moras = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		if ($moras != FALSE) {
			foreach ( $moras as $key => $values ) {
				
				$this->actualizarFacturaM ( $moras [$key] ['factura_mora'] );
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'facturaERP', $moras [$key] ['factura_mora'] );
				$erp = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" )[0]['factura_erpnext'];
				
				$url = $this->sincronizar->crearUrlFactura ( $erp );
				$this->sincronizar->cancelarFactura ( $url );
			}
		}

	}
	public function inactivarPadre() {
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarPadre', $_REQUEST ['id_factura'] );
		$padre = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		if ($padre != FALSE) {
			foreach ( $padre as $key => $values ) {
				
				$array = array (
						'id_factura' => $padre [$key] ['id_factura'],
						'idPago' => $_REQUEST ['idPago'] 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarFacturaPadre', $array );
				$padreUpdate = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
				
				$url = $this->sincronizar->crearUrlFactura ( $padre [$key] ['factura_erpnext'] );
				$this->sincronizar->cancelarFactura ( $url );
			}
		}
	}
	public function generarComprobante() {
		$this->comprobante->comprobante ();
		exit ();
	}
	
	public function actualizarERP($idFactura){
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarFactura_especifico', $idFactura );
		$padre = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

		$_REQUEST['facturaNum']=$padre [0] ['indice_facturacion'].str_pad($padre[0]['numeracion_facturacion'], 6, "0", STR_PAD_LEFT);
		$array=array(
				'factura_erpnext'=>$padre [0] ['factura_erpnext'],
				'id_beneficiario'=>$_REQUEST ['id_beneficiario'],
				'total_factura'=>$padre[0]['total_factura'],
				'id_factura'=>$_REQUEST['id_factura']
		);
		
		$url = $this->sincronizar->pagarUrlFactura (  $array);
		$this->sincronizar->pagarFactura( $url );
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );
?>

