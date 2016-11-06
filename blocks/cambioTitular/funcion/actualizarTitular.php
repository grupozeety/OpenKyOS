<?php

namespace cambioTitular\funcion;

use cambioTitular\funcion\redireccionar;

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
		
		
		$datos['id'] = $_REQUEST['id']; 
		$datos['titular'] = $_REQUEST['titular'];
		
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'informacionFamiliar', $datos);
		$nuevoTitular = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$nuevoTitular = $nuevoTitular[0];
		
		if($nuevoTitular){
			$cadenaSql = $this->miSql->getCadenaSql ( 'informacionBeneficiarioPotencial', $datos);
			$actualTitular = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			$actualTitular = $actualTitular[0];
		}
		
		$titular['id_beneficiario'] = $actualTitular['id_beneficiario'];
		$titular['tipo_beneficiario'] = $actualTitular['tipo_beneficiario'];
		$titular['tipo_documento'] = $actualTitular['tipo_documento'];
		$titular['identificacion'] = $nuevoTitular['identificacion_familiar'];
		$titular['nombre'] = $nuevoTitular['nombre_familiar'];
		$titular['primer_apellido'] = $nuevoTitular['primer_apellido_familiar'];
		$titular['segundo_apellido'] = $nuevoTitular['segundo_apellido_familiar'];
		$titular['genero'] = $nuevoTitular['genero_familiar'];
		$titular['edad'] = $nuevoTitular['edad_familiar'];
		$titular['nivel_estudio'] = $nuevoTitular['nivel_estudio_familiar'];
		$titular['correo'] = $nuevoTitular['correo_familiar'];
		$titular['foto'] = '';
		$titular['ruta_foto'] = '';
		$titular['url_foto'] = '';
		$titular['direccion'] = $actualTitular['direccion'];
		$titular['tipo_vivienda'] = $actualTitular['tipo_vivienda'];
		$titular['manzana'] = $actualTitular['manzana'];
		$titular['torre'] = $actualTitular['torre'];
		$titular['bloque'] = $actualTitular['bloque'];
		$titular['apartamento'] = $actualTitular['apartamento'];
		$titular['telefono'] = $actualTitular['telefono'];
		$titular['celular'] = $nuevoTitular['celular_familiar'];
		$titular['whatsapp'] = '';
		$titular['facebook'] = '';
		$titular['departamento'] = $actualTitular['departamento'];
		$titular['municipio'] = $actualTitular['municipio'];
		$titular['id_proyecto'] = $actualTitular['id_proyecto'];
		$titular['proyecto'] = $actualTitular['proyecto'];
		$titular['territorio'] = $actualTitular['territorio'];
		$titular['estrato'] = $actualTitular['estrato'];
		$titular['geolocalizacion'] = $actualTitular['geolocalizacion'];
		$titular['jefe_hogar'] = '';
		$titular['pertenencia_etnica'] = $nuevoTitular['pertenencia_etnica_familiar'];
		$titular['ocupacion'] = $nuevoTitular['ocupacion_familiar'];
		
		$familiar = array();
			
		$familiar['id_beneficiario'] = $actualTitular['id_beneficiario'];
		$familiar['tipo_documento'] = $nuevoTitular['tipo_documento_familiar'];
		$familiar['identificacion_familiar'] = $actualTitular['identificacion'];
		$familiar['nombre_familiar'] = $actualTitular['nombre'];
		$familiar['primer_apellido'] = $actualTitular['primer_apellido'];
		$familiar['segundo_apellido'] = $actualTitular['segundo_apellido'];
		$familiar['parentesco'] = '';
		$familiar['genero_familiar'] = $actualTitular['genero'];
		$familiar['edad_familiar'] = $actualTitular['edad'];
		$familiar['celular'] = $actualTitular['celular'];
		$familiar['nivel_estudio_familiar'] = $actualTitular['nivel_estudio'];
		$familiar['correo_familiar'] = $actualTitular['correo'];
		$familiar['grado'] = '';
		$familiar['institucion_educativa'] = '';
		$familiar['pertenencia_etnica_familiar'] = $actualTitular['pertenencia_etnica'];
		$familiar['ocupacion_familiar'] = $actualTitular['ocupacion'];
		
		$titular['familiar'] = $familiar;
		
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/" . $esteBloque ['nombre'];
		
		$cadenaSql = "";
		$cadenaSql .= 'BEGIN; ';
		
		$cadenaSql .= $this->miSql->getCadenaSql ( 'registrarTitular', $titular);
		$cadenaSql .= $this->miSql->getCadenaSql ( 'actualizarFamiliar', $titular['identificacion']);
		$cadenaSql .= $this->miSql->getCadenaSql ( 'registrarFamiliar', $titular['familiar'] );
		
		$cadenaSql .= 'COMMIT;';
			
		$cadenaSql = str_replace("''", 'null', $cadenaSql);
			
		$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "registrar");
		
		echo $cadenaSql;
		var_dump($resultado);
		die;
		
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
