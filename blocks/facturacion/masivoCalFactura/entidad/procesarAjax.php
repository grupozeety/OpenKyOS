<?php

namespace facturacion\masivoCalFactura\entidad;

class procesarAjax {
	public $miConfigurador;
	public $sql;
	public function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
		
		$this->sql = $sql;
		// Conexion a Base de Datos
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		switch ($_REQUEST ['funcion']) {
			
			case 'consultaBeneficiarios' :
				
				$cadena = '';
				
				$cadenaSql = $this->sql->getCadenaSql ( 'consultarBeneficiariosPotenciales', $cadena );
				
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
			
			case 'consultarRoles' :
				
				$cadenaSql = $this->sql->getCadenaSql ( 'consultarRolUsuario' );
				$procesos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				if ($procesos) {
					foreach ( $procesos as $key => $valor ) {
						
						$resultadoFinal [] = array (
								'beneficiario' => "<center>" . $valor ['beneficiario'] . "</center>",
								'rol' => "<center>" . $valor ['descripcion_rol'] . "</center>",
								'tipo_periodo' => "<center></center>",
								'inicio_periodo' => "<center></center>", 
						);
					}
					
					$total = count ( $resultadoFinal );
					$resultado = json_encode ( $resultadoFinal );
					$resultado = '{
                                "recordsTotal":' . $total . ',
                                "recordsFiltered":' . $total . ',
                                "data":' . $resultado . '}';
				} else {
					$resultado = '{
                                "recordsTotal":0 ,
                                "recordsFiltered":0 ,
                                "data": 0 }';
				}
				echo $resultado;
				
				break;
		}
	}
}

$miProcesarAjax = new procesarAjax ( $this->sql );
exit ();
?>
