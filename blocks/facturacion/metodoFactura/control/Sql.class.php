<?php

namespace facturacion\metodoFactura;

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
				$cadenaSql = " SELECT id_rol, descripcion ";
				$cadenaSql .= " FROM facturacion.rol ";
				$cadenaSql .= " WHERE estado_registro=TRUE;";
				break;
			
			case 'parametroRegla' :
				$cadenaSql = " SELECT regla.id_regla , identificador ||' - '|| descripcion as valor ";
				$cadenaSql .= " FROM facturacion.regla ";
				$cadenaSql .= " WHERE  regla.estado_registro=TRUE ";
				break;
			
			case 'consultarAsociacion' :
				$cadenaSql = " SELECT id_metodos ";
				$cadenaSql .= " FROM facturacion.metodos ";
				$cadenaSql .= " WHERE  id_rol='" . $variable ['id_rol'] . "' ";
				$cadenaSql .= " AND  id_regla='" . $variable ['id_regla'] . "' ";
				break;
			
			case 'consultarMetodos' :
				$cadenaSql = " SELECT id_metodos, metodos.id_rol,rol.descripcion as n_rol,metodos.id_regla , regla.descripcion as n_regla ";
				$cadenaSql .= " FROM facturacion.metodos  ";
				$cadenaSql .= " JOIN facturacion.rol ON rol.id_rol=metodos.id_rol and rol.estado_registro=TRUE ";
				$cadenaSql .= " JOIN facturacion.regla on regla.id_regla=metodos.id_regla and regla.estado_registro=TRUE ";
				$cadenaSql .= " WHERE metodos.estado_registro=TRUE ORDER BY n_rol ASC";
				break;
			
			case 'consultarMetodos_especifico' :
				$cadenaSql = " SELECT id_metodos, metodos.id_rol as rol,rol.descripcion as n_rol,metodos.id_regla as regla , regla.descripcion as n_regla ";
				$cadenaSql .= " FROM facturacion.metodos  ";
				$cadenaSql .= " JOIN facturacion.rol ON rol.id_rol=metodos.id_rol and rol.estado_registro=TRUE ";
				$cadenaSql .= " JOIN facturacion.regla on regla.id_regla=metodos.id_regla and regla.estado_registro=TRUE ";
				$cadenaSql .= " WHERE metodos.estado_registro=TRUE AND id_metodos='" . $variable . "' ORDER BY n_rol ASC";
				break;
			
			case 'registrarMetodo' :
				$cadenaSql = " INSERT INTO facturacion.metodos (id_rol, id_regla) ";
				$cadenaSql .= " VALUES( ";
				$cadenaSql .= " " . $variable ['id_rol'] . ",";
				$cadenaSql .= " " . $variable ['id_regla'] . " );";
				break;
			
			case 'inhabilitarMetodo' :
				$cadenaSql = " UPDATE facturacion.metodos SET  ";
				$cadenaSql .= " estado_registro='FALSE' ";
				$cadenaSql .= " WHERE id_metodos='" . $variable . "' ";
				break;
			
			case 'actualizarMetodo' :
				$cadenaSql = " UPDATE facturacion.metodos SET  ";
				$cadenaSql .= " id_regla='" . $variable['id_regla'] . "', ";
				$cadenaSql .= " id_rol='" . $variable['id_rol'] . "' ";
				$cadenaSql .= " WHERE id_metodos='" . $variable['id_metodo'] . "' ";
				break;
		}
		
		return $cadenaSql;
	}
}
?>

