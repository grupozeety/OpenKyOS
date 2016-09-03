<?php

namespace registroBeneficiario\funcion;

use registroBeneficiario\funcion\redireccionar;

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
		
		
		$beneficiarioPotencial = array();
		
		$beneficiarioPotencial['id_beneficiario'] = $_REQUEST['id_beneficiario'];
		$beneficiarioPotencial['tipo_beneficiario'] = $_REQUEST['tipo_beneficiario'];
		$beneficiarioPotencial['identificacion_beneficiario'] = $_REQUEST['identificacion_beneficiario'];
		$beneficiarioPotencial['nombre_beneficiario'] = $_REQUEST['nombre_beneficiario'];
		$beneficiarioPotencial['genero_beneficiario'] = $_REQUEST['genero_beneficiario'];
		$beneficiarioPotencial['edad_beneficiario'] = $_REQUEST['edad_beneficiario'];
		$beneficiarioPotencial['nivel_estudio'] = $_REQUEST['nivel_estudio'];
		$beneficiarioPotencial['correo'] = $_REQUEST['correo'];
		$beneficiarioPotencial['foto'] = $_REQUEST['foto'];
		$beneficiarioPotencial['direccion'] = $_REQUEST['direccion'];
		$beneficiarioPotencial['tipo_vivienda'] = $_REQUEST['tipo_vivienda'];
		$beneficiarioPotencial['telefono'] = $_REQUEST['telefono'];
		$beneficiarioPotencial['celular'] = $_REQUEST['celular'];
		$beneficiarioPotencial['whatsapp'] = $_REQUEST['whatsapp'];
		$beneficiarioPotencial['departamento'] = $_REQUEST['departamento'];
		$beneficiarioPotencial['municipio'] = $_REQUEST['municipio'];
		$beneficiarioPotencial['urbanizacion'] = $_REQUEST['urbanizacion'];
		$beneficiarioPotencial['territorio'] = $_REQUEST['territorio'];
		$beneficiarioPotencial['estrato'] = $_REQUEST['estrato'];
		$beneficiarioPotencial['geolocalizacion'] = $_REQUEST['geolocalizacion'];
		$beneficiarioPotencial['jefe_hogar'] = $_REQUEST['jefe_hogar'];
		$beneficiarioPotencial['pertenencia_etnica'] = $_REQUEST['pertenencia_etnica'];
		$beneficiarioPotencial['ocupacion'] = $_REQUEST['ocupacion'];
		
// 		$contador=0;
		
// 		foreach ($_REQUEST as $key=>$consumo){
			
// 			$materiales = explode(":", $key);
			
// 			if($materiales[0] == "material"){
// 				$consumoMaterial[$contador]['name'] = $materiales[1];
// 				$consumoMaterial[$contador]['ordenTrabajo'] = $_REQUEST['ordenTrabajoReal'];
// 				$consumoMaterial[$contador]['proyecto'] = $_REQUEST['proyecto'];
// 				$consumoMaterial[$contador]['salida'] = $materiales[4];
// 				$consumoMaterial[$contador]['descripcion'] = $_REQUEST['actividad'];
// 				$consumoMaterial[$contador]['material'] = $materiales[2];
// 				$consumoMaterial[$contador]['asignada'] = $materiales[3];
// 				$consumoMaterial[$contador]['consume'] = $consumo;
// 				$consumoMaterial[$contador]['porcentajecons'] = $_REQUEST['porcentajecons'];
// 				$consumoMaterial[$contador]['geolocalizacion'] = $_REQUEST['geolocalizacion'];
// 				$contador++;
// 			}
// 		}
		
// 		var_dump($_REQUEST); die();
		
		
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/" . $esteBloque ['nombre'];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'registrarBeneficiarioPotencial', $beneficiarioPotencial);
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
		
		echo $cadenaSql;
		var_dump($resultado); die();
		
		if ($resultado) {
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarBeneficiarioPotencial', $consumoMaterial );
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
