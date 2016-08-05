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
			
			case 'consultarConexionesDB' :
				
				include ('consultaConexiones.php');
				
				break;
			case 'crearConexion' :
				
				include ('crearConexion.php');
				
				break;
			
			case 'editarConexion' :
				
				include ('editarConexion.php');
				
				break;
			
			case 'eliminarConexion' :
				
				include ('eliminarConexion.php');
				
				break;
			
		}
	}
}

$miProcesarAjax = new procesarAjax ( $this->sql );

?>