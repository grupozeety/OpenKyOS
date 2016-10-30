<?php

namespace gestionComisionamiento\archivosAlfresco\entidad;

class procesarAjax {
	var $miConfigurador;
	var $sql;
	function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
		
		$this->sql = $sql;
		
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		switch ($_REQUEST ['funcion']) {
			
			case 'consultaBeneficiarios' :
				
				$cadenaSql = $this->sql->getCadenaSql ( 'consultarBeneficiariosPotenciales' );
				
				$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				foreach ( $resultadoItems as $key => $values ) {
					$keys = array (
							'value',
							'data' 
					);
					$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
				}
				echo '{"suggestions":' . json_encode ( $resultado ) . '}';
				
				break;
		}
	}
}

$miProcesarAjax = new procesarAjax ( $this->sql );

?>
