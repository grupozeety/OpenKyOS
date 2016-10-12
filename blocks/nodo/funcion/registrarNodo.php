<?php

namespace nodo\funcion;

use nodo\funcion\redireccionar;

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
	
	private $proyecto;
	private $nodo;
	private $parent;
	
	function __construct($lenguaje, $sql, $funcion) {
		
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miFuncion = $funcion;
		
		$_REQUEST['tiempo'] = time();
	}
	
	function consultarProyectoCabecera(){
	
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
	
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
	
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/" . $esteBloque ['nombre'];
	
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarProyectoCabecera', $_REQUEST['codigo_cabecera']);
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
	
		$this->proyecto = $resultado[0]['proyecto'];
		
	}

	function obtenerPaquetePadre() {
	
		$urlDetalle = $this->crearUrlDetalleProyectos($this->proyecto);
	
		$detalle = file_get_contents($urlDetalle);
		$detalle = json_decode($detalle, true);

		foreach ($detalle as $key => $value) {
			if ($value['subject'] === 'Infraestructura Nodo') {
				$paqueteInfraestructura = $value;
			}
		}
	
		$this->parent = $paqueteInfraestructura['id'];
		
	}
	
	function organizarVariables(){
	
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
	
			$nodo['mac_master_eoc'] = $_REQUEST['mac_master_eoc'];
			$nodo['ip_master_eoc'] = $_REQUEST['ip_master_eoc'];
			$nodo['mac_onu_eoc'] = $_REQUEST['mac_onu_eoc'];
			$nodo['ip_onu_eoc'] = $_REQUEST['ip_onu_eoc'];
			$nodo['mac_hub_eoc'] = $_REQUEST['mac_hub_eoc'];
			$nodo['ip_hub_eoc'] = $_REQUEST['ip_hub_eoc'];
			$nodo['mac_cpe_eoc'] = $_REQUEST['mac_cpe_eoc'];
			$nodo['mac_celda'] = "No Aplica";
			$nodo['ip_celda'] = "No Aplica";
			$nodo['nombre_nodo'] = "No Aplica";
			$nodo['nombre_sectorial'] = "No Aplica";
			$nodo['ip_switch_celda'] = "No Aplica";
			$nodo['mac_sm_celda'] = "No Aplica";
			$nodo['ip_sm_celda'] = "No Aplica";
			$nodo['mac_cpe_celda'] = "No Aplica";
	
		}
	
		$this->nodo = $nodo;
	
	}
	
	function crearUrlDetalleProyectos($var = '') {
	
		// URL base
		$url = $this->miConfigurador->getVariableConfiguracion("host");
		$url .= $this->miConfigurador->getVariableConfiguracion("site");
		$url .= "/index.php?";
		// Variables
		$variable = "pagina=openKyosApi";
		$variable .= "&procesarAjax=true";
		$variable .= "&action=index.php";
		$variable .= "&bloqueNombre=" . "llamarApi";
		$variable .= "&bloqueGrupo=" . "";
		$variable .= "&tiempo=" . $_REQUEST['tiempo'];
		$variable .= "&metodo=paquetesTrabajo";
		$variable .= "&id_proyecto=" . $var;
	
		// Codificar las variables
		$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
		$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);
	
		// URL definitiva
		$urlApi = $url . $cadena;
		
		return $urlApi;
	}
	
	function crearOrdenTrabajoNodo($project, $parent, $nombre, $descripcion) {
	
		$url = $this->miConfigurador->getVariableConfiguracion("host");
		$url .= $this->miConfigurador->getVariableConfiguracion("site");
		$url .= "/index.php?";
		// Variables
		$variable = "pagina=openKyosApi";
		$variable .= "&procesarAjax=true";
		$variable .= "&action=index.php";
		$variable .= "&bloqueNombre=" . "llamarApi";
		$variable .= "&bloqueGrupo=" . "";
		$variable .= "&tiempo=" . $_REQUEST['tiempo'];
		$variable .= "&metodo=crearPaqueteTrabajo";
	
		$arreglo['proyecto'] = $project;
		$arreglo['nombre'] = $nombre;
		$arreglo['porcentaje_avance'] = "0";
		$arreglo['descripcion'] = $descripcion;
		$arreglo['tipo'] = "2";
		$arreglo['estado'] = "1";
		$arreglo['prioridad'] = "8";
		$arreglo['paquete_trabajo_padre'] = $parent;
		$arreglo['camposPersonalizados'] = array(
				"customField14" => array(
						'value' => 'No Iniciado',
						'tipo' => 'string_objects',
				),
	
		);
	
		$variable .= "&variables=" . base64_encode(json_encode($arreglo));
	
		// Codificar las variables
		$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
		$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);
	
		// URL definitiva
		$urlApi = $url . $cadena;
	
		$resultado_registro= json_decode(file_get_contents ($urlApi), true);
		
		return $resultado_registro;
	
	}
	
	function obtenerIdentificadorPaqueteTrabajo($paquete_Trabajo = '') {
	
		$String = $paquete_Trabajo['self']['href'];
	
		$array = explode("/", $String);
	
		$resultado = end($array);
	
		return $resultado;
	
	}
	
	function crearOrdenesNodoHFC(){
		
		$id_project = $this->proyecto;
		$id_work_packge = $this->parent;
		
		$id = $this->crearOrdenTrabajoNodo($id_project,  $id_work_packge, $this->nodo['codigo_nodo'], $this->nodo['codigo_nodo']);
		$id = $this->obtenerIdentificadorPaqueteTrabajo($id);
		
		$this->crearOrdenTrabajoNodo($id_project, $id, "Instalación y conectorización equipo activo nodo EOC", "");
		$this->crearOrdenTrabajoNodo($id_project, $id, "Instalación herraje de seguridad", "");
		$this->crearOrdenTrabajoNodo($id_project, $id, "Instalación red eléctrica", "");
		$this->crearOrdenTrabajoNodo($id_project, $id, "Calibración y configuración", "");
		$this->crearOrdenTrabajoNodo($id_project, $id, "Entrega, lista de chequeo y documentación", "");
		$this->crearOrdenTrabajoNodo($id_project, $id, "Acometida a hogares", "");
		
		if($id != null && $id != ""){
			return true;
		}else{
			return false;
		}
		
	}
	
	function crearOrdenesNodoWMAN(){
	
		$id_project = $this->proyecto;
		$id_work_packge = $this->parent;
	
		$id = $this->crearOrdenTrabajoNodo($id_project,  $id_work_packge, $this->nodo['codigo_nodo'], $this->nodo['codigo_nodo']);
		$id = $this->obtenerIdentificadorPaqueteTrabajo($id);

		$this->crearOrdenTrabajoNodo($id_project, $id, "Incio instalación", "");
		$this->crearOrdenTrabajoNodo($id_project, $id, "Pruebas y configuración", "");
		$this->crearOrdenTrabajoNodo($id_project, $id, "Puesta en funcionamiento", "");
		$this->crearOrdenTrabajoNodo($id_project, $id, "Calibración y Configuración", "");
	
		if($id != null && $id != ""){
			return true;
		}else{
			return false;
		}
	
	}
	
	function registrarNodo(){
		

		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/" . $esteBloque ['nombre'];
		
		if($this->nodo['tipo_tecnologia'] == 1){
			$resultado = $this->crearOrdenesNodoWMAN();
		}else if($this->nodo['tipo_tecnologia'] == 2){
			$resultado = $this->crearOrdenesNodoHFC();
		}
		
		if ($resultado) {
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarNodo', $this->nodo['codigo_nodo']);
			$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
				
		}
		
		if ($resultado) {
				
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarNodo', $this->nodo);
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
	
	function procesarFormulario() {
		
		$this->consultarProyectoCabecera();
		
		$this->obtenerPaquetePadre();
		
		$this->organizarVariables();
		
		$this->registrarNodo();
		
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
