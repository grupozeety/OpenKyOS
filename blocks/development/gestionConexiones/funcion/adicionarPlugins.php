<?php

namespace development\gestionBloques\funcion;

use bloquesModelo\bloqueModelo1\Funcion;

class CrearPlugin {
	var $miConfigurador;
	var $miSql;
	var $conexion;
	var $rutaNuevoBloque;
	var $rutaFrontera;
	var $rutaScript;
	var $rutaCss;
	var $rutaControl;
	var $rutaEntidad;
	var $rutaLocale;
	var $rutaIdioma;
	var $archivo;
	var $rutaAbsolutaSitio;
	var $rutaAbsolutaBloque;
	var $rutaAbsolutaCss;
	var $rutaAbsolutaScript;
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
	function procesarAdicionPlugin() {
		if (! empty ( $_FILES )) {
			
			/**
			 * 1.agregar Plugin a directorio
			 */
			$this->agregarPluginUbicacion ();
			
			/**
			 * 2.Editar Ficheros Script y Estilo Css
			 */
			$this->editarArchivos ();
			
			return true;
		} else {
			
			return true;
		}
	}
	function editarArchivos() {
		if ($_REQUEST ['tipo'] == 'css') {
			
			$this->reescribirArchivo ( $this->rutaAbsolutaCss . "Estilo.php" );
		}
		if ($_REQUEST ['tipo'] == 'javascript') {
			
			$this->reescribirArchivo ( $this->rutaAbsolutaScript . "Script.php" );
		}
	}
	function reescribirArchivo($archivo) {
		$contenidoArchivo = file_get_contents ( $archivo );
		
		$contenidoArchivo = explode ( "\n", $contenidoArchivo );
		
		if ($_REQUEST ['tipo'] == 'css') {
			$cadenaAgregar = '$estilo [$indice ++] = "' . $this->archivo ['name'] . '";';
			
			$i = 0;
			foreach ( $contenidoArchivo as $key => $valor ) {
				
				if ($key == 8) {
					
					$contenidoReescribir [$i] = $valor;

					$i ++;
					$contenidoReescribir [$i] = $cadenaAgregar;
				} else {
					
					$contenidoReescribir [$i] = $valor;
				}
				
				$i ++;
			}
		} elseif ($_REQUEST ['tipo'] == 'javascript') {
			$cadenaAgregar = '$funcion [$indice ++] = "' . $this->archivo ['name'] . '";';
			
			$i = 0;
			foreach ( $contenidoArchivo as $key => $valor ) {
				
				if ($key == 13) {
					
					$contenidoReescribir [$i] = $valor;
					
					$i ++;
					$contenidoReescribir [$i] = $cadenaAgregar;
				} else {
					
					$contenidoReescribir [$i] = $valor;
				}
				
				$i ++;
			}
		}
	
		
		$archivoReescribir = fopen ( $archivo, "w+b" );
		
		foreach ( $contenidoReescribir as $linea ) {
			fwrite ( $archivoReescribir, $linea . "\n" );
		}
		
		fclose ( $archivoReescribir );
	}
	function agregarPluginUbicacion() {
		if ($_REQUEST ['tipo'] == 'css') {
			
			$this->archivo = $_FILES ['archivo'];
			$trozos = explode ( ".", $this->archivo ['name'] );
			$extension = end ( $trozos );
			($extension != 'css') ? $this->retornarError ( 'Error al Extension Plugin .css' ) : $this->archivo = $_FILES ['archivo'];
			
			$tamano = $this->archivo ['size'];
			$tipo = $this->archivo ['type'];
			$archivo = $this->archivo ['name'];
			
			$this->rutaAbsolutaBloque = $this->rutaAbsolutaSitio . '/blocks/' . $this->infoBloqueActual ['grupo'] . '/' . $this->infoBloqueActual ['nombre'] . "/";
			
			$this->rutaAbsolutaCss = $this->rutaAbsolutaBloque . "frontera/css/";
			
			$ruta_absoluta_archivo = $this->rutaAbsolutaCss . $archivo;
			
			if (! copy ( $this->archivo ['tmp_name'], $ruta_absoluta_archivo )) {
				
				$this->retornarError ( 'Error al cargar Plugin' );
			}
		} elseif ($_REQUEST ['tipo'] == 'javascript') {
			
			$this->archivo = $_FILES ['archivo'];
			$trozos = explode ( ".", $this->archivo ['name'] );
			$extension = end ( $trozos );
			($extension != 'js') ? $this->retornarError ( 'Error al Extension Plugin .js ' ) : $this->archivo = $_FILES ['archivo'];
			
			$tamano = $this->archivo ['size'];
			$tipo = $this->archivo ['type'];
			$archivo = $this->archivo ['name'];
			
			$this->rutaAbsolutaBloque = $this->rutaAbsolutaSitio . '/blocks/' . $this->infoBloqueActual ['grupo'] . '/' . $this->infoBloqueActual ['nombre'] . "/";
			
			$this->rutaAbsolutaScript = $this->rutaAbsolutaBloque . "frontera/script/";
			
			$ruta_absoluta_archivo = $this->rutaAbsolutaScript . $archivo;
			
			if (! copy ( $this->archivo ['tmp_name'], $ruta_absoluta_archivo )) {
				
				$this->retornarError ( 'Error al cargar Plugin' );
			}
		}
	}
	function retornarError($mensaje = '') {
		echo json_encode ( $mensaje );
		exit ();
	}
	
}

$miRegistrador = new CrearPlugin ( $this->sql );

$resultado = $miRegistrador->procesarAdicionPlugin ();

?>