<?php

namespace facturacion\configuracionFactura;

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
			
			case 'parametroRol' :
				$cadenaSql = " SELECT id_rol, rol.descripcion , id_valor ";
				$cadenaSql .= " FROM facturacion.rol ";
				$cadenaSql .= " LEFT JOIN facturacion.parametros_generales pg on pg.id_valor=id_rol and pg.descripcion='rol' AND pg.estado_registro=TRUE ";
				$cadenaSql .= " WHERE rol.estado_registro=TRUE ORDER BY id_valor ASC;";
				break;
			
			case 'actualizarMetodo' :
				$cadenaSql = " UPDATE facturacion.metodos SET  ";
				$cadenaSql .= " id_regla='" . $variable ['id_regla'] . "', ";
				$cadenaSql .= " id_rol='" . $variable ['id_rol'] . "' ";
				$cadenaSql .= " WHERE id_metodos='" . $variable ['id_metodo'] . "' ";
				break;
		}
		
		return $cadenaSql;
	}
}
?>

