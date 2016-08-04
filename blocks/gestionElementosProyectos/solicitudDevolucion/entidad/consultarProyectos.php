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
		
		
		
		
		
		
	}
	
	function estructurarInformacionProyectos() {
		
		

	}
	
	
	
	function consultarProyectos() {
		
		// Aquí va la lógica de procesamiento
		
		// Al final se ejecuta la redirección la cual pasará el control a otra página
	}
}

$miProcesador = new Proyectos ( $this->sql );

?>

