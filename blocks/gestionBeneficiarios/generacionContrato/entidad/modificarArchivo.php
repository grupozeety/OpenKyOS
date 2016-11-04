<?php

namespace gestionBeneficiarios\generacionContrato\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

use gestionBeneficiarios\generacionContrato\entidad\Redireccionador;
use gestionBeneficiarios\generacionContrato\entidad\Sincronizar;

include_once "core/auth/SesionSso.class.php";

include_once 'Redireccionador.php';
require_once 'sincronizar.php';
class Alfresco {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $conexion;
	public $archivos_datos;
	public $esteRecursoDB;
	public $datos_contrato;
	public $rutaURL;
	public $rutaAbsoluta;
	public $miSesionSso;
	public function __construct($lenguaje, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->sincronizacion = new Sincronizar ( $lenguaje, $sql );
		$this->miSesionSso = \SesionSso::singleton ();
		
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		
		$this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		
		if (! isset ( $_REQUEST ["bloqueGrupo"] ) || $_REQUEST ["bloqueGrupo"] == "") {
			$this->rutaURL .= "/blocks/" . $_REQUEST ["bloque"] . "/";
			$this->rutaAbsoluta .= "/blocks/" . $_REQUEST ["bloque"] . "/";
		} else {
			$this->rutaURL .= "/blocks/" . $_REQUEST ["bloqueGrupo"] . "/" . $_REQUEST ["bloque"] . "/";
			$this->rutaAbsoluta .= "/blocks/" . $_REQUEST ["bloqueGrupo"] . "/" . $_REQUEST ["bloque"] . "/";
		}
		// Conexion a Base de Datos
		$conexion = "interoperacion";
		$this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
			if ($_REQUEST ['verificar'] == 'true') {
			
			// Modificar el estado del archivo por verificación según el rol
			$this->verificarArchivo ();
		}
		
		if ($_REQUEST ['actualizar'] == 'true') {
		
			foreach ($_FILES as $key=>$values){
				if ($_FILES [$key] ['size'] == 0) {
					Redireccionador::redireccionar ( "noverifico", $_REQUEST ['id_beneficiario'] );
				}
			}
			// Modificar Documento Actual por uno nuevo
			$this->asociarCodigoDocumento ();
			$this->cargarArchivos ();
	

			$this->sincronizacion->sincronizarAlfresco ( $_REQUEST ['id_beneficiario'], $this->archivos_datos [0] );
			
			$this->actualizarLocal ();
		}

		if ($this->verificacion) {
			Redireccionador::redireccionar ( "verifico", $_REQUEST ['id_beneficiario'] );
		} else {
			Redireccionador::redireccionar ( "noverifico", $_REQUEST ['id_beneficiario'] );
		}
	}
	public function verificarArchivo() {
		$respuesta = $this->miSesionSso->getParametrosSesionAbierta ();
		foreach ( $respuesta ['description'] as $key => $rol ) {
			$respuesta ['rol'] [] = $rol;
		}
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'pruebas' );
		$pruebas = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$rol = $pruebas [0] [0];
		
		$datos = array (
				'archivo' => $_REQUEST ['id_archivo'],
				'rol' => $rol 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'verificarArchivo', $datos );
		$this->verificacion = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
	}
	public function actualizarLocal() {
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarLocal', $this->archivos_datos [0] );
		$this->verificacion = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
	}
	
	public function asociarCodigoDocumento() {
		foreach ( $_FILES as $key => $value ) {
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarParametro', $key );
			$id_parametro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			$_FILES [$key] ['tipo_documento'] = $id_parametro [0] ['id_parametro'];
			$_FILES [$key] ['descripcion_documento'] = $id_parametro [0] ['id_parametro'] . '_' . $id_parametro [0] ['descripcion'];
		}
	}
	public function cargarArchivos() {

		foreach ( $_FILES as $key => $archivo ) {
			if ($_FILES [$key] ['size'] != 0) {
				$this->prefijo = substr ( md5 ( uniqid ( time () ) ), 0, 6 );
				$exten = pathinfo ( $archivo ['name'] );
				$tamano = $archivo ['size'];
				$tipo = $archivo ['type'];
				$nombre_archivo = str_replace ( " ", "_", $archivo ['descripcion_documento'] );
				$doc = $_REQUEST ['id_beneficiario'] . "_" . $nombre_archivo . "_" . $this->prefijo . '.' . $exten ['extension'];
				/*
				 * guardamos el fichero en el Directorio
				 */
				$ruta_absoluta = $this->miConfigurador->configuracion ['raizDocumento'] . "/archivos/" . $doc;
				$ruta_relativa = $this->miConfigurador->configuracion ['host'] . $this->miConfigurador->configuracion ['site'] . "/archivos/" . $doc;
				$archivo ['rutaDirectorio'] = $ruta_absoluta;
				if (! copy ( $archivo ['tmp_name'], $ruta_absoluta )) {
					exit ();
					Redireccionador::redireccionar ( "ErrorCargarFicheroDirectorio" );
				}
				$archivo_datos [] = array (
						'ruta_archivo' => $ruta_relativa,
						'rutaabsoluta' => $ruta_absoluta,
						'nombre_archivo' => $doc,
						'campo' => $key,
						'tipo_documento' => $archivo ['tipo_documento'],
						'id_archivo'=>$_REQUEST['id_archivo'],
				);
			}
		}
		$this->archivos_datos = $archivo_datos;
	}
}

$miProcesador = new Alfresco ( $this->lenguaje, $this->sql );
?>

