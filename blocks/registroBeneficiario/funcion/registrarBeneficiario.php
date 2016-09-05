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
		$beneficiarioPotencial['manzana'] = $_REQUEST['manzana'];
		$beneficiarioPotencial['torre'] = $_REQUEST['torre'];
		$beneficiarioPotencial['bloque'] = $_REQUEST['bloque'];
		$beneficiarioPotencial['apartamento'] = $_REQUEST['apartamento'];
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
		
		$familiar = array();
		
		for($i=0; $i< $_REQUEST['familiares']; $i++){
			
			$familiar[$i]['id_beneficiario'] = $_REQUEST['id_beneficiario'];
			$familiar[$i]['identificacion'] = $_REQUEST['identificacion_familiar_'.$i];
			$familiar[$i]['nombre'] = $_REQUEST['nombre_familiar_'.$i];
			$familiar[$i]['parentesco'] = $_REQUEST['parentesco_'.$i];
			$familiar[$i]['genero'] = $_REQUEST['genero_familiar_'.$i];
			$familiar[$i]['edad'] = $_REQUEST['edad_familiar_'.$i];
			$familiar[$i]['nivel_estudio'] = $_REQUEST['nivel_estudio_familiar_'.$i];
			$familiar[$i]['correo'] = $_REQUEST['correo_familiar_'.$i];
			$familiar[$i]['grado'] = $_REQUEST['grado_familiar_'.$i];
			$familiar[$i]['institucion_educativa'] = $_REQUEST['institucion_educativa_familiar_'.$i];
			$familiar[$i]['pertenencia_etnica'] = $_REQUEST['pertenencia_etnica_familiar_'.$i];
			$familiar[$i]['ocupacion'] = $_REQUEST['ocupacion_familiar_'.$i];
		
		}
		
		$beneficiarioPotencial['familiar'] = $familiar;
		
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/" . $esteBloque ['nombre'];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'registrarBeneficiarioPotencial', $beneficiarioPotencial);
		$cadenaSql = str_replace("''", 'null', $cadenaSql);
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
		
		
		if ($resultado) {
			
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarFamiliares', $beneficiarioPotencial['familiar'] );
			$cadenaSql = str_replace("''", 'null', $cadenaSql);
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
