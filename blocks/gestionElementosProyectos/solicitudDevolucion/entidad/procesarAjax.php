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
				
				include_once ("consultarProyectos.php");
				
				break;
			
			case 'consultarActividades' :
				
				include_once ("consultarActividades.php");
				
				break;
				
				
			default :
				var_dump ( $_REQUEST );
				
				break;
		}
	}
}

$miProcesarAjax = new procesarAjax ( $this->sql );

?>
