<?php
require (dirname ( __FILE__ ) . '/FrappeClient.php');

class Consultar {
	var $ordenVenta = '';
	var $error;
	var $itemOrdenVenta = array ();
	var $itemNotaEntrega = array ();
	var $notaEntrega = '';
	var $numeroFactura = '';
	var $cantidad = 0;
	var $producto = '0';
	var $client;
	var $expenseAccount;
	var $debitAccount;
	var $debitToAccount;
	
	function configurar($datosConexion) {
		
		try {
			$this->client = new FrappeClient ();
			$this->client->configurar ( $datosConexion );
		} catch ( Exception $e ) {
			var_dump ( $e );
		}
		set_error_handler ( function ($errno, $errstr, $errfile, $errline) {
			throw new ErrorException ( $errstr, 0, $errno, $errfile, $errline );
		} );
	}

	function obtenerAlmacen( $datosConexion){
		
		$this->configurar ( $datosConexion );
		
		$data = array (
		);
		
		$fields=array(
				"name",
				"default_warehouse",
				"stock_uom"
		);
		
		$result = $this->client->search ( "Item",$data,$fields );
	
		if (! empty ( $result->body->data )) {
			
			echo json_encode($result->body->data);
			
		}
		
		return false;
		
	}
	
	function obtenerNombreAlmacen($nombre){
		
		$data = array (
				"name" => str_replace(' ', '%20', $nombre)
		);
		
		$fields=array(
				"warehouse_name"
		);
		
		$result = $this->client->search ( "Warehouse",$data,$fields );
		
		if (! empty ( $result->body->data )) {
				
			return $result->body->data[0]->warehouse_name;
				
		}
		return false;
		
	}
	
	
	
}