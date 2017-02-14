<?php

namespace registroBeneficiario\funcion;

use registroBeneficiario\funcion\redireccionar;

include_once 'redireccionar.php';

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}
class Registrar {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miFuncion;
	public $miSql;
	public $conexion;
	public $archivos_datos;
	public function __construct($lenguaje, $sql, $funcion) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miFuncion = $funcion;
	}
	public function procesarFormulario() {
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/" . $esteBloque ['nombre'];
		
		$beneficiarioPotencial = array ();
		
		$this->validarBeneficiario ( $_REQUEST ['identificacion_beneficiario'] );
		
		if (isset ( $_REQUEST ['actualizar'] )) {
			$beneficiarioPotencial ['id_beneficiario'] = $_REQUEST ['id_beneficiario'];
			$beneficiarioPotencial ['nomenclatura'] = $_REQUEST ['nomenclatura'];
		} else {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'codificacion', $_REQUEST ['urbanizacion'] );
			$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			if ($resultado) {
				$_REQUEST ['consecutivo'] = $resultado [0] ['abr_benf'];
				$_REQUEST ['abr_urb'] = $resultado [0] ['abr_urb'];
				$_REQUEST ['abr_mun'] = $resultado [0] ['abr_mun'];
			} else {
				$_REQUEST ['consecutivo'] = "ND";
				$_REQUEST ['abr_urb'] = "ND";
				$_REQUEST ['abr_mun'] = "ND";
			}
			
			$numeroCaracteres = 5;
			$numeroBusqueda = strlen ( $_REQUEST ['consecutivo'] );
			
			$valor ['string'] = $_REQUEST ['consecutivo'];
			$valor ['longitud'] = $numeroCaracteres - $numeroBusqueda - 1;
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarConsecutivo', $valor );
			$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			if ($resultado) {
				$consecutivo = explode ( $_REQUEST ['consecutivo'], $resultado [0] ['id_beneficiario'] );
				$nuevoConsecutivo = $consecutivo [1] + 1;
				
				if (strlen ( $_REQUEST ['consecutivo'] ) == 1) {
					if ($nuevoConsecutivo < 10) {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '000' . $nuevoConsecutivo;
					} else if ($nuevoConsecutivo < 100) {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '00' . $nuevoConsecutivo;
					} else if ($nuevoConsecutivo < 1000) {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '0' . $nuevoConsecutivo;
					} else {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . $nuevoConsecutivo;
					}
				} else if (strlen ( $_REQUEST ['consecutivo'] ) == 2) {
					if ($nuevoConsecutivo < 10) {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '00' . $nuevoConsecutivo;
					} else if ($nuevoConsecutivo < 100) {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '0' . $nuevoConsecutivo;
					} else {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . $nuevoConsecutivo;
					}
				} else if (strlen ( $_REQUEST ['consecutivo'] ) == 3) {
					if ($nuevoConsecutivo < 10) {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '0' . $nuevoConsecutivo;
					} else if ($nuevoConsecutivo < 100) {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . $nuevoConsecutivo;
					} else {
						$nuevoConsecutivo = $_REQUEST ['consecutivo'] . $nuevoConsecutivo;
					}
				} else {
					$nuevoConsecutivo = $_REQUEST ['consecutivo'] . $nuevoConsecutivo;
				}
				
				$beneficiarioPotencial ['id_beneficiario'] = $nuevoConsecutivo;
			} else {
				if (strlen ( $_REQUEST ['consecutivo'] ) == 1) {
					$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '0001';
				} else if (strlen ( $_REQUEST ['consecutivo'] ) == 2) {
					$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '001';
				} else if (strlen ( $_REQUEST ['consecutivo'] ) == 3) {
					$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '01';
				} else if (strlen ( $_REQUEST ['consecutivo'] ) == 4) {
					$nuevoConsecutivo = $_REQUEST ['consecutivo'] . '1';
				}
				
				$beneficiarioPotencial ['id_beneficiario'] = $nuevoConsecutivo;
			}
			
			$beneficiarioPotencial ['nomenclatura'] = $_REQUEST ['abr_mun'] . "_" . $_REQUEST ['abr_urb'] . "_" . $_REQUEST ['identificacion_beneficiario'];
		}
		
		$this->cargarArchivos ( $beneficiarioPotencial ['id_beneficiario'] );
		
		$beneficiarioPotencial ['tipo_beneficiario'] = $_REQUEST ['tipo_beneficiario'];
		$beneficiarioPotencial ['identificacion_beneficiario'] = $_REQUEST ['identificacion_beneficiario'];
		$beneficiarioPotencial ['tipo_documento'] = $_REQUEST ['tipo_documento'];
		$beneficiarioPotencial ['nombre_beneficiario'] = $_REQUEST ['nombre_beneficiario'];
		$beneficiarioPotencial ['primer_apellido'] = $_REQUEST ['primer_apellido'];
		$beneficiarioPotencial ['segundo_apellido'] = $_REQUEST ['segundo_apellido'];
		$beneficiarioPotencial ['genero_beneficiario'] = $_REQUEST ['genero_beneficiario'];
		$beneficiarioPotencial ['edad_beneficiario'] = $_REQUEST ['edad_beneficiario'];
		$beneficiarioPotencial ['nivel_estudio'] = $_REQUEST ['nivel_estudio'];
		$beneficiarioPotencial ['correo'] = $_REQUEST ['correo'];
		
		if ($this->archivos_datos) {
			$beneficiarioPotencial ['foto'] = $this->archivos_datos [0] ['nombre_archivo'];
			$beneficiarioPotencial ['url_foto'] = $this->archivos_datos [0] ['rutaabsoluta'];
			$beneficiarioPotencial ['ruta_foto'] = $this->archivos_datos [0] ['ruta_archivo'];
		} else {
			$beneficiarioPotencial ['foto'] = str_replace ( "\\", "", $_REQUEST ['nombre_foto'] );
			$beneficiarioPotencial ['url_foto'] = str_replace ( "\\", "", $_REQUEST ['urlFoto'] );
			$beneficiarioPotencial ['ruta_foto'] = str_replace ( "\\", "", $_REQUEST ['rutaFoto'] );
		}
		
		$beneficiarioPotencial ['direccion'] = $_REQUEST ['direccion'];
		$beneficiarioPotencial ['tipo_vivienda'] = $_REQUEST ['tipo_vivienda'];
		$beneficiarioPotencial ['manzana'] = $_REQUEST ['manzana'];
		$beneficiarioPotencial ['torre'] = $_REQUEST ['torre'];
		$beneficiarioPotencial ['bloque'] = $_REQUEST ['bloque'];
		$beneficiarioPotencial ['interior'] = $_REQUEST ['interior'];
		$beneficiarioPotencial ['lote'] = $_REQUEST ['lote'];
		// $beneficiarioPotencial['torre'] = '';
		// $beneficiarioPotencial['bloque'] = '';
		$beneficiarioPotencial ['apartamento'] = $_REQUEST ['apartamento'];
		$beneficiarioPotencial ['telefono'] = $_REQUEST ['telefono'];
		$beneficiarioPotencial ['celular'] = $_REQUEST ['celular'];
		$beneficiarioPotencial ['whatsapp'] = $_REQUEST ['whatsapp'];
		$beneficiarioPotencial ['facebook'] = $_REQUEST ['facebook'];
		$departamento = explode ( " ", $_REQUEST ['departamento'] );
		$beneficiarioPotencial ['departamento'] = $departamento [0];
		$municipio = explode ( " ", $_REQUEST ['municipio'] );
		$beneficiarioPotencial ['municipio'] = $municipio [0];
		$beneficiarioPotencial ['id_proyecto'] = $_REQUEST ['urbanizacion'];
		$beneficiarioPotencial ['proyecto'] = $_REQUEST ['id_urbanizacion'];
		$beneficiarioPotencial ['territorio'] = $_REQUEST ['territorio'];
		$beneficiarioPotencial ['estrato'] = $_REQUEST ['estrato'];
		$beneficiarioPotencial ['geolocalizacion'] = $_REQUEST ['geolocalizacion'];
		$beneficiarioPotencial ['jefe_hogar'] = $_REQUEST ['jefe_hogar'];
		$beneficiarioPotencial ['pertenencia_etnica'] = $_REQUEST ['pertenencia_etnica'];
		$beneficiarioPotencial ['ocupacion'] = $_REQUEST ['ocupacion'];
		$beneficiarioPotencial ['nomenclatura'] = str_replace ( "\\", "", $beneficiarioPotencial ['nomenclatura'] );
		$beneficiarioPotencial ['id_hogar'] = '';
		// $beneficiarioPotencial['id_hogar'] = $_REQUEST['id_hogar'];
		// $beneficiarioPotencial['resolucion_adjudicacion'] = $_REQUEST['resolucion_adjudicacion'];
		// $beneficiarioPotencial['nomenclatura'] = '';
		$beneficiarioPotencial ['resolucion_adjudicacion'] = '';
		$beneficiarioPotencial ['minvi'] = 'FALSE';
		
		$familiar = array ();
		
		for($i = 0; $i < $_REQUEST ['familiares']; $i ++) {
			
			$familiar [$i] ['id_beneficiario'] = $beneficiarioPotencial ['id_beneficiario'];
			$familiar [$i] ['tipo_documento'] = $_REQUEST ['tipo_documento_familiar_' . $i];
			$familiar [$i] ['identificacion'] = $_REQUEST ['identificacion_familiar_' . $i];
			$familiar [$i] ['nombre'] = $_REQUEST ['nombre_familiar_' . $i];
			$familiar [$i] ['primer_apellido'] = $_REQUEST ['primer_apellido_familiar_' . $i];
			$familiar [$i] ['segundo_apellido'] = $_REQUEST ['segundo_apellido_familiar_' . $i];
			$familiar [$i] ['parentesco'] = $_REQUEST ['parentesco_' . $i];
			$familiar [$i] ['genero'] = $_REQUEST ['genero_familiar_' . $i];
			$familiar [$i] ['edad'] = $_REQUEST ['edad_familiar_' . $i];
			$familiar [$i] ['celular'] = $_REQUEST ['celular_familiar_' . $i];
			$familiar [$i] ['nivel_estudio'] = $_REQUEST ['nivel_estudio_familiar_' . $i];
			$familiar [$i] ['correo'] = $_REQUEST ['correo_familiar_' . $i];
			$familiar [$i] ['grado'] = $_REQUEST ['grado_familiar_' . $i];
			$familiar [$i] ['institucion_educativa'] = $_REQUEST ['institucion_educativa_familiar_' . $i];
			$familiar [$i] ['pertenencia_etnica'] = $_REQUEST ['pertenencia_etnica_familiar_' . $i];
			$familiar [$i] ['ocupacion'] = $_REQUEST ['ocupacion_familiar_' . $i];
		}
		
		$beneficiarioPotencial ['familiar'] = $familiar;
		$cadenaSql = "";
		$resultado = "";
		
		if (isset ( $_REQUEST ['actualizar'] )) {
			$cadenaSql .= 'BEGIN; ';
			
			$cadenaSql .= $this->miSql->getCadenaSql ( 'actualizarBeneficiarioPotencial', $beneficiarioPotencial );
			
			$cadenaSql .= $this->miSql->getCadenaSql ( 'actualizarFamiliarBeneficiario', $beneficiarioPotencial ['id_beneficiario'] );
			
			if ($_REQUEST ['familiares'] > 0) {
				$cadenaSql .= $this->miSql->getCadenaSql ( 'registrarFamiliares', $beneficiarioPotencial ['familiar'] );
			}
			
			$cadenaSql .= 'COMMIT;';
			
			$cadenaSql = str_replace ( "''", 'null', $cadenaSql );
			
			$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registrar" );
		} else {
			
			$cadenaSql .= 'BEGIN; ';
			
			$cadenaSql .= $this->miSql->getCadenaSql ( 'registrarBeneficiarioPotencial', $beneficiarioPotencial );
			
			if ($_REQUEST ['familiares'] > 0) {
				$cadenaSql .= $this->miSql->getCadenaSql ( 'registrarFamiliares', $beneficiarioPotencial ['familiar'] );
			}
			
			$cadenaSql .= 'COMMIT;';
			
			$cadenaSql = str_replace ( "''", 'null', $cadenaSql );
			
			$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registrar" );
		}
		
		if ($resultado) {
			
			if (isset ( $_REQUEST ['actualizar'] )) {
				redireccion::redireccionar ( 'actualizo', $beneficiarioPotencial ['id_beneficiario'] );
			} else {
				redireccion::redireccionar ( 'inserto', $beneficiarioPotencial ['id_beneficiario'] );
			}
			exit ();
		} else {
			if (isset ( $_REQUEST ['actualizar'] )) {
				redireccion::redireccionar ( 'noActualizo', $beneficiarioPotencial ['id_beneficiario'] );
			} else {
				redireccion::redireccionar ( 'noInserto', $beneficiarioPotencial ['id_beneficiario'] );
			}
			exit ();
		}
	}
	public function validarBeneficiario($beneficiario) {

		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'validarBen', $beneficiario );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

		if ($resultado != false) {
			redireccion::redireccionar ( 'existeBen', '' );
		}
	}
	
	public function cargarArchivos($id_beneficiario) {
		$archivo_datos = false;
		
		foreach ( $_FILES as $key => $archivo ) {
			
			if ($_FILES [$key] ['size'] != 0) {
				
				$this->prefijo = substr ( md5 ( uniqid ( time () ) ), 0, 6 );
				$exten = pathinfo ( $archivo ['name'] );
				
				$allowed = array (
						'image/jpeg',
						'image/png',
						'image/psd',
						'image/bmp',
						'application/pdf' 
				);
				
				if (! in_array ( $_FILES [$key] ['type'], $allowed )) {
					exit ();
				}
				
				if (isset ( $exten ['extension'] ) == false) {
					$exten ['extension'] = 'txt';
				}
				
				$tamano = $archivo ['size'];
				$tipo = $archivo ['type'];
				
				$prefijo = substr ( md5 ( uniqid ( time () ) ), 0, 6 );
				
				$carpetaAdjunta = $this->miConfigurador->configuracion ['raizDocumento'] . "/archivos/" . $prefijo . "/";
				$rutaUrlBloque = $this->miConfigurador->configuracion ['host'] . $this->miConfigurador->configuracion ['site'] . "/archivos/" . $prefijo . "/";
				
				$allowed = array (
						'image/jpeg',
						'image/png',
						'image/psd',
						'image/bmp',
						'application/pdf' 
				);
				
				if (! in_array ( $_FILES [$key] ['type'], $allowed )) {
					exit ();
				}
				
				mkdir ( $carpetaAdjunta, 0777 );
				
				// El nombre y nombre temporal del archivo que vamos para adjuntar
				$nombreArchivo = isset ( $archivo ['name'] ) ? $archivo ['name'] : null;
				$nombreTemporal = isset ( $archivo ['tmp_name'] ) ? $archivo ['tmp_name'] : null;
				
				$nombreArchivo = str_replace ( " ", "", $nombreArchivo );
				
				$nombreFinal = $id_beneficiario . "-" . $prefijo . "-" . $nombreArchivo;
				$rutaFinal = $carpetaAdjunta;
				$urlFinal = $rutaUrlBloque;
				
				$rutaArchivo = $carpetaAdjunta . $nombreFinal;
				$rutaUrlArchivo = $rutaUrlBloque . $nombreFinal;
				
				$dir = $carpetaAdjunta;
				$handle = opendir ( $dir );
				$ficherosEliminados = 0;
				while ( $file = readdir ( $handle ) ) {
					if (is_file ( $dir . $file )) {
						if (unlink ( $dir . $file )) {
							$ficherosEliminados ++;
						}
					}
				}
				
				$archivo_datos [] = array (
						'ruta_archivo' => $rutaFinal,
						'rutaabsoluta' => $urlFinal,
						'nombre_archivo' => $nombreFinal 
				);
				
				move_uploaded_file ( $nombreTemporal, $rutaArchivo );
			}
		}
		
		$this->archivos_datos = $archivo_datos;
	}
	public function resetForm() {
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
