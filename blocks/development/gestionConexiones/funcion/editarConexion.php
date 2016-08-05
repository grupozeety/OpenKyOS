<?php

namespace development\gestionConexiones\funcion;

class EditarConexion {
	var $miConfigurador;
	var $miSql;
	var $conexion;
	var $encriptador;
	function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->encriptador = new \Encriptador ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
		
		/**
		 * Procesar Actualización Conexión
		 */
		
		$this->procesarEdicionConexionSql ();
	}
	function procesarEdicionConexionSql() {
		foreach ( $_REQUEST as $key => $valor )
			$_REQUEST [$key] = str_replace ( '\_', '_', $valor );
		
		$_REQUEST ["contrasena"] = $this->encriptador->codificar ( $_REQUEST ["contrasena"] );
		
		$this->conexion = $this->miConfigurador->fabricaConexiones->getRecursoDB ( 'estructura' );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarInformacionConexion' );
		$resultado = $this->conexion->ejecutarAcceso ( $cadenaSql, 'acceso' );
		
		return true;
	}
}

$miRegistrador = new EditarConexion ( $this->sql );

?>