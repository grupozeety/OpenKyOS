<?php

namespace gestionBeneficiarios\generacionContrato\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

use gestionBeneficiarios\generacionContrato\entidad\Redireccionador;
use gestionBeneficiarios\generacionContrato\entidad\Sincronizar;

include_once 'Redireccionador.php';
require_once 'sincronizar.php';
class FormProcessor {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $conexion;
	public $archivos_datos;
	public $esteRecursoDB;
	public $datos_contrato;
	public function __construct($lenguaje, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->sincronizacion = new Sincronizar ( $lenguaje, $sql );
		
		// Conexion a Base de Datos
		$conexion = "interoperacion";
		$this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$_REQUEST ['tiempo'] = time ();
		
		/**
		 * 1.
		 * CargarArchivos en el Directorio
		 */
		
		$this->cargarArchivos ();
		
		/**
		 * 2.
		 * Asociar Codigo Documento
		 */
		
		$this->asosicarCodigoDocumento ();
		
		/**
		 * 3.
		 * Registrar Documentos
		 */
		
		$this->registrarDocumentos ();
		
		/**
		 * 4.
		 * Sincronizar Alfresco
		 */
		$total=0;
		foreach ( $this->archivos_datos as $key => $values ) {
			$resultado[$key]=$this->sincronizacion->sincronizarAlfresco ( $_REQUEST ['id_beneficiario'], $this->archivos_datos[$key] );
			
			$total=$resultado[$key]['estado']+$total;
		}
		
		/**
		 * 5.
		 * Registrar Contrato Borrador y Servicio
		 */
		
		$this->registrarContratoBorrador ();
		
		if ($this->datos_contrato) {
			Redireccionador::redireccionar ( "Inserto",$total);
		} else {
			Redireccionador::redireccionar ( "NoInserto" );
		}
	}
	public function registrarContratoBorrador() {
		$cadenaSql = $this->miSql->getCadenaSql ( 'registrarContrato' );
		$registro_contrato = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$this->datos_contrato = $registro_contrato;
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'registrarServicio', $registro_contrato [0] [0] );
		$registro_servicio = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
	}
	public function registrarDocumentos() {
		foreach ( $this->archivos_datos as $key => $value ) {
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarDocumentos', $value );
			$registro_docmuentos = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		}
	}
	public function asosicarCodigoDocumento() {
		foreach ( $this->archivos_datos as $key => $value ) {
			
			switch ($value ['campo']) {
				case 'cedula' :
					$cadenaSql = $this->miSql->getCadenaSql ( 'consultarParametro', "001" );
					$id_parametro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
					$this->archivos_datos [$key] ['tipo_documento'] = $id_parametro [0] ['id_parametro'];
					break;
				
				case 'certificado_servicio' :
					$cadenaSql = $this->miSql->getCadenaSql ( 'consultarParametro', "003" );
					$id_parametro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
					$this->archivos_datos [$key] ['tipo_documento'] = $id_parametro [0] ['id_parametro'];
					break;
				
				case 'acta_vip' :
					$cadenaSql = $this->miSql->getCadenaSql ( 'consultarParametro', "002" );
					$id_parametro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
					$this->archivos_datos [$key] ['tipo_documento'] = $id_parametro [0] ['id_parametro'];
					break;
				
				case 'documento_acceso_propietario' :
					$cadenaSql = $this->miSql->getCadenaSql ( 'consultarParametro', "006" );
					$id_parametro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
					$this->archivos_datos [$key] ['tipo_documento'] = $id_parametro [0] ['id_parametro'];
					break;
				
				case 'documento_direccion' :
					$cadenaSql = $this->miSql->getCadenaSql ( 'consultarParametro', "007" );
					$id_parametro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
					$this->archivos_datos [$key] ['tipo_documento'] = $id_parametro [0] ['id_parametro'];
					break;
				
				case 'certificado_proyecto_vip' :
					
					$cadenaSql = $this->miSql->getCadenaSql ( 'consultarParametro', "005" );
					$id_parametro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
					$this->archivos_datos [$key] ['tipo_documento'] = $id_parametro [0] ['id_parametro'];
					break;
				
				case 'cedula_cliente' :
					
					$cadenaSql = $this->miSql->getCadenaSql ( 'consultarParametro', "777" );
					$id_parametro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
					$this->archivos_datos [$key] ['tipo_documento'] = $id_parametro [0] ['id_parametro'];
					break;
			}
		}
	}
	public function cargarArchivos() {
		foreach ( $_FILES as $key => $archivo ) {
			
			$this->prefijo = substr ( md5 ( uniqid ( time () ) ), 0, 6 );
			/*
			 * obtenemos los datos del Fichero
			 */
			$tamano = $archivo ['size'];
			$tipo = $archivo ['type'];
			$nombre_archivo = str_replace ( " ", "", $archivo ['name'] );
			/*
			 * guardamos el fichero en el Directorio
			 */
			$ruta_absoluta = $this->miConfigurador->configuracion ['raizDocumento'] . "/archivos/" . $_REQUEST ['id_beneficiario'] . "_" . $this->prefijo . "_" . $nombre_archivo;
			$ruta_relativa = $this->miConfigurador->configuracion ['host'] . $this->miConfigurador->configuracion ['site'] . "/archivos/" . $_REQUEST ['id_beneficiario'] . "_" . $this->prefijo . "_" . $nombre_archivo;
			$archivo ['rutaDirectorio'] = $ruta_absoluta;
			if (! copy ( $archivo ['tmp_name'], $ruta_absoluta )) {
				exit ();
				Redireccionador::redireccionar ( "ErrorCargarFicheroDirectorio" );
			}
			
			$archivo_datos [] = array (
					'ruta_archivo' => $ruta_relativa,
					'rutaabsoluta' => $ruta_absoluta,
					'nombre_archivo' => $archivo ['name'],
					'campo' => $key 
			);
		}
		
		$this->archivos_datos = $archivo_datos;
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );
?>

