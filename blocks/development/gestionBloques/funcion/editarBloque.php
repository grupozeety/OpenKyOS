<?php

namespace development\gestionBloques\funcion;

class EditarBloques {
	var $miConfigurador;
	var $miSql;
	var $conexion;
	var $infoBloqueActual;
	var $autorizacionActualizar;
	var $nuevaRutaGrupo = false;
	var $directorioInstalacion = "blocks/";
	var $rutaNuevaBloque;
	var $namespace;
	function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
	}
	function procesarEditarBloque() {
		
		/**
		 * Consultar Información Actual Bloque
		 */
		$this->consultarInformacionActualBloque ();
		
		/**
		 * Edición de Directorios
		 */
		$this->edicionDirectorios ();
		
		/**
		 * Mover Bloque a Nueva Ruta Grupo
		 */
		$this->moverBloque ();
		
		/**
		 * Renombrar Bloque a Nueva Ruta Grupo
		 */
		$this->renombrarBloque ();
		
		/**
		 * Cambiar Namespace Bloque
		 */
		$this->cambiarNamespace ();
		
		/**
		 * Actualizar Información Bloque en la DB
		 */
		$this->procesarEdicionBloqueSql ();
		
		return true;
	}
	function procesarEdicionBloqueSql() {
		$this->conexion = $this->miConfigurador->fabricaConexiones->getRecursoDB ( 'estructura' );
		/**
		 * Datos Actuales del Bloque
		 */
		
		if ($this->autorizacionActualizar) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarInformacionBloque' );
			$actualizacionBloque = $this->conexion->ejecutarAcceso ( $cadenaSql, 'acceso' );
		}
	}
	function cambiarNamespace() {
		$this->reescribirNamespace ( $this->rutaNuevaBloque . "/bloque.php" );
		$this->reescribirNamespace ( $this->rutaNuevaBloque . "/control/Entidad.class.php" );
		$this->reescribirNamespace ( $this->rutaNuevaBloque . "/control/Frontera.class.php" );
		$this->reescribirNamespace ( $this->rutaNuevaBloque . "/control/Lenguaje.class.php" );
		$this->reescribirNamespace ( $this->rutaNuevaBloque . "/control/Sql.class.php" );
		$this->reescribirNamespace ( $this->rutaNuevaBloque . "/entidad/procesarAjax.php", " \ entidad " );
		$this->reescribirNamespace ( $this->rutaNuevaBloque . "/entidad/procesarFormulario.php", " \ entidad " );
		$this->reescribirNamespace ( $this->rutaNuevaBloque . "/entidad/Redireccionador.php", " \ entidad " );
		$this->reescribirNamespace ( $this->rutaNuevaBloque . "/frontera/miFormulario.php", " \ frontera " );
		
		$this->autorizacionActualizar = true;
	}
	function reescribirNamespace($archivo, $extensionNamespace = '') {
		$extensionNamespace = str_replace ( ' ', '', $extensionNamespace );
		
		$contenidoArchivo = file_get_contents ( $archivo );
		
		$contenidoArchivo = explode ( "\n", $contenidoArchivo );
		
		$contenidoArchivo [1] = 'namespace ' . $this->namespace . $extensionNamespace . ';';
		
		$archivoReescribir = fopen ( $archivo, "w+b" );
		
		foreach ( $contenidoArchivo as $linea ) {
			fwrite ( $archivoReescribir, $linea . "\n" );
		}
		
		fclose ( $archivoReescribir );
	}
	function renombrarBloque() {
		/**
		 * Comparar Nombre Bloque
		 */
		$infoActual = $this->infoBloqueActual;
		
		if ($this->infoBloqueActual ['nombre'] != $_REQUEST ['nombre'] && $this->nuevaRutaGrupo) {
			
			$this->rutaNuevaBloque = $this->nuevaRutaGrupo . "/" . trim ( $_REQUEST ['nombre'] );
			$this->namespace = str_replace ( "/", " \ ", trim ( $_REQUEST ['grupo'] ) . "/" . trim ( $_REQUEST ['nombre'] ) );
			$this->namespace = str_replace ( " ", "", $this->namespace );
			rename ( $this->nuevaRutaGrupo . "/" . trim ( $this->infoBloqueActual ['nombre'] ), $this->rutaNuevaBloque );
		} elseif ($this->infoBloqueActual ['nombre'] != $_REQUEST ['nombre'] && $this->nuevaRutaGrupo == false && $_REQUEST ['grupo'] != '') {
			
			$NombreActualBloque = $this->directorioInstalacion . trim ( $this->infoBloqueActual ['grupo'] ) . "/" . trim ( $this->infoBloqueActual ['nombre'] );
			$this->rutaNuevaBloque = $this->directorioInstalacion . trim ( $this->infoBloqueActual ['grupo'] ) . "/" . trim ( $_REQUEST ['nombre'] );
			$this->namespace = str_replace ( "/", " \ ", trim ( $this->infoBloqueActual ['grupo'] ) . "/" . trim ( $_REQUEST ['nombre'] ) );
			$this->namespace = str_replace ( " ", "", $this->namespace );
			rename ( $NombreActualBloque, $this->rutaNuevaBloque );
		} elseif ($this->infoBloqueActual ['nombre'] != $_REQUEST ['nombre'] && $this->nuevaRutaGrupo == false && $_REQUEST ['grupo'] == '') {
			$NombreActualBloque = $this->directorioInstalacion . trim ( $this->infoBloqueActual ['nombre'] );
			$this->rutaNuevaBloque = $this->directorioInstalacion . trim ( $_REQUEST ['nombre'] );
			$this->namespace = str_replace ( "/", " \ ", trim ( $_REQUEST ['nombre'] ) );
			$this->namespace = str_replace ( " ", "", $this->namespace );
			rename ( $NombreActualBloque, $this->rutaNuevaBloque );
		} elseif ($this->infoBloqueActual ['nombre'] == $_REQUEST ['nombre'] && $this->nuevaRutaGrupo) {
			
			$this->rutaNuevaBloque = $this->nuevaRutaGrupo . "/" . $this->infoBloqueActual ['nombre'];
			$this->namespace = str_replace ( "/", " \ ", trim ( $_REQUEST ['grupo'] ) . "/" . $this->infoBloqueActual ['nombre'] );
			$this->namespace = str_replace ( " ", "", $this->namespace );
			rename ( $this->nuevaRutaGrupo . "/" . trim ( $this->infoBloqueActual ['nombre'] ), $this->rutaNuevaBloque );
		}
	}
	function moverBloque() {
		/**
		 * Validar si exite Nueva Ruta
		 */
		if ($this->nuevaRutaGrupo) {
			
			$rutaActualBloque = $this->directorioInstalacion . trim ( $this->infoBloqueActual ['grupo'] ) . "/" . trim ( $this->infoBloqueActual ['nombre'] );
			
			$rutaActualGrupo = $this->directorioInstalacion . trim ( $this->infoBloqueActual ['grupo'] );
			
			rename ( $rutaActualBloque, $this->nuevaRutaGrupo . "/" . trim ( $this->infoBloqueActual ['nombre'] ) );
			
			/**
			 * Validar si existen ficheros
			 */
			
			$arrayDirectorio = explode ( "/", $rutaActualGrupo );
			
			for($i = count ( $arrayDirectorio ); $i > 1; $i --) {
				
				$rutaVerificar = implode ( "/", $arrayDirectorio );
				
				if (is_dir ( $rutaVerificar )) {
					// Escaneamos el directorio
					$carpeta = scandir ( $rutaVerificar );
					
					// Eliminamos si el conteo es cigual a dos dado que corresponde a (.) y(..)
					if (count ( $carpeta ) == 2) {
						
						rmdir ( $rutaVerificar );
						$arrayDirectorio = explode ( "/", $rutaVerificar );
						
						unset ( $arrayDirectorio [$i - 1] );
					}
				}
			}
		} elseif ($_REQUEST ['grupo'] == '') {
			
			$rutaActualBloque = $this->directorioInstalacion . trim ( $this->infoBloqueActual ['grupo'] ) . "/" . trim ( $this->infoBloqueActual ['nombre'] );
			
			$rutaActualGrupo = $this->directorioInstalacion . trim ( $this->infoBloqueActual ['grupo'] );
			
			rename ( $rutaActualBloque, $this->directorioInstalacion . trim ( $this->infoBloqueActual ['nombre'] ) );
			
			/**
			 * Validar si existen ficheros
			 */
			
			$arrayDirectorio = explode ( "/", $rutaActualGrupo );
			
			for($i = count ( $arrayDirectorio ); $i > 1; $i --) {
				
				$rutaVerificar = implode ( "/", $arrayDirectorio );
				
				if (is_dir ( $rutaVerificar )) {
					// Escaneamos el directorio
					$carpeta = scandir ( $rutaVerificar );
					
					// Eliminamos si el conteo es cigual a dos dado que corresponde a (.) y(..)
					if (count ( $carpeta ) == 2) {
						
						rmdir ( $rutaVerificar );
						$arrayDirectorio = explode ( "/", $rutaVerificar );
						
						unset ( $arrayDirectorio [$i - 1] );
					}
				}
			}
		}
	}
	function edicionDirectorios() {
		/**
		 * Comparar Nombre Directorio
		 */
		if ($this->infoBloqueActual ['grupo'] != $_REQUEST ['grupo']) {
			$this->crearNuevoGrupo ();
		}
	}
	function crearNuevoGrupo() {
		
		/**
		 * Creación Nuevo Grupo
		 */
		$_REQUEST ['grupo'] = str_replace ( ' ', '', $_REQUEST ['grupo'] );
		
		if ($_REQUEST ['grupo'] != '') {
			
			$gruposBusqueda = strpos ( $_REQUEST ['grupo'], "/" );
			
			if ($gruposBusqueda) {
				
				$arrayGrupo = explode ( "/", $_REQUEST ['grupo'] );
				
				$cadenaGrupo = '';
				
				foreach ( $arrayGrupo as $valor ) {
					
					$this->nuevaRutaGrupo = ($cadenaGrupo != '') ? $this->directorioInstalacion . $cadenaGrupo . "/" . $valor : $this->directorioInstalacion . $valor;
					if (! file_exists ( $this->nuevaRutaGrupo ))
						mkdir ( $this->nuevaRutaGrupo, 0777, true );
					
					chmod ( $this->nuevaRutaGrupo, 0777 );
					$cadenaGrupo .= ($cadenaGrupo != '') ? "/" . $valor : $valor;
				}
			} else {
				
				$this->nuevaRutaGrupo = $this->directorioInstalacion . $_REQUEST ['grupo'];
				mkdir ( $this->nuevaRutaGrupo, 0777, true );
				chmod ( $this->nuevaRutaGrupo, 0777 );
			}
		}
	}
	function consultarInformacionActualBloque() {
		$this->conexion = $this->miConfigurador->fabricaConexiones->getRecursoDB ( 'estructura' );
		/**
		 * Datos Actuales del Bloque
		 */
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'informacionBloque' );
		$informacionActual = $this->conexion->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		$informacionActual = $informacionActual [0];
		
		foreach ( $informacionActual as $key => $valor )
			$this->infoBloqueActual [$key] = trim ( $valor );
	}
}

$miRegistrador = new EditarBloques ( $this->sql );

$resultado = $miRegistrador->procesarEditarBloque ();

?>