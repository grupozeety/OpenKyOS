<?php

namespace gestionComisionamiento\gestionRequisitos\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

use gestionComisionamiento\gestionRequisitos\entidad\Redireccionador;
use reportes\masivoActas\entidad\GenerarDocumento;
use gestionComisionamiento\gestionRequisitos\entidad\Sincronizar;
use gestionComisionamiento\gestionRequisitos\entidad\CrearUsuario;

include_once 'Redireccionador.php';
require_once 'sincronizar.php';
class cargueRequisitos {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $conexion;
	public $archivos_datos;
	public $esteRecursoDB;
	public $datos_contrato;
	public $mensaje;
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
		 * Se crea esta función para crear las cuentas de los abonasdos a los cuales ya les ha sido creada una cuenta en el sistema.
		 */
		//$this->crearCuentasMasivo ();
		
		/**
		 * 2.
		 * Asociar Codigo Documento
		 */
		
		$this->asosicarCodigoDocumento ();
		
		/**
		 * 1.
		 * CargarArchivos en el Directorio
		 */
		
		$this->cargarArchivos ();
		
		/**
		 * 3.
		 * Registrar Documentos
		 */
		
		$this->registrarDocumentos ();
		
		/**
		 * 4.
		 * Sincronizar Alfresco
		 */
		$total = 0;
		
		foreach ( $this->archivos_datos as $key => $values ) {
			$resultado [$key] = $this->sincronizacion->sincronizarAlfresco ( $_REQUEST ['id_beneficiario'], $this->archivos_datos [$key] );
			$total = $resultado [$key] ['estado'] + $total;
		}
		
		/**
		 * 5.
		 * Registrar Contrato Borrador y Servicio
		 */
		
		$valor = array (
				"total" => $total,
				"estado" => $this->mensaje ['sucess'] 
		);
		
		if ($this->registro_documentos) {
			
			Redireccionador::redireccionar ( "Inserto", $valor );
		} else {
			Redireccionador::redireccionar ( "NoInserto" );
		}
	}
	public function registrarDocumentos() {
		foreach ( $this->archivos_datos as $key => $value ) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarDocumentos', $value );
			$this->registro_documentos = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
			
			if ($value ['tipo_documento'] == "132" || $value ['campo'] == "004009") {
				
				// Consulta Agendamiento
				$cadenaSql = $this->miSql->getCadenaSql ( 'informacionLdapUsuario' );
				$crearCuenta = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
				
				if ($crearCuenta ['telefono'] == "0" || $crearCuenta ['telefono']) {
					$crearCuenta ['telefono'] = $crearCuenta ['celular'];
				}
				
				$_REQUEST = array_merge ( $_REQUEST, $crearCuenta );
				
				require_once 'crearUsuario.php';
				
				$miDocumento = new CrearUsuario ();
				//$this->mensaje = $miDocumento->iniciar ( $this->miSql );
			}
		}
		
		$this->verificarEstadoComisionamiento ();
	}
	public function verificarEstadoComisionamiento() {
		{
			
			// Consulta Agendamiento
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultaAgendamiento' );
			$agendamiento = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
			
			// var_dump($agendamiento);exit;
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarEstadoComisionamiento', "No Iniciado" );
			$estadoComisionamiento = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
			
			if ($agendamiento ['estado_comisionamiento'] === $estadoComisionamiento ['id_parametro']) {
				
				// Consulta Agendamiento
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultarEstadoComisionamiento', "En Proceso" );
				$estadoComisionamiento = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarEstadoComisionamiento', $estadoComisionamiento ['id_parametro'] );
				$actualizacionComisionamiento = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
			}
		}
	}
	public function asosicarCodigoDocumento() {
		foreach ( $_FILES as $key => $value ) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarParametro', $key );
			$id_parametro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			$_FILES [$key] ['tipo_documento'] = $id_parametro [0] ['id_parametro'];
			$_FILES [$key] ['descripcion_documento'] = str_replace ( "\\", "_", $id_parametro [0] ['codigo'] ) . '_' . $id_parametro [0] ['descripcion'];
		}
	}
	public function cargarArchivos() {
		foreach ( $_FILES as $key => $archivo ) {
			
			if ($_FILES [$key] ['size'] != 0 && $_FILES [$key] ['error'] == 0) {
				
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
					
					Redireccionador::redireccionar ( "ErrorCargarFicheroDirectorio" );
					exit ();
				}
				
				if (isset ( $exten ['extension'] ) == false) {
					$exten ['extension'] = 'txt';
				}
				
				$tamano = $archivo ['size'];
				$tipo = $archivo ['type'];
				$nombre_archivo = str_replace ( " ", "_", $archivo ['descripcion_documento'] );
				$doc = $nombre_archivo . "_" . $this->prefijo . '.' . $exten ['extension'];
				
				/*
				 * guardamos el fichero en el Directorio
				 */
				$ruta_absoluta = $this->miConfigurador->configuracion ['raizDocumento'] . "/archivos/" . $doc;
				
				$ruta_relativa = $this->miConfigurador->configuracion ['host'] . $this->miConfigurador->configuracion ['site'] . "/archivos/" . $doc;
				
				$archivo ['rutaDirectorio'] = $ruta_absoluta;
				if (! copy ( $archivo ['tmp_name'], $ruta_absoluta )) {
					
					Redireccionador::redireccionar ( "ErrorCargarFicheroDirectorio" );
					exit ();
				}
				$archivo_datos [] = array (
						'ruta_archivo' => $ruta_relativa,
						'rutaabsoluta' => $ruta_absoluta,
						'nombre_archivo' => $doc,
						'campo' => $key,
						'tipo_documento' => $archivo ['tipo_documento'] 
				);
			}
		}
		$this->archivos_datos = $archivo_datos;
	}
	public function crearCuentasMasivo() {
		
		// Consulta Agendamiento
		$cadenaSql = $this->miSql->getCadenaSql ( 'informacionLdapUsuarioMasivo' );
		$informacion = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		require_once 'crearUsuario.php';
		
		$contador = 0;
		
		foreach ($informacion AS $crearCuenta){
		
			if ($crearCuenta ['telefono'] == "0" || $crearCuenta ['telefono']) {
				$crearCuenta ['telefono'] = $crearCuenta ['celular'];
			}
			
			$_REQUEST = array_merge ( $_REQUEST, $crearCuenta );
			
			
			$miDocumento = new CrearUsuario ();
			$this->mensaje = $miDocumento->iniciar ( $this->miSql );
			
			$contador++;
			
		}
		
		die;
	}
}

$miProcesador = new cargueRequisitos ( $this->lenguaje, $this->sql );
?>

