<?php

include_once ("core/auth/SesionSso.class.php");

class VerificarSesion {
	var $miSesionSso;
	
	function __construct() {
		$this->miSesionSso = \SesionSSO::singleton();
	}
	
	function procesarFormulario() {
		$respuesta = $this->miSesionSso->verificarSesionAbierta();
		return $respuesta;
	}
}

$miProcesador = new VerificarSesion ();
$respuesta = $miProcesador->procesarFormulario();
?>