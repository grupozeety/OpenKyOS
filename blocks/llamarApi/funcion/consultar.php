<?php
require (dirname ( __FILE__ ) . '/FrappeClient.php');
require (dirname ( __FILE__ ) . '/OpenProject.php');

class Consultar {
	var $ordenVenta = '';
	var $error;
	var $itemOrdenVenta = array ();
	var $itemNotaEntrega = array ();
	var $notaEntrega = '';
	var $numeroFactura = '';
	var $cantidad = 0;
	var $producto = '0';
	var $clientFrappe;
	var $clientOpenProject;
	var $expenseAccount;
	var $debitAccount;
	var $debitToAccount;
	
	function configurar($datosConexion) {
		
		try {
			$this->clientFrappe = new FrappeClient ();
			$this->clientOpenProject = new OpenProject();
			$this->clientFrappe->configurar ( $datosConexion );
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
		
		$result = $this->clientFrappe->search ( "Item",$data,$fields );
	
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
		
		$result = $this->clientFrappe->search ( "Warehouse",$data,$fields );
		
		if (! empty ( $result->body->data )) {
				
			echo json_encode($result->body->data[0]->warehouse_name);
				
		}
		
		return false;
		
	}
	
	function obtenerProjecto(){
	
		$this->clientOpenProject = new OpenProject();
		
		$result = $this->clientOpenProject->search ( );
		
		$arreglo = array();
		
		if (! empty ( $result )) {
		
			$tree = $this->buildTree($result->body['projects']);
			
// 			$tree = array(array(1,2,3,4,5), 'nodes'=>array(1,2,3));
			print_r(json_encode($tree));
// 			echo '[' . json_encode($tree, true) . ']';
		
		}
		return false;
	
	}
	
// 	function buildTree(array &$elements, $parentId = 0) {
// 	    $branch = array();
	
// 	    foreach ($elements as $element) {
// 	        if ($element['parent_id'] == $parentId) {
// 	            $children = $this->buildTree($elements, $element['id']);
// 	            if ($children) {
// 	                $element['nodes'] = $children;
// 	            }
// 	            $branch[$element['id']] = $element;
// 	            unset($elements[$element['id']]);
// 	        }
// 	    }
// 	    return $branch;
// 	}
	
	function buildTree(array &$elements, $parentId = 0) {
		$branch = array();
	
		foreach ($elements as $element) {
			if ($element['parent_id'] == $parentId) {
				$children = $this->buildTree($elements, $element['id']);
				if ($children) {
// 					$element['nodes'] = $children;
					$branch[] = array('text'=> $element['name'], $element, 'nodes'=>$children);
				}else{
					$branch[] = array('text'=> $element['name'], $element);
				}
				
// 				if(isset($element['nodes'])){
// 					$branch[] = array('text'=> $element['name'], 'id'=>$element['id'], 'name'=>$element['name'], 'nodes'=>$element['nodes']);
// 				}else{
// 					$branch[] = array('text'=> $element['name'], 'id'=>$element['id'], 'name'=>$element['name']);
// 				}
				unset($elements[$element['id']]);
			}
		}
		return $branch;
	}
	
	
	
}