<?php

namespace usuarios\crearUsuario\funcion;

use usuarios\crearUsuario\funcion\redireccionar;

include_once ('redireccionar.php');
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class RegistrarIndexacionRevista {
	
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miFuncion;
	var $miSql;
	var $conexion;
	
	function __construct($lenguaje, $sql, $funcion) {
		
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miFuncion = $funcion;
	}
	
	function procesarFormulario() {
		
		var_dump("Procesar");die;
		
		
		die;

		if ($resultado) {
			redireccion::redireccionar ( 'actualizo',  $_REQUEST['docenteRegistrar']);
			exit ();
		} else {
			redireccion::redireccionar ( 'noActualizo',  $_REQUEST['docenteRegistrar'] );
			exit ();
		}
	}
	
	function resetForm() {
		foreach ( $_REQUEST as $clave => $valor ) {
			
			if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
				unset ( $_REQUEST [$clave] );
			}
		}
	}
}

$miRegistrador = new RegistrarIndexacionRevista ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();

?>
