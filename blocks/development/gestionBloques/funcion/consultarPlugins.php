<?php

namespace development\gestionBloques\funcion;

class ConsultarPlugins {
	var $miConfigurador;
	var $miSql;
	var $conexion;
	var $infoActualBloque;
	var $directorioInstalacion = "blocks/";
	var $rutaBloque;
	var $arregloPlugins = false;
	function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
	}
	function procesarConsultaPlugins() {
		
		/**
		 * 1.Consultar Información Actual del Bloque
		 */
		$this->consultarInfoActualBloque ();
		
		/**
		 * 2.Estructurar Ruta Bloque
		 */
		$this->rutaBloque = $this->directorioInstalacion . $this->infoActualBloque ['grupo'] . "/" . $this->infoActualBloque ['nombre'];
		
		/**
		 * En listar Javascript y Css
		 */
		
		$this->enlistarConstenidoBloque ();
		
		/**
		 * 3.
		 * Restrucutación Resultados Retorno Tabla
		 */
		$this->retornoTabla ();
	}
	function retornoTabla() {
		$tabla = new \stdClass ();
		
		$page = $_REQUEST ['page'];
		
		$limit = $_REQUEST ['rows'];
		
		$sidx = $_REQUEST ['sidx'];
		
		$sord = $_REQUEST ['sord'];
		
		if (! $sidx)
			$sidx = 1;
		
		$filas = count ( $this->arregloPlugins );
		
		if ($filas > 0 && $limit > 0) {
			$total_pages = ceil ( $filas / $limit );
		} else {
			$total_pages = 0;
		}
		
		if ($page > $total_pages) {
			$page = $total_pages;
		}
		$start = $limit * $page - $limit;
		if ($this->arregloPlugins != false) {
			$tabla->page = $page;
			$tabla->total = $total_pages;
			$tabla->records = $filas;
			
			$i = 1;
			$j = 0;
			
			foreach ( $this->arregloPlugins as $row ) {
				$tabla->rows [$j] ['nombre'] = $row ['nombre'];
				$tabla->rows [$j] ['cell'] = array (
						$row ['tipo'],
						$row ['nombre'],
						" " 
				);
				$i ++;
				$j ++;
			}
			
			$tabla = json_encode ( $tabla );
		} else {
			$tabla->page = 1;
			$tabla->total = 1;
			$tabla->records = 1;
			
			$tabla->rows [0] ['nombre'] = ' ';
			$tabla->rows [0] ['cell'] = array (
					" ",
					" ",
					" " 
			);
			$tabla = json_encode ( $tabla );
		}
		
		echo $tabla;
	}
	function enlistarConstenidoBloque() {
		$rutaCss = $this->rutaBloque . "/frontera/css/*";
		$rutaJs = $this->rutaBloque . "/frontera/script/*";
		$i = 0;
		foreach ( glob ( $rutaCss ) as $archivos_carpeta ) {
			if (! is_dir ( $archivos_carpeta )) {
				
				$var1 = explode ( "/", $archivos_carpeta );
				
				$archivo = $var1 [count ( $var1 ) - 1];
				
				$var2 = explode ( ".", $archivo );
				
				$extension = $var2 [count ( $var2 ) - 1];
				
				if ($extension == "css") {
					
					$this->arregloPlugins [$i] ["nombre"] = $archivo;
					$this->arregloPlugins [$i] ["tipo"] = "css";
					$i ++;
				}
			}
		}
		
		foreach ( glob ( $rutaJs ) as $archivos_carpeta ) {
			if (! is_dir ( $archivos_carpeta )) {
				
				$var1 = explode ( "/", $archivos_carpeta );
				
				$archivo = $var1 [count ( $var1 ) - 1];
				
				$var2 = explode ( ".", $archivo );
				
				$extension = $var2 [count ( $var2 ) - 1];
				
				if ($extension == "js") {
					
					$this->arregloPlugins [$i] ["nombre"] = $archivo;
					$this->arregloPlugins [$i] ["tipo"] = "javascript";
					$i ++;
				}
			}
		}
	}
	function consultarInfoActualBloque() {
		$this->conexion = $this->miConfigurador->fabricaConexiones->getRecursoDB ( 'estructura' );
		if (! $this->conexion) {
			error_log ( "No se conectó" );
			$resultado = false;
		}
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'informacionBloque' );
		
		$resultado = $this->conexion->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		
		if ($resultado) {
			$resultado = $resultado [0];
			foreach ( $resultado as $valor => $val )
				
				$this->infoActualBloque [$valor] = trim ( $val );
		}
	}
}

$miRegistrador = new ConsultarPlugins ( $this->sql );

$resultado = $miRegistrador->procesarConsultaPlugins ();

?>