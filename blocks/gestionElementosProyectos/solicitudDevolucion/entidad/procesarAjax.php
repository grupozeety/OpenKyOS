<?php

namespace gestionElementosProyectos\solicitudDevolucion\entidad;

class procesarAjax {
	var $miConfigurador;
	var $sql;
	function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
		
		$this->sql = $sql;
		
		switch ($_REQUEST ['funcion']) {
			
			case 'consultarProyectos' :
				
				/**
				 * Código de Logica Procesar Ajax
				 */
				
				include_once ("consultarProyectos.php");
				
				break;
		}
	}
}

$miProcesarAjax = new procesarAjax ( $this->sql );

?>
