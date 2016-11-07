<?php

namespace gestionComisionamiento\agendaComisionador\funcion;

use gestionComisionamiento\agendaComisionador\funcion\redireccionar;

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
		
		var_dump($_REQUEST); die;
		
		$informacion = array();
		$agen = array();
		$cont = 0;
		foreach ($_REQUEST as $key => $agendamiento){
			
			if(explode("_", $key)[0] == "checkbox"){
				
				$informacion = explode(":", $agendamiento);
				$agen[$cont]['id_agendamiento'] =  "AG-01";
				$agen[$cont]['id_urbanizacion'] = $informacion[1];
				$agen[$cont]['descripcion_urbanizacion'] = $informacion[0];
				$agen[$cont]['identificacion_beneficiario'] = $informacion[8];
				$agen[$cont]['nombre_beneficiario'] = $informacion[9];
				$agen[$cont]['tipo_agendamiento'] = $_REQUEST['tipo_agendamiento'];
				$agen[$cont]['tipo_tecnologia'] = $_REQUEST['tipo_tecnologia'];
				$agen[$cont]['id_comisionador'] = $_REQUEST['comisionador'];
				$agen[$cont]['fecha_agendamiento'] = $_REQUEST['fecha_agendamiento'];
				$agen[$cont]['codigo_nodo'] = $informacion[2];
				$agen[$cont]['id_orden_trabajo'] = $informacion[3];
				$agen[$cont]['descripcion_orden_trabajo'] = $informacion[3];
				$agen[$cont]['manzana'] = $informacion[4];
				$agen[$cont]['torre'] = $informacion[5];
				$agen[$cont]['bloque'] = $informacion[6];
				$agen[$cont]['apartamento'] = $informacion[7];
				
				$cont++;
			}
			
		}
		
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/" . $esteBloque ['nombre'];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'registrarAgendamiento', $agen);
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
		
		if ($resultado) {
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarConsecutivoAgendamiento');
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
