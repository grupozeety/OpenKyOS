<?php
namespace development\registro\funcion;

class BuscadorBloques {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	var $conexion;

	function __construct($lenguaje, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
	}

	function getBloques() {
		$this->conexion = $this->miConfigurador->fabricaConexiones->getRecursoDB ( 'estructura' );
		if (! $this->conexion) {
			error_log ( "No se conectó" );
			$resultado = false;
		} else {
				
			$cadenaSql = $this->miSql->getCadenaSql ( 'buscarBloques' );
			$resultado = $this->conexion->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		}

		return $resultado;
	}
}

$miBuscador = new BuscadorBloques ( $this->lenguaje, $this->miSQL );

$resultado = $miBuscador->getBloques ();