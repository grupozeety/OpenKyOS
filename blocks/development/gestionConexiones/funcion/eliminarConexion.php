<?php

namespace development\gestionConexiones\funcion;

class EliminarConexion {
	var $miConfigurador;
	var $miSql;
	var $conexion;
	function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
		
		/**
		 * Procesar Eliminar Conexion
		 */
		$this->procesarEliminarConexionql ();
	}
	function procesarEliminarConexionql() {
		$this->conexion = $this->miConfigurador->fabricaConexiones->getRecursoDB ( 'estructura' );
		/**
		 * Datos Actuales del Bloque
		 */
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'eliminarInformacionConexion' );
		$eliminarConexion = $this->conexion->ejecutarAcceso ( $cadenaSql, 'acceso' );
		
		return true;
	}
}

$miRegistrador = new EliminarConexion ( $this->sql );

?>