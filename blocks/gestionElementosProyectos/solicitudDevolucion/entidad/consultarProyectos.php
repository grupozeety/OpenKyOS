<?php

namespace gestionElementosProyectos\solicitudDevolucion\entidad;

class Proyectos {
	var $miConfigurador;
	var $miSql;
	var $conexion;
	var $infoProyectos;
	function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->miSql = $sql;
		
		/**
		 * Infomación de Proyectos
		 */
		$this->estructurarInformacionProyectos ();
		
		/**
		 * Restornar Proyectos
		 */
		
		$this->restornarProyectos ();
	}
	function estructurarInformacionProyectos() {
		$this->infoProyectos [] = array (
				"data" => '1',
				"value" => 'Conexiones Digitales II: Sucre y Córdoba' 
		);
		
		$this->infoProyectos [] = array (
				"data" => '2',
				"value" => 'Sistema de Información y Web Services' 
		);
		
		$this->infoProyectos [] = array (
				"data" => '3',
				"value" => 'Instalación y Puesta en Servicio' 
		);
		
		$this->infoProyectos [] = array (
				"data" => '4',
				"value" => 'Componente Gestión de Proyectos' 
		);
		
		$this->infoProyectos [] = array (
				"data" => '5',
				"value" => 'Inventariar equipos portatiles' 
		);
	}
	function restornarProyectos() {
		foreach ( $this->infoProyectos as $key => $values ) {
			$keys = array (
					'value',
					'data' 
			);
			$resultado [$key] = array_intersect_key ( $this->infoProyectos [$key], array_flip ( $keys ) );
		}
		
		echo '{"suggestions":' . json_encode ( $resultado ) . '}';
	}
}

$miProcesador = new Proyectos ( $this->sql );

?>

