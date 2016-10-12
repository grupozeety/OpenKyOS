<?php

namespace cabecera\funcion;

use cabecera\funcion\redireccionar;

include_once ('redireccionar.php');
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class Registrar {
	
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
		
		$cabecera = array();

		$cabecera['codigo_cabecera'] = $_REQUEST['codigo_cabecera'];
		$cabecera['descripcion'] = $_REQUEST['descripcion'];
		$departamento = explode(" ", $_REQUEST['departamento']);
		$cabecera['departamento'] = $departamento[0]; 
		$municipio = explode(" ", $_REQUEST['municipio']);
		$cabecera['municipio'] = $municipio[0];
		$cabecera['urbanizacion'] = $_REQUEST['id_urbanizacion'];
		$cabecera['id_urbanizacion'] = $_REQUEST['urbanizacion'];
		$cabecera['ip_olt'] = $_REQUEST['ip_olt'];
		$cabecera['mac_olt'] = $_REQUEST['mac_olt'];
		$cabecera['port_olt'] = $_REQUEST['port_olt'];
		$cabecera['nombre_olt'] = $_REQUEST['nombre_olt'];
		$cabecera['puerto_olt'] = $_REQUEST['puerto_olt'];
		
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/" . $esteBloque ['nombre'];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarCabecera', $cabecera['codigo_cabecera']);
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
		
		if ($resultado) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarCabecera', $cabecera);
			$cadenaSql = str_replace("''", 'null', $cadenaSql);
			$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
			
		} 
		
		if ($resultado) { 
			redireccion::redireccionar ( 'inserto');
			exit ();
		} else {
			redireccion::redireccionar ( 'noInserto' );
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

$miRegistrador = new Registrar ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();

?>
