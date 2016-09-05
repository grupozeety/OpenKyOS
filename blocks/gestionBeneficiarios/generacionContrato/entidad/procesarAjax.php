<?php
namespace gestionBeneficiarios\generacionContrato\entidad;
class procesarAjax {
	var $miConfigurador;
	var $sql;
	function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
		
		$this->sql = $sql;

		
		switch ($_REQUEST ['funcion']) {
			
			case 'ejemploFuncion' :
				
				/**
				 * Código de Logica Procesar Ajax 
				 */
				
				break;
			
		}
	}
}

$miProcesarAjax = new procesarAjax ( $this->sql );

?>
