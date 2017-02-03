<?php

namespace facturacion\rolFactura;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

include_once "core/manager/Configurador.class.php";
include_once "core/connection/Sql.class.php";

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	public $miConfigurador;
	public function getCadenaSql($tipo, $variable = '') {
		
		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
		switch ($tipo) {
			
			case 'consultarRoles' :
				$cadenaSql = " SELECT id_rol, descripcion ";
				$cadenaSql .= " FROM facturacion.rol ";
				$cadenaSql .= " WHERE estado_registro=TRUE AND id_rol!=1ORDER BY descripcion ASC";
				break;
			
			case 'consultarMetodos_especifico' :
				$cadenaSql = " SELECT id_rol, descripcion ";
				$cadenaSql .= " FROM facturacion.rol ";
				$cadenaSql .= " WHERE descripcion='" . $variable . "' ";
				break;
			
			case 'consultarRol_especifico' :
				$cadenaSql = " SELECT id_rol, descripcion ";
				$cadenaSql .= " FROM facturacion.rol ";
				$cadenaSql .= " WHERE id_rol='" . $variable . "' ";
				break;
			
			case 'registrarRol' :
				$cadenaSql = " INSERT INTO facturacion.rol (descripcion) ";
				$cadenaSql .= " VALUES( ";
				$cadenaSql .= " '" . $variable ['descripcion'] . "');";
				break;
			
			case 'inhabilitarRol' :
				$cadenaSql = " UPDATE facturacion.rol SET  ";
				$cadenaSql .= " estado_registro='FALSE' ";
				$cadenaSql .= " WHERE id_rol='" . $variable . "' ";
				break;
			
			case 'actualizarRol' :
				$cadenaSql = " UPDATE facturacion.rol SET  ";
				$cadenaSql .= " descripcion='" . $variable ['descripcion'] . "' ";
				$cadenaSql .= " WHERE id_rol='" . $variable ['id_rol'] . "' ";
				break;
		}
		
		return $cadenaSql;
	}
}
?>

