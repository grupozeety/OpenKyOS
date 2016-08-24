<?php

namespace consumoMateriales\funcion;

use hojaDeVida\crearDocente\funcion\redireccionar;

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
		
		$consumoMaterial = array();
		
		$contador=0;
		
		foreach ($_REQUEST as $key=>$consumo){
			
			$materiales = explode(":", $key);
			
			if($materiales[0] == "material"){
				$consumoMaterial[$contador]['name'] = $materiales[1];
				$consumoMaterial[$contador]['ordenTrabajo'] = $_REQUEST['ordenTrabajoReal'];
				$consumoMaterial[$contador]['proyecto'] = $_REQUEST['proyecto'];
				$consumoMaterial[$contador]['salida'] = $materiales[4];
				$consumoMaterial[$contador]['descripcion'] = $_REQUEST['actividad'];
				$consumoMaterial[$contador]['material'] = $materiales[2];
				$consumoMaterial[$contador]['asignada'] = $materiales[3];
				$consumoMaterial[$contador]['consume'] = $consumo;
				$consumoMaterial[$contador]['porcentajecons'] = $_REQUEST['porcentajecons'];
				$consumoMaterial[$contador]['geolocalizacion'] = $_REQUEST['geolocalizacion'];
				$contador++;
			}
		}
		
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/" . $esteBloque ['nombre'];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarConsumo', $consumoMaterial );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
		
		if ($resultado) {
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarConsumo', $consumoMaterial );
			$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
		} else {
			redireccion::redireccionar ( 'noInserto' );
			exit ();
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
