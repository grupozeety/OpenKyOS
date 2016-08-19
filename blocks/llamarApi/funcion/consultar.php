<?php

use hojaDeVida\titulosAcademicos\Sql;

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
	var $name;
	var $miConfigurador;
	var $sql;
	
	function configurarERPNext($datosConexion) {
		
		try {
			$this->clientFrappe = new FrappeClient ();
			$this->clientFrappe->configurar ( $datosConexion );
		} catch ( Exception $e ) {
			var_dump ( $e );
		}
		set_error_handler ( function ($errno, $errstr, $errfile, $errline) {
			throw new ErrorException ( $errstr, 0, $errno, $errfile, $errline );
		} );
	}
	
	function configurarOpenProject($datosConexion) {
	
		try {
			$this->clientOpenProject = new OpenProject();
			$this->clientOpenProject->configurar ( $datosConexion );
		} catch ( Exception $e ) {
			var_dump ( $e );
		}
		set_error_handler ( function ($errno, $errstr, $errfile, $errline) {
			throw new ErrorException ( $errstr, 0, $errno, $errfile, $errline );
		} );
	}
	
	function codificarNombre($nombre){
		
		include_once ("core/builder/FormularioHtml.class.php");
		
		$miFormulario = new \FormularioHtml();
		
		if(!isset($_REQUEST['tiempo'])){
			$_REQUEST['tiempo']=time();
		}
		//Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php
		
		$_REQUEST['ready']= true;
		
		return $miFormulario->campoSeguro($nombre);
		
	}
	
	function obtenerAlmacen( $datosConexion){
		
		$this->configurarERPNext ( $datosConexion );
		
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
	
	function obtenerProjectos($datosConexion){
		
		$this->configurarOpenProject ( $datosConexion );
		
		$this->name= 'name';
		
		$data = '';
		
		$fields= 'projects';
	
		$result = $this->clientOpenProject->search ("",$data,$fields );
		
		$arreglo = array();
		
		if (! empty ( $result )) {
		
			$tree = $this->buildTree($result->body['projects']);
			
			echo json_encode($tree);
		
		}
		return false;
	
	}
	
	function obtenerActividades($datosConexion, $proyecto){
	
		$this->configurarOpenProject ( $datosConexion );
		
		$this->name= 'subject';
		
		$data = $proyecto;
		
		$fields= 'planning_elements';
		
		$result = $this->clientOpenProject->search ("projects",$data,$fields );
	
		if (! empty ( $result )) {
	
			$tree = $this->buildTree($result->body['planning_elements']);
				
			echo json_encode($tree);
	
		}
		return false;
	
	}
	
	function buildTree(array &$elements, $parentId = 0) {
		$branch = array();
	
		foreach ($elements as $element) {
			if ($element['parent_id'] == $parentId) {
				$children = $this->buildTree($elements, $element['id']);
				if ($children) {
					$branch[] = array('text'=> $element[$this->name], 'custom'=> $element['id'], $element, 'nodes'=>$children);
				}else{
					$branch[] = array('text'=> $element[$this->name], 'custom'=> $element['id'], $element);
				}
// 				unset($elements[$element['id']]);
			}
		}
		return $branch;
	}
	
	function obtenerOrdenTrabajo($datosConexion){
		
		$this->configurarERPNext ( $datosConexion );
		
		$data = array (
		);
	
		$fields=array(
				"item_code"
		);
	
		$result = $this->clientFrappe->search ( "Stock Entry Detail",$data,$fields );
	
		if (! empty ( $result->body->data )) {
			echo json_encode($result->body->data);
	
		}
	
		return false;
	
	}
	
	function obtenerMaterialesOrden($datosConexion, $nombre){
	
		$this->configurarERPNext ( $datosConexion );
	
		$data = array (
			"item_code" => str_replace(' ', '%20', $nombre)
		);
	
		$fields=array(
				"name",
				"item_name",
				"uom",
				"description",
				"item_code",
				"qty"
				
		);
		
		$result = $this->clientFrappe->search ( "Stock Entry Detail",$data,$fields );
		
		$contador = 0;

		foreach ($result->body->data as $data){
			$data->{"material"} = $this->codificarNombre("material:".$data->name.":".$data->item_name);
			$data->{"project"} = "proyecto";
			$contador++;
		}
		
		if (! empty ( $result->body->data )) {
	
			echo json_encode($result->body->data);
	
		}
	
		return false;
	
	}
	
	function obtenerDetalleOrden($datosConexion, $nombre){
	
		$this->configurarERPNext ( $datosConexion );
	
		$data = array (
				"orden_trabajo" => str_replace(' ', '%20', $nombre)
		);
	
		$fields=array(
				"project",
				"descripcion_orden"
		);
	
		$result = $this->clientFrappe->search ( "Stock Entry",$data,$fields );
	
		
		if (! empty ( $result->body->data )) {
	
			echo json_encode($result->body->data);
	
		}
	
		return false;
	
	}
	
	
	
	
}