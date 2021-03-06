<?php

namespace gui\menuPrincipal;

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
	public $miSesionSso;
	
	public function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miSesionSso = \SesionSso::singleton ();
	}
	public function getCadenaSql($tipo, $variable = "") {
		
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
			 * Clausulas Menú.
			 * Mediante estas sentencias se generan los diferentes menus del aplicativo
			 */
			
			case "consultarDatosMenu" :
				$cadenaSql = " SELECT DISTINCT";
				$cadenaSql .= " enl.id_enlace AS id_enlace,";
				$cadenaSql .= " enl.id_menu AS menu,";
				$cadenaSql .= " enl.icon AS icon,";
				$cadenaSql .= " men.descripcion AS nombre_menu,";
				$cadenaSql .= " enl.titulo AS titulo_enlace,";
				$cadenaSql .= " enl.columna AS columna,";
				$cadenaSql .= " enl.submenu AS submenu,";
				$cadenaSql .= " enl.orden AS orden,";
				$cadenaSql .= " ten.nombre AS tipo_enlace,";
				$cadenaSql .= " cen.nombre AS clase_enlace,";
				$cadenaSql .= " enl.enlace AS enlace,";
				$cadenaSql .= " enl.parametros AS parametros,";
				$cadenaSql .= " enl.acceso_rapido AS rapido";
				$cadenaSql .= " FROM gestion_menu.menu_rol_enlace as rol_enlace";
				$cadenaSql .= " INNER JOIN gestion_menu.menu_enlace AS enl ON enl.id_enlace = rol_enlace.id_enlace";
				$cadenaSql .= " INNER JOIN gestion_menu.menu_tipo_enlace AS ten ON ten.id_tipo_enlace = enl.id_tipo_enlace";
				$cadenaSql .= " INNER JOIN gestion_menu.menu_clase_enlace AS cen ON cen.id_clase_enlace = enl.id_clase_enlace";
				$cadenaSql .= " INNER JOIN gestion_menu.rol AS rol ON rol.id_rol = rol_enlace.id_rol";
				$cadenaSql .= " INNER JOIN gestion_menu.menu AS men ON men.id_menu = enl.id_menu";
				$cadenaSql .= " WHERE";
				$i = 0;
				foreach ( $variable as $rol ) {
					if ($i == 0) {
						$cadenaSql .= " rol.rol = '" . $rol . "'";
						$i ++;
					} else {
						$cadenaSql .= " OR rol.rol = '" . $rol . "'";
						$i ++;
					}
				}
				$cadenaSql .= " AND enl.estado='TRUE' AND  men.estado='TRUE' ";
				$cadenaSql .= " ORDER BY enl.id_menu, enl.columna, enl.orden";
				$cadenaSql .= " ;";
				
				break;
			
			case "accesoRapido" :
				$cadenaSql = "SELECT documento, beneficiario ";
				$cadenaSql .= " FROM";
				$cadenaSql .= " parametros.usuario_beneficiario";
				$cadenaSql .= " WHERE";
				$cadenaSql .= " usuario='" . $variable . "'";
				break;
			
			case 'consultarColor' :
				$cadenaSql = " SELECT color1, color2, color3";
				$cadenaSql .= " FROM parametros.color_usuario";
				$cadenaSql .= " WHERE identificacion ='" . $info_usuario ['uid'] [0] . "';";
				break;
		}
		
		return $cadenaSql;
	}
}
?>