<?php

namespace development\gestionBloques\funcion;

class procesarAjax {
	var $miConfigurador;
	var $sql;
	function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
		
		$this->sql = $sql;
		
		switch ($_REQUEST ['funcion']) {
			
			case 'consultarBloques' :
				
				include ('consultarBloque.php');
				
				break;
			case 'crearBloque' :
				
				include ('crearBloque.php');
				
				break;
			
			case 'editarBloque' :
				
				include ('editarBloque.php');
				
				break;
			
			case 'eliminarBloque' :
				
				include ('eliminarBloque.php');
				
				break;
			
			case 'consultarPlugins' :
				
				include ('consultarPlugins.php');
				
				break;
			
			case 'adicionPlugin' :
				
				include ('adicionarPlugins.php');
				
				break;
			
			case 'eliminarPlugin' :
				
				include ('eliminarPlugins.php');
				
				break;
		}
	}
}

$miProcesarAjax = new procesarAjax ( $this->sql );

?>