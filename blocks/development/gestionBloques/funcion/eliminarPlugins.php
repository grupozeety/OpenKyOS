<?php

namespace development\gestionBloques\funcion;

use bloquesModelo\bloqueModelo1\Funcion;

class EliminarPlugin {
	var $miConfigurador;
	var $miSql;
	var $conexion;
	var $rutaAbsolutaSitio;
	var $rutaAbsolutaBloque;
	var $rutaAbsolutaCss;
	var $rutaAbsolutaScript;
	var $extension;
	function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
		
		$this->conexion = $this->miConfigurador->fabricaConexiones->getRecursoDB ( 'estructura' );
		
		/**
		 * Ruta Absoluta Sitio
		 */
		$this->rutaAbsolutaSitio = $this->miConfigurador->configuracion ['raizDocumento'];
		/**
		 * Datos Actuales del Bloque
		 */
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'informacionBloquePlugins' );
		$informacionActual = $this->conexion->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		$informacionActual = $informacionActual [0];
		
		foreach ( $informacionActual as $key => $valor )
			$this->infoBloqueActual [$key] = trim ( $valor );
	}
	function procesarEliminarPlugin() {
		
		/**
		 * 1.Eliminar Plugin a directorio
		 */
		$this->eliminarPluginUbicacion ();
		
		/**
		 * 2.Editar Ficheros Script y Estilo Css
		 */
		$this->editarArchivos ();
		
		return true;
	}
	function editarArchivos() {
		if ($this->extension == 'css') {
			
			$this->reescribirArchivo ( $this->rutaAbsolutaCss . "Estilo.php" );
		}
		if ($this->extension == 'js') {
			
			$this->reescribirArchivo ( $this->rutaAbsolutaScript . "Script.php" );
		}
	}
	function reescribirArchivo($archivo) {
		$contenidoArchivo = file_get_contents ( $archivo );
		
		$contenidoArchivo = explode ( "\n", $contenidoArchivo );
		
		if ($this->extension == 'css') {
			$cadenaComparar = '$estilo [$indice ++] = "' . $_REQUEST ['id'] . '";';
			
			foreach ( $contenidoArchivo as $valor ) {
				
				if ($valor != $cadenaComparar) {
					
					$contenidoReescribir [] = $valor;
				}
			}
		} elseif ($this->extension == 'js') {
			$cadenaComparar = '$funcion [$indice ++] = "' . $_REQUEST ['id'] . '";';
			
			foreach ( $contenidoArchivo as $valor ) {
				
				if ($valor != $cadenaComparar) {
					
					$contenidoReescribir [] = $valor;
				}
			}
		}
		
		$archivoReescribir = fopen ( $archivo, "w+b" );
		
		foreach ( $contenidoReescribir as $linea ) {
			fwrite ( $archivoReescribir, $linea . "\n" );
		}
		
		fclose ( $archivoReescribir );
	}
	function eliminarPluginUbicacion() {
		$trozos = explode ( ".", $_REQUEST ['id'] );
		$this->extension = end ( $trozos );
		
		if ($this->extension == 'css') {
			
			$this->rutaAbsolutaBloque = $this->rutaAbsolutaSitio . '/blocks/' . $this->infoBloqueActual ['grupo'] . '/' . $this->infoBloqueActual ['nombre'] . "/";
			
			$this->rutaAbsolutaCss = $this->rutaAbsolutaBloque . "frontera/css/";
			
			$ruta_absoluta_archivo = $this->rutaAbsolutaCss . $_REQUEST ['id'];
		} elseif ($this->extension == 'js') {
			
			$this->rutaAbsolutaBloque = $this->rutaAbsolutaSitio . '/blocks/' . $this->infoBloqueActual ['grupo'] . '/' . $this->infoBloqueActual ['nombre'] . "/";
			
			$this->rutaAbsolutaScript = $this->rutaAbsolutaBloque . "frontera/script/";
			
			$ruta_absoluta_archivo = $this->rutaAbsolutaScript . $_REQUEST ['id'];
		}
		
		unlink ( $ruta_absoluta_archivo );
	}
}

$miRegistrador = new EliminarPlugin ( $this->sql );

$resultado = $miRegistrador->procesarEliminarPlugin ();

?>