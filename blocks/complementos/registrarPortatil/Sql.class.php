<?php

namespace complementos\registrarPortatil;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

/**
 * IMPORTANTE: Se recomienda que no se borren registros.
 * Utilizar mecanismos para - independiente del motor de bases de datos,
 * poder realizar rollbacks gestionados por el aplicativo.
 */
class Sql extends \Sql {
	var $miConfigurador;
	function getCadenaSql($tipo, $variable = '') {		
		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
		return $this->$tipo();
	}
	
	function nombresColumnas() {
		$cadenaSql = 'INSERT INTO ';
		$cadenaSql .= $prefijo . 'pagina ';
		$cadenaSql .= '( ';
		$cadenaSql .= 'nombre,';
		$cadenaSql .= 'descripcion,';
		$cadenaSql .= 'modulo,';
		$cadenaSql .= 'nivel,';
		$cadenaSql .= 'parametro';
		$cadenaSql .= ') ';
		$cadenaSql .= 'VALUES ';
		$cadenaSql .= '( ';
		$cadenaSql .= '\'' . $_REQUEST ['nombrePagina'] . '\', ';
		$cadenaSql .= '\'' . $_REQUEST ['descripcionPagina'] . '\', ';
		$cadenaSql .= '\'' . $_REQUEST ['moduloPagina'] . '\', ';
		$cadenaSql .= $_REQUEST ['nivelPagina'] . ', ';
		$cadenaSql .= '\'' . $_REQUEST ['parametroPagina'] . '\'';
		$cadenaSql .= ') ';
		return $cadenaSQL;
	}
	function buscarEquipos() {
		$cadenaSql = 'SELECT ';
		$cadenaSql .= 'id_interno_bodega, ';
		$cadenaSql .= 'serial,';
		$cadenaSql .= 'marca,';
		$cadenaSql .= 'modelo ';
		$cadenaSql .= 'FROM ';
		$cadenaSql .= 'politecnica_portatil ';
		$cadenaSql .= 'LIMIT ' . $_REQUEST ['length'] . ' ';
		$cadenaSql .= 'OFFSET ' . $_REQUEST ['start'];
		return $cadenaSql;
	}
	function totalEquipos() {
		$cadenaSql = 'SELECT ';
		$cadenaSql .= 'COUNT(*) ';
		$cadenaSql .= 'FROM ';
		$cadenaSql .= 'politecnica_portatil ';
		return $cadenaSql;
	}
}
?>
