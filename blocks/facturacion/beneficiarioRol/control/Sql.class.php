<?php

namespace facturacion\beneficiarioRol;

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
				$cadenaSql = " SELECT id_usuario_rol ";
				$cadenaSql .= " FROM facturacion.usuario_rol ";
				$cadenaSql .= " WHERE  id_rol='" . $variable ['id_rol'] . "' ";
				$cadenaSql .= " AND  id_beneficiario='" . $variable ['id_beneficiario'] . "' ";
				break;
			
			case 'consultarBenRol' :
				$cadenaSql = " SELECT id_usuario_rol, ur.id_rol,rol.descripcion as n_rol,ur.id_beneficiario, identificacion ||' - '|| nombre ||' '|| primer_apellido as beneficiario";
				$cadenaSql .= " FROM facturacion.usuario_rol ur ";
				$cadenaSql .= " JOIN facturacion.rol ON rol.id_rol=ur.id_rol and rol.estado_registro=TRUE ";
				$cadenaSql .= " JOIN interoperacion.beneficiario_potencial bp on bp.id_beneficiario=ur.id_beneficiario and bp.estado_registro=TRUE ";
				$cadenaSql .= " WHERE ur.estado_registro=TRUE ORDER BY n_rol ASC";
				break;
			
			case 'consultarAsociacion_especifico' :
				$cadenaSql = " SELECT id_usuario_rol, ur.id_rol,rol.descripcion as n_rol,ur.id_beneficiario, identificacion ||' - '|| nombre ||' '|| primer_apellido as beneficiario";
				$cadenaSql .= " FROM facturacion.usuario_rol ur ";
				$cadenaSql .= " JOIN facturacion.rol ON rol.id_rol=ur.id_rol and rol.estado_registro=TRUE ";
				$cadenaSql .= " JOIN interoperacion.beneficiario_potencial bp on bp.id_beneficiario=ur.id_beneficiario and bp.estado_registro=TRUE ";
				$cadenaSql .= " WHERE ur.estado_registro=TRUE AND id_usuario_rol='" . $variable . "' ORDER BY n_rol ASC";
				break;
			
			case 'registrarAsociacion' :
				$cadenaSql = " INSERT INTO facturacion.usuario_rol (id_rol, id_beneficiario) ";
				$cadenaSql .= " VALUES( ";
				$cadenaSql .= " " . $variable ['id_rol'] . ",";
				$cadenaSql .= " '" . $variable ['id_beneficiario'] . "' );";
				break;
			
			case 'inhabilitarMetodo' :
				$cadenaSql = " UPDATE facturacion.usuario_rol SET  ";
				$cadenaSql .= " estado_registro='FALSE' ";
				$cadenaSql .= " WHERE id_usuario_rol='" . $variable . "' ";
				break;
			
			case 'actualizarAsociacion' :
				$cadenaSql = " UPDATE facturacion.usuario_rol SET  ";
				$cadenaSql .= " id_rol='" . $variable ['id_rol'] . "' ";
				$cadenaSql .= " WHERE id_usuario_rol='" . $variable ['id_usuario_rol'] . "' ";
				break;
			
			case 'consultarBeneficiariosPotenciales' :
				$cadenaSql = " SELECT value , data ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "(SELECT DISTINCT identificacion ||' - ('||nombre||' '||primer_apellido||' '||segundo_apellido||')' AS  value, bp.id_beneficiario  AS data ";
				$cadenaSql .= " FROM  interoperacion.beneficiario_potencial bp ";
				$cadenaSql .= " WHERE bp.estado_registro=TRUE ";
				$cadenaSql .= "     ) datos ";
				$cadenaSql .= "WHERE value ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "LIMIT 10; ";
				break;
		}
		
		return $cadenaSql;
	}
}
?>

