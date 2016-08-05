<?php

namespace development\gestionConexiones;

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
	function getCadenaSql($tipo, $variable = '') {
		
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
			case 'consultarConexiones' :
				
				$cadenaSql = " SELECT * ";
				$cadenaSql .= " FROM " . $prefijo . "dbms ";
				$cadenaSql .= " WHERE idconexion > 1 ";
				$cadenaSql .= " ORDER BY  idconexion " . $_REQUEST ['sord'] . " ;";
				break;
			
			case 'insertarConexion' :
				$cadenaSql = "INSERT INTO ";
				$cadenaSql .= $prefijo . "dbms ";
				$cadenaSql .= "(";
				$cadenaSql .= "nombre , ";
				$cadenaSql .= "dbms , ";
				$cadenaSql .= "servidor, ";
				$cadenaSql .= "puerto, ";
				$cadenaSql .= "conexionssh, ";
				$cadenaSql .= "db, ";
				$cadenaSql .= "esquema, ";
				$cadenaSql .= "usuario, ";
				$cadenaSql .= "password ";
				$cadenaSql .= ") ";
				$cadenaSql .= "VALUES ";
				$cadenaSql .= "( ";
				$cadenaSql .= "'" . $_REQUEST ['nombre'] . "', ";
				$cadenaSql .= "'" . $_REQUEST ['dbms'] . "',";
				$cadenaSql .= "'" . $_REQUEST ['host'] . "',";
				$cadenaSql .= "'" . $_REQUEST ['puerto'] . "',";
				$cadenaSql .= "'',";
				$cadenaSql .= "'" . $_REQUEST ['namedb'] . "',";
				$cadenaSql .= "'" . $_REQUEST ['esquemadb'] . "',";
				$cadenaSql .= "'" . $_REQUEST ['usuario'] . "',";
				$cadenaSql .= "'" . $_REQUEST ['contrasena'] . "' ";
				$cadenaSql .= ")";
				
				break;
			
			case 'actualizarInformacionConexion' :
				
				$cadenaSql = " UPDATE " . $prefijo . "dbms";
				$cadenaSql .= " SET nombre='" . $_REQUEST ['nombre'] . "',";
				$cadenaSql .= " dbms='" . $_REQUEST ['dbms'] . "',";
				$cadenaSql .= " servidor='" . $_REQUEST ['host'] . "',";
				$cadenaSql .= " puerto='" . $_REQUEST ['puerto'] . "',";
				$cadenaSql .= " conexionssh='', ";
				$cadenaSql .= " db='" . $_REQUEST ['namedb'] . "', ";
				$cadenaSql .= " esquema='" . $_REQUEST ['esquemadb'] . "',";
				$cadenaSql .= " usuario='" . $_REQUEST ['usuario'] . "',";
				$cadenaSql .= " password='" . $_REQUEST ['contrasena'] . "'";
				$cadenaSql .= " WHERE idconexion='" . $_REQUEST ['id'] . "';";
				break;
			
			case 'eliminarInformacionConexion' :
				$cadenaSql = " DELETE FROM " . $prefijo . "dbms";
				$cadenaSql .= " WHERE idconexion='" . $_REQUEST ['id'] . "';";
				break;
		}
		
		return $cadenaSql;
	}
}
?>
