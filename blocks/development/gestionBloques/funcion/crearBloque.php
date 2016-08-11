<?php

namespace development\gestionBloques\funcion;

use bloquesModelo\bloqueModelo1\Funcion;

class CrearBloques {
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
	var $namespace;
	function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
	}
	function procesarCrearBloque() {
		
		/**
		 * 1.Creación Directorios
		 */
		$this->crearDirectorio ();
		
		/**
		 * 2.Creación Ficheros
		 */
		$this->crearFicheros ();
		
		/**
		 * 3.Registro DB Bloque
		 */
		$this->procesarCreacionBloqueSql ();
		
		return true;
	}
	function crearFicheros() {
		$rutaPlantillas = $this->miConfigurador->getVariableConfiguracion ( "rutaUrlBloque" ) . "funcion/plantillasFicheros/";
		
		/**
		 * Fichero bloque.php
		 */
		$plantillaBloque = file_get_contents ( $rutaPlantillas . "bloque" );
		$ficheroBloque = fopen ( $this->rutaNuevoBloque . "/bloque.php", "w" );
		fwrite ( $ficheroBloque, $plantillaBloque );
		fclose ( $ficheroBloque );
		$this->reescribirNamespace ( $this->rutaNuevoBloque . "/bloque.php" );
		$arregloFicherosPermisos [] = $this->rutaNuevoBloque . "/bloque.php";
		
		/**
		 * Fichero Frontera.class.php
		 */
		
		$plantillaFrontera = file_get_contents ( $rutaPlantillas . "frontera" );
		$ficheroFrontera = fopen ( $this->rutaControl . "/Frontera.class.php", "w" );
		fwrite ( $ficheroFrontera, $plantillaFrontera );
		fclose ( $ficheroFrontera );
		$this->reescribirNamespace ( $this->rutaControl . "/Frontera.class.php" );
		$arregloFicherosPermisos [] = $this->rutaControl . "/Frontera.class.php";
		
		/**
		 * Fichero Entidad.class.php
		 */
		
		$plantillaEntidad = file_get_contents ( $rutaPlantillas . "entidad" );
		$ficheroEntidad = fopen ( $this->rutaControl . "/Entidad.class.php", "w" );
		fwrite ( $ficheroEntidad, $plantillaEntidad );
		fclose ( $ficheroEntidad );
		$this->reescribirNamespace ( $this->rutaControl . "/Entidad.class.php" );
		$arregloFicherosPermisos [] = $this->rutaControl . "/Entidad.class.php";
		
		/**
		 * Fichero Lenguaje.class.php
		 */
		
		$plantillaLenguaje = file_get_contents ( $rutaPlantillas . "lenguaje" );
		$ficheroLenguaje = fopen ( $this->rutaControl . "/Lenguaje.class.php", "w" );
		fwrite ( $ficheroLenguaje, $plantillaLenguaje );
		fclose ( $ficheroLenguaje );
		$this->reescribirNamespace ( $this->rutaControl . "/Lenguaje.class.php" );
		$arregloFicherosPermisos [] = $this->rutaControl . "/Lenguaje.class.php";
		
		/**
		 * Fichero Sql.class.php
		 */
		
		$plantillaSql = file_get_contents ( $rutaPlantillas . "sql" );
		$ficheroSql = fopen ( $this->rutaControl . "/Sql.class.php", "w" );
		fwrite ( $ficheroSql, $plantillaSql );
		fclose ( $ficheroSql );
		$this->reescribirNamespace ( $this->rutaControl . "/Sql.class.php" );
		$arregloFicherosPermisos [] = $this->rutaControl . "/Sql.class.php";
		
		/**
		 * Directorio Frontera/
		 * Fichero miFormulario.php
		 */
		
		$plantillaMiFormulario = file_get_contents ( $rutaPlantillas . "miformulario" );
		$ficheroMiFormulario = fopen ( $this->rutaFrontera . "/miFormulario.php", "w" );
		fwrite ( $ficheroMiFormulario, $plantillaMiFormulario );
		fclose ( $ficheroMiFormulario );
		$this->reescribirNamespace ( $this->rutaFrontera . "/miFormulario.php", " \ frontera " );
		$arregloFicherosPermisos [] = $this->rutaFrontera . "/miFormulario.php";
		
		/**
		 * Directorio Frontera/script
		 * Fichero ready.js
		 */
		
		$plantillaReady = file_get_contents ( $rutaPlantillas . "ready" );
		$ficheroReady = fopen ( $this->rutaScript . "/ready.js", "w" );
		fwrite ( $ficheroReady, $plantillaReady );
		fclose ( $ficheroReady );
		$arregloFicherosPermisos [] = $this->rutaScript . "/ready.js";
		
		/**
		 * Directorio Frontera/script
		 * Fichero ajax.php
		 */
		
		$plantillaAjax = file_get_contents ( $rutaPlantillas . "ajax" );
		$ficheroAjax = fopen ( $this->rutaScript . "/ajax.php", "w" );
		fwrite ( $ficheroAjax, $plantillaAjax );
		fclose ( $ficheroAjax );
		$arregloFicherosPermisos [] = $this->rutaScript . "/ajax.php";
		
		/**
		 * Directorio Frontera/script
		 * Fichero Script.php
		 */
		
		$plantillaScript = file_get_contents ( $rutaPlantillas . "script" );
		$ficheroScript = fopen ( $this->rutaScript . "/Script.php", "w" );
		fwrite ( $ficheroScript, $plantillaScript );
		fclose ( $ficheroScript );
		$arregloFicherosPermisos [] = $this->rutaScript . "/Script.php";
		
		/**
		 * Directorio Frontera/css
		 * Fichero Estilo.php
		 */
		
		$plantillaEstilo = file_get_contents ( $rutaPlantillas . "estilo" );
		$ficheroEstilo = fopen ( $this->rutaCss . "/Estilo.php", "w" );
		fwrite ( $ficheroEstilo, $plantillaEstilo );
		fclose ( $ficheroEstilo );
		$arregloFicherosPermisos [] = $this->rutaCss . "/Estilo.php";
		
		/**
		 * Directorio Frontera/css
		 * Fichero estiloBloque.css
		 */
		
		$plantillaEstiloBloque = file_get_contents ( $rutaPlantillas . "estilobloque" );
		$ficheroEstiloBloque = fopen ( $this->rutaCss . "/estiloBloque.css", "w" );
		fwrite ( $ficheroEstiloBloque, $plantillaEstiloBloque );
		fclose ( $ficheroEstiloBloque );
		$arregloFicherosPermisos [] = $this->rutaCss . "/estiloBloque.css";
		
		/**
		 * Directorio Frontera/locale/es_es
		 * Fichero Mensaje.php
		 */
		
		$plantillaMensaje = file_get_contents ( $rutaPlantillas . "mensaje" );
		$ficheroMensaje = fopen ( $this->rutaIdioma . "/Mensaje.php", "w" );
		fwrite ( $ficheroMensaje, $plantillaMensaje );
		fclose ( $ficheroMensaje );
		$arregloFicherosPermisos [] = $this->rutaIdioma . "/Mensaje.php";
		
		/**
		 * Directorio Entidad
		 * Fichero procesarAjax.php
		 */
		
		$plantillaProcesarAjax = file_get_contents ( $rutaPlantillas . "procesarajax" );
		$ficheroProcesarAjax = fopen ( $this->rutaEntidad . "/procesarAjax.php", "w" );
		fwrite ( $ficheroProcesarAjax, $plantillaProcesarAjax );
		fclose ( $ficheroProcesarAjax );
		$this->reescribirNamespace ( $this->rutaEntidad . "/procesarAjax.php", " \ entidad" );
		$arregloFicherosPermisos [] = $this->rutaEntidad . "/procesarAjax.php";
		
		/**
		 * Directorio Entidad
		 * Fichero Redireccionador.php
		 */
		
		$plantillaRedireccionador = file_get_contents ( $rutaPlantillas . "redireccionador" );
		$ficheroRedireccionador = fopen ( $this->rutaEntidad . "/Redireccionador.php", "w" );
		fwrite ( $ficheroRedireccionador, $plantillaRedireccionador );
		fclose ( $ficheroRedireccionador );
		$this->reescribirNamespace ( $this->rutaEntidad . "/Redireccionador.php", " \ entidad" );
		$arregloFicherosPermisos [] = $this->rutaEntidad . "/Redireccionador.php";
		
		/**
		 * Directorio Entidad
		 * Fichero procesarFormulario.php
		 */
		
		$plantillaProcesarFormulario = file_get_contents ( $rutaPlantillas . "procesarformulario" );
		$ficheroProcesarFormulario = fopen ( $this->rutaEntidad . "/procesarFormulario.php", "w" );
		fwrite ( $ficheroProcesarFormulario, $plantillaProcesarFormulario );
		fclose ( $ficheroProcesarFormulario );
		$this->reescribirNamespace ( $this->rutaEntidad . "/procesarFormulario.php", " \ entidad" );
		$arregloFicherosPermisos [] = $this->rutaEntidad . "/procesarFormulario.php";
		
		/**
		 * Fichero index.php
		 */
		
		$plantillaIndex = file_get_contents ( $rutaPlantillas . "index" );
		$ficheroIndex = fopen ( $this->rutaNuevoBloque . "/index.php", "w" );
		fwrite ( $ficheroIndex, $plantillaIndex );
		fclose ( $ficheroIndex );
		$arregloFicherosPermisos [] = $this->rutaNuevoBloque . "/index.php";
		
		$this->permisosFicheros ( $arregloFicherosPermisos );
	}
	function permisosFicheros($variableFicheros = '') {
		if (is_array ( $variableFicheros ) == true) {
			
			foreach ( $variableFicheros as $valor ) {
				
				chmod ( $valor, 0777 );
			}
		} else {
			
			chmod ( $variableFicheros, 0777 );
		}
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
	function crearDirectorio() {
		$DirectorioInstalacion = "blocks/";
		
		/**
		 * Creación Grupo Bloque
		 */
		
		$_REQUEST ['grupo'] = str_replace ( ' ', '', $_REQUEST ['grupo'] );
		
		if ($_REQUEST ['grupo'] != '') {
			
			$gruposBusqueda = strpos ( $_REQUEST ['grupo'], "/" );
			
			if ($gruposBusqueda) {
				
				$arrayGrupo = explode ( "/", $_REQUEST ['grupo'] );
				
				$cadenaGrupo = '';
				
				foreach ( $arrayGrupo as $valor ) {
					
					$rutaGrupo = ($cadenaGrupo != '') ? $DirectorioInstalacion . $cadenaGrupo . "/" . $valor : $DirectorioInstalacion . $valor;
					mkdir ( $rutaGrupo, 0777, true );
					chmod ( $rutaGrupo, 0777 );
					$cadenaGrupo .= ($cadenaGrupo != '') ? "/" . $valor : $valor;
				}
			} else {
				
				$rutaGrupo = $DirectorioInstalacion . $_REQUEST ['grupo'];
				mkdir ( $rutaGrupo, 0777, true );
				chmod ( $rutaGrupo, 0777 );
			}
		}
		
		/**
		 * Creación Bloque
		 */
		
		$_REQUEST ['nombre'] = str_replace ( ' ', '', $_REQUEST ['nombre'] );
		
		$this->rutaNuevoBloque = ($_REQUEST ['grupo'] != '') ? $DirectorioInstalacion . $_REQUEST ['grupo'] . "/" . $_REQUEST ['nombre'] : $DirectorioInstalacion . $_REQUEST ['nombre'];
		
		$this->namespace = ($_REQUEST ['grupo'] != '') ? $_REQUEST ['grupo'] . "/" . $_REQUEST ['nombre'] : $_REQUEST ['nombre'];
		$this->namespace = str_replace ( ' ', '', str_replace ( '/', ' \ ', $this->namespace ) );
		
		mkdir ( $this->rutaNuevoBloque, 0777, true );
		chmod ( $this->rutaNuevoBloque, 0777 );
		
		/**
		 * Estructuramiento Bloque:
		 *
		 * frontera
		 * ->css
		 * ->script
		 * control
		 *
		 * entidad
		 *
		 * bloque.php
		 */
		$this->rutaFrontera = $this->rutaNuevoBloque . "/frontera";
		mkdir ( $this->rutaFrontera, 0777 );
		chmod ( $this->rutaFrontera, 0777 );
		
		$this->rutaCss = $this->rutaFrontera . "/css";
		mkdir ( $this->rutaCss, 0777 );
		chmod ( $this->rutaCss, 0777 );
		
		$this->rutaScript = $this->rutaFrontera . "/script";
		mkdir ( $this->rutaScript, 0777 );
		chmod ( $this->rutaScript, 0777 );
		
		$this->rutaLocale = $this->rutaFrontera . "/locale";
		mkdir ( $this->rutaLocale, 0777 );
		chmod ( $this->rutaLocale, 0777 );
		
		$this->rutaIdioma = $this->rutaLocale . "/es_es";
		mkdir ( $this->rutaIdioma, 0777 );
		chmod ( $this->rutaIdioma, 0777 );
		
		$this->rutaControl = $this->rutaNuevoBloque . "/control";
		mkdir ( $this->rutaControl, 0777 );
		chmod ( $this->rutaControl, 0777 );
		
		$this->rutaEntidad = $this->rutaNuevoBloque . "/entidad";
		mkdir ( $this->rutaEntidad, 0777 );
		chmod ( $this->rutaEntidad, 0777 );
	}
	function procesarCreacionBloqueSql() {
		$this->conexion = $this->miConfigurador->fabricaConexiones->getRecursoDB ( 'estructura' );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarBloque' );
		$resultado = $this->conexion->ejecutarAcceso ( $cadenaSql, 'acceso' );
	}
}

$miRegistrador = new CrearBloques ( $this->sql );

$resultado = $miRegistrador->procesarCrearBloque ();

?>