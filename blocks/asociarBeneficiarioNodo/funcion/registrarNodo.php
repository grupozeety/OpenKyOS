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
		
		$nodo = array();

		$nodo['codigo_nodo'] = $_REQUEST['codigo_nodo'];
		$nodo['codigo_cabecera'] = $_REQUEST['codigo_cabecera'];
		$nodo['tipo_tecnologia'] = $_REQUEST['tipo_tecnologia'];
		
		if($nodo['tipo_tecnologia'] == 1){
			
			$nodo['mac_master_eoc'] = "No Aplica";
			$nodo['ip_master_eoc'] = "No Aplica";
			$nodo['mac_onu_eoc'] = "No Aplica";
			$nodo['ip_onu_eoc'] = "No Aplica";
			$nodo['mac_hub_eoc'] = "No Aplica";
			$nodo['ip_hub_eoc'] = "No Aplica";
			$nodo['mac_cpe_eoc'] = "No Aplica";
			$nodo['mac_celda'] = $_REQUEST['mac_celda'];
			$nodo['ip_celda'] = $_REQUEST['ip_celda'];
			$nodo['nombre_nodo'] = $_REQUEST['nombre_nodo'];
			$nodo['nombre_sectorial'] = $_REQUEST['nombre_sectorial'];
			$nodo['ip_switch_celda'] = $_REQUEST['ip_switch_celda'];
			$nodo['mac_sm_celda'] = $_REQUEST['mac_sm_celda'];
			$nodo['ip_sm_celda'] = $_REQUEST['ip_sm_celda'];
			$nodo['mac_cpe_celda'] = $_REQUEST['mac_cpe_celda'];
			
		}else if($nodo['tipo_tecnologia'] == 2){
			
			$nodo['mac_master_eoc'] = $_REQUEST['mac_celda'];
			$nodo['ip_master_eoc'] = $_REQUEST['mac_celda'];
			$nodo['mac_onu_eoc'] = $_REQUEST['mac_celda'];
			$nodo['ip_onu_eoc'] = $_REQUEST['mac_celda'];
			$nodo['mac_hub_eoc'] = $_REQUEST['mac_celda'];
			$nodo['ip_hub_eoc'] = $_REQUEST['mac_celda'];
			$nodo['mac_cpe_eoc'] = $_REQUEST['mac_celda'];
			$nodo['mac_celda'] = "No Aplica";
			$nodo['ip_celda'] = "No Aplica";
			$nodo['nombre_nodo'] = "No Aplica";
			$nodo['nombre_sectorial'] = "No Aplica";
			$nodo['ip_switch_celda'] = "No Aplica";
			$nodo['mac_sm_celda'] = "No Aplica";
			$nodo['ip_sm_celda'] = "No Aplica";
			$nodo['mac_cpe_celda'] = "No Aplica";
			
		}
		
		
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/" . $esteBloque ['nombre'];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarNodo', $nodo['codigo_nodo']);
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
		
		if ($resultado) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarNodo', $nodo);
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
