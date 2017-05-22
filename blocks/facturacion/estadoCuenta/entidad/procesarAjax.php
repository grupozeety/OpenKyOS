<?php

namespace facturacion\estadoCuenta\entidad;

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
			
			case 'consultarFacturas' :
				
				$cadenaSql = $this->sql->getCadenaSql ( 'consultarFactura', $_REQUEST ['id_ben'] );
				$procesos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				if ($procesos != false) {
					foreach ( $procesos as $key => $valor ) {
						
						$resultadoFinal [] = array (
								'id_factura' => "<center>" . $valor ['id_factura'] . "</center>",
								'id_ciclo' => "<center>" . $valor ['id_ciclo'] . "</center>",
								'total' => "<center> $ " . number_format ( $valor ['total_factura'], 2, ',', '.' ) . "</center>",
								'estado_factura' => "<center>" . $valor ['estado_factura'] . "</center>" 
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
			
			case 'consultarPagos' :
				
				$cadenaSql = $this->sql->getCadenaSql ( 'consultarPagos', $_REQUEST ['id_ben'] );
				$procesos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				if ($procesos != false) {
					foreach ( $procesos as $key => $valor ) {
						
						$resultadoFinal [] = array (
								'id_factura' => "<center>" . $valor ['id_factura'] . "</center>",
								'id_pago' => "<center>" . $valor ['id_pago'] . "</center>",
								'total' => "<center> $ " . number_format ( $valor ['total_pagado'], 2, ',', '.' ) . "</center>",
								'total_factura' => "<center> $ " . number_format ( $valor ['valor_pagado'], 2, ',', '.' ) . "</center>",
								'total_abono' => "<center> $ " . number_format ( $valor ['abono_adicional'], 2, ',', '.' ) . "</center>",
								'fecha_pago' => "<center>" . $valor ['fecha_registro'] . "</center>",
								'cajero' => "<center>" . $valor ['cajero'] . "</center>"
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
			
			case 'inhabilitarMetodo' :
				$conexion = "interoperacion";
				$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
				
				$cadenaSql = $this->sql->getCadenaSql ( 'inhabilitarFactura', $_REQUEST ['valor'] );
				$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "actualizar" );
				
				echo $resultado;
				break;
			
			case 'redireccionar' :
				include_once ("core/builder/FormularioHtml.class.php");
				
				$miFormulario = new \FormularioHtml ();
				
				if (! isset ( $_REQUEST ['tiempo'] )) {
					$_REQUEST ['tiempo'] = time ();
				}
				// Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php
				
				$_REQUEST ['ready'] = true;
				
				$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $_REQUEST ['valor'] . $_REQUEST ['id'] );
				
				$enlace = $_REQUEST ['directorio'] . '=' . $valorCodificado;
				
				echo json_encode ( $enlace );
				break;
			
			case 'redireccionarPago' :
				include_once ("core/builder/FormularioHtml.class.php");
				
				$miFormulario = new \FormularioHtml ();
				
				if (! isset ( $_REQUEST ['tiempo'] )) {
					$_REQUEST ['tiempo'] = time ();
				}
				// Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php
				
				$_REQUEST ['ready'] = true;
				
				$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $_REQUEST ['valor'] . $_REQUEST ['id'] );
				
				$enlace = $_REQUEST ['directorio'] . '=' . $valorCodificado;
				
				echo json_encode ( $enlace );
				break;
		}
	}
}

$miProcesarAjax = new procesarAjax ( $this->sql );
exit ();
?>
