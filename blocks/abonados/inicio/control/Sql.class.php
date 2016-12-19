<?php

namespace cambioClave;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

include_once "core/manager/Configurador.class.php";
include_once "core/connection/Sql.class.php";
include_once "core/auth/SesionSso.class.php";

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	public $miConfigurador;
	public $miSesionSso;
	public function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miSesionSso = \SesionSso::singleton ();
	}
	public function getCadenaSql($tipo, $variable = '') {
		$info_usuario = $this->miSesionSso->getParametrosSesionAbierta ();
		
		foreach ( $info_usuario ['description'] as $key => $rol ) {
			
			$info_usuario ['rol'] [] = $rol;
		}
		
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
			
			case 'consultarColor' :
				$cadenaSql = " SELECT color1, color2, color3";
				$cadenaSql .= " FROM parametros.color_usuario";
				$cadenaSql .= " WHERE identificacion ='" . $info_usuario ['uid'] [0] . "';";
				break;
			
			case 'guardarColor' :
				$cadenaSql = " INSERT INTO  parametros.color_usuario (identificacion,color1, color2, color3) VALUES";
				$cadenaSql .= " (";
				$cadenaSql .= "'" . $info_usuario ['uid'] [0] . "',";
				$cadenaSql .= "'" . $variable ['color1'] . "',";
				$cadenaSql .= "'" . $variable ['color2'] . "',";
				$cadenaSql .= "'" . $variable ['color3'] . "'";
				$cadenaSql .= ")";
				break;
			
			case 'actualizarColor' :
				$cadenaSql = " UPDATE parametros.color_usuario SET";
				$cadenaSql .= " color1=" . "'" . $variable ['color1'] . "',";
				$cadenaSql .= " color2=" . "'" . $variable ['color2'] . "',";
				$cadenaSql .= " color3=" . "'" . $variable ['color3'] . "'";
				$cadenaSql .= " WHERE identificacion ='" . $info_usuario ['uid'] [0] . "';";
				break;
		}
		
		return $cadenaSql;
	}
}
?>

