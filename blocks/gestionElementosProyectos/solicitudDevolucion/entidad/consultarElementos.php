<?php

namespace gestionElementosProyectos\solicitudDevolucion\entidad;

class Elementos {
	var $miConfigurador;
	var $miSql;
	var $conexion;
	var $infoElementos;
	function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->miSql = $sql;
		
		/**
		 * Infomación de Proyectos
		 */
		$this->estructurarInformacionElementos ();
		
		/**
		 * Restornar Proyectos
		 */
		
		$this->retornarElementos ();
	}
	function estructurarInformacionElementos() {
		$this->infoElementos = array (
				0 => array (
						"id_elemento" => "COX1",
						"decripcion" => "Cables Coaxial",
						"cantidad" => "40",
						"devolucion" => "Opción Devolución" 
				),
				
				1 => array (
						"id_elemento" => "PCL",
						"decripcion" => "Compuatdores Lenovo",
						"cantidad" => "10",
						"devolucion" => "Opción Devolución" 
				),
				
				3 => array (
						"id_elemento" => "RTCA",
						"decripcion" => "Router Arris",
						"cantidad" => "4",
						"devolucion" => "Opción Devolución" 
				),
				
				4 => array (
						"id_elemento" => "CCC",
						"decripcion" => "Cable Cobre",
						"cantidad" => "1122",
						"devolucion" => "Opción Devolución"
				),
				
				5 => array (
						"id_elemento" => "SV1",
						"decripcion" => "Servidor  HP ProLiant MicroServer",
						"cantidad" => "5",
						"devolucion" => "Opción Devolución"
				),
				6 => array (
						"id_elemento" => "WFF",
						"decripcion" => "Antenas WIFI/LAN",
						"cantidad" => "300",
						"devolucion" => "Opción Devolución"
				),
				
				
				
				
		);
		
		
	}
	function retornarElementos() {
		$tabla = new \stdClass ();
		
		$page = $_REQUEST ['page'];
		
		$limit = $_REQUEST ['rows'];
		
		$sidx = $_REQUEST ['sidx'];
		
		$sord = $_REQUEST ['sord'];
		
		if (! $sidx)
			$sidx = 1;
		
		$filas = count ( $this->infoElementos );
		
		if ($filas > 0 && $limit > 0) {
			$total_pages = ceil ( $filas / $limit );
		} else {
			$total_pages = 0;
		}
		
		if ($page > $total_pages) {
			$page = $total_pages;
		}
		$start = $limit * $page - $limit;
		
		$tabla->page = $page;
		$tabla->total = $total_pages;
		$tabla->records = $filas;
		
		$i = 0;
		$j = 1;
		foreach ( $this->infoElementos as $row ) {
			
			$tabla->rows [$i] ['id'] = $row ['id_elemento'];
			$tabla->rows [$i] ['cell'] = array (
					$row ['id_elemento'],
					trim ( $row ['decripcion'] ), 
					trim ( $row ['cantidad'] ),
					trim ( $row ['devolucion'] )
			);
			$i ++;
		}
		
		$tabla = json_encode ( $tabla );
		
		echo $tabla;
	}
}

$miProcesador = new Elementos ( $this->sql );

?>

