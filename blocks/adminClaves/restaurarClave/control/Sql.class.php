<?php

namespace cambioClave;

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
	public function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
	}
	public function getCadenaSql($tipo, $variable = '') {
		
		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
		switch ($tipo) {
			
			/**
			 * Clausulas específicas
			 */
			
			case 'consultarInformacionApi' :
				$cadenaSql = " SELECT componente, host, usuario, password, ruta_cookie ";
				$cadenaSql .= " FROM parametros.api_data";
				$cadenaSql .= " WHERE componente ='" . $variable . "';";
				break;
			
			case 'registrarRecuperacionClave' :
				$cadenaSql = " INSERT INTO parametros.restaurar_clave";
				$cadenaSql .= " (usuario, token) VALUES";
				$cadenaSql .= "('" . $variable ['usuario'] . "',";
				$cadenaSql .= "'" . $variable ['token'] . "')";
				break;
			
			case 'consultarInformacionRestauracion' :
				$cadenaSql = " SELECT usuario, token, estado_registro";
				$cadenaSql .= " FROM parametros.restaurar_clave";
				$cadenaSql .= " WHERE usuario ='" . $variable ['usuario'] . "'";
				$cadenaSql .= " AND token ='" . $variable ['token'] . "'";
				$cadenaSql .= " AND estado_registro =TRUE";
				break;
		
			case 'actualizarecuperacionClave' :
				$cadenaSql = " UPDATE parametros.restaurar_clave SET estado_registro=FALSE";
				$cadenaSql .= " WHERE usuario ='" . $variable ['usuario'] . "'";
				$cadenaSql .= " AND token ='" . $variable ['token'] . "'";
				$cadenaSql .= " AND estado_registro =TRUE";
				break;
		}
		
		return $cadenaSql;
	}
}
?>

