<?php
namespace reportes\porcentajeConsumoMateriales\entidad;
class procesarAjax {
	var $miConfigurador;
	var $sql;
	function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
		
		$this->sql = $sql;

		
		switch ($_REQUEST ['funcion']) {
			
			case 'obtenerProyectos' :
				
				$conexion = "interoperacion";
				$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
				
				$cadenaSql = $this->sql->getCadenaSql ( 'obtenerProyectos');
				$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				echo json_encode ( $resultadoItems );
				
				break;
			
		}
	}
}

$miProcesarAjax = new procesarAjax ( $this->sql );

?>
