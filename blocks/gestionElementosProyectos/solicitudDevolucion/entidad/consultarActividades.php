<?php

namespace gestionElementosProyectos\solicitudDevolucion\entidad;

class Actividades {
	var $miConfigurador;
	var $miSql;
	var $conexion;
	var $infoActividades;
	function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->miSql = $sql;
		
		/**
		 * Infomación de Proyectos
		 */
		$this->estructurarInformacionActividades ();
		
		/**
		 * Restornar Proyectos
		 */
		
		$this->retornarActividades ();
	}
	function estructurarInformacionActividades() {
		$arrayActividades [1] = array (
				0 => array (
						"id_actividad" => "1",
						"decripcion" => "Actividad 1 " 
				),
				
				1 => array (
						"id_actividad" => "2",
						"decripcion" => "Actividad 2 " 
				),
				
				3 => array (
						"id_actividad" => "3",
						"decripcion" => "Actividad 3 " 
				) 
		);
		
		$arrayActividades [2] = array (
				0 => array (
						"id_actividad" => "4",
						"decripcion" => "Actividad 4 " 
				),
				
				1 => array (
						"id_actividad" => "5",
						"decripcion" => "Actividad 5 " 
				),
				
				3 => array (
						"id_actividad" => "6",
						"decripcion" => "Actividad 7 " 
				) 
		);
		
		$arrayActividades [3] = array (
				0 => array (
						"id_actividad" => "7",
						"decripcion" => "Actividad 7 " 
				) 
		);
		
		$arrayActividades [4] = array (
				0 => array (
						"id_actividad" => "9",
						"decripcion" => "Actividad 9 " 
				) 
		);
		
		$arrayActividades [5] = array (
				0 => array (
						"id_actividad" => "10",
						"decripcion" => "Actividad 10 " 
				),
				1 => array (
						"id_actividad" => "11",
						"decripcion" => "Actividad 11 " 
				),
				2 => array (
						"id_actividad" => "12",
						"decripcion" => "Actividad 12 " 
				) 
		);
		
		$this->infoActividades = $arrayActividades [$_REQUEST ['Proyecto']];
	}
	function retornarActividades() {
		$tabla = new \stdClass ();
		
		$page = $_REQUEST ['page'];
		
		$limit = $_REQUEST ['rows'];
		
		$sidx = $_REQUEST ['sidx'];
		
		$sord = $_REQUEST ['sord'];
		
		
		if (! $sidx)
			$sidx = 1;
		
		$filas = count ( $this->infoActividades );
		
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
		foreach ( $this->infoActividades as $row ) {
			
			$tabla->rows [$i] ['id'] = $row ['id_actividad'];
			$tabla->rows [$i] ['cell'] = array (
					$row ['id_actividad'],
					trim ( $row ['decripcion'] ) 
			);
			$i ++;
		}
		
		$tabla = json_encode ( $tabla );
		
		echo $tabla;
	}
}

$miProcesador = new Actividades ( $this->sql );

?>

