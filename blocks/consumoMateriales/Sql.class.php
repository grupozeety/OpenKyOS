<?php

namespace consumoMateriales;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	var $miConfigurador;
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
	}
	function getCadenaSql($tipo, $variable = "") {
		
		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
		switch ($tipo) {
			
			/**
			 * Clausulas genéricas.
			 * se espera que estén en todos los formularios
			 * que utilicen esta plantilla
			 */
			case "iniciarTransaccion" :
				$cadenaSql = "START TRANSACTION";
				break;
			
			case "finalizarTransaccion" :
				$cadenaSql = "COMMIT";
				break;
			
			case "cancelarTransaccion" :
				$cadenaSql = "ROLLBACK";
				break;
			
			case "eliminarTemp" :
				
				$cadenaSql = "DELETE ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_sesion = '" . $variable . "' ";
				break;
			
			case "insertarTemp" :
				$cadenaSql = "INSERT INTO ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "( ";
				$cadenaSql .= "id_sesion, ";
				$cadenaSql .= "formulario, ";
				$cadenaSql .= "campo, ";
				$cadenaSql .= "valor, ";
				$cadenaSql .= "fecha ";
				$cadenaSql .= ") ";
				$cadenaSql .= "VALUES ";
				
				foreach ( $_REQUEST as $clave => $valor ) {
					$cadenaSql .= "( ";
					$cadenaSql .= "'" . $idSesion . "', ";
					$cadenaSql .= "'" . $variable ['formulario'] . "', ";
					$cadenaSql .= "'" . $clave . "', ";
					$cadenaSql .= "'" . $valor . "', ";
					$cadenaSql .= "'" . $variable ['fecha'] . "' ";
					$cadenaSql .= "),";
				}
				
				$cadenaSql = substr ( $cadenaSql, 0, (strlen ( $cadenaSql ) - 1) );
				break;
			
			case "rescatarTemp" :
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_sesion, ";
				$cadenaSql .= "formulario, ";
				$cadenaSql .= "campo, ";
				$cadenaSql .= "valor, ";
				$cadenaSql .= "fecha ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_sesion='" . $idSesion . "'";
				break;
			
			/* Consultas del desarrollo */
			
				
			case "registrarConsumo" :
				
				$cadenaSql = "INSERT INTO interoperacion.consumo_material(";
				$cadenaSql .= "nombre,";
				$cadenaSql .= "orden_trabajo,";
				$cadenaSql .= "descripcion,";
				$cadenaSql .= "proyecto,";
				$cadenaSql .= "nombre_material,";
				$cadenaSql .= "cantidad_asignada,";
				$cadenaSql .= "consumo,";
				$cadenaSql .= "porcentaje_consumo,";
				$cadenaSql .= "geolocalizacion";
				$cadenaSql .= ") values ";
				
				foreach ( $variable as $clave => $valor ) {
					
					$cadenaSql .= "(";
					$cadenaSql .= "'" . $valor['name'] . "',";
					$cadenaSql .= "'" . $valor['ordenTrabajo'] . "',";
					$cadenaSql .= "'" . $valor['descripcion'] . "',";
					$cadenaSql .= "'" . $valor['proyecto'] . "',";
					$cadenaSql .= "'" . $valor['material'] . "',";
					$cadenaSql .= "'" . $valor['asignada'] . "',";
					$cadenaSql .= "'" . $valor['consume'] . "',";
					$cadenaSql .= "'" . $valor['porcentajecons'] . "',";
					$cadenaSql .= "'" . $valor['geolocalizacion'] . "'";
					$cadenaSql .= "),";
				}
				
				$cadenaSql = substr ( $cadenaSql, 0, (strlen ( $cadenaSql ) - 1) );
				
				break;
				
			case "actualizarConsumo" :
				
				$cadenaSql = "UPDATE interoperacion.consumo_material ";
				$cadenaSql .= "SET ";
				$cadenaSql .= "estado_registro=FALSE ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "nombre ";
				$cadenaSql .= "IN ";
				$cadenaSql .= "(";
				
				foreach ( $variable as $clave => $valor ) {
						
					$cadenaSql .= "'" . $valor['name'] . "',";
				
				}
						
				$cadenaSql = substr ( $cadenaSql, 0, (strlen ( $cadenaSql ) - 1) );
				$cadenaSql .= ")";
				
				break;
				
			case "obtenerConsumo" :
				
				$cadenaSql = "SELECT consumo,geolocalizacion, porcentaje_consumo from interoperacion.consumo_material where nombre='" . $variable ."' AND estado_registro=TRUE;";
				
				break;
		
		}
		
		return $cadenaSql;
	}
}

?>
