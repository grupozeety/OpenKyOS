<?php

namespace complementos\registrarPortatil\funcion;

class CargadorDatos {
	var $miConfigurador;
	var $equipo;
	var $parser;
	var $procesado;
	var $elementos;
	var $valores;
	var $archivoXML;
	var $archivoTXT;
	var $lenguaje;
	var $sql;
	var $conexion;
	var $arregloAtributos;
	var $campos = array (
			'System Information' => array (
					'serial' => 'Serial Number: ',
					'fabricante' => 'Manufacturer: ',
					'modelo' => 'Product Name: ',
					'sku' => 'SKU Number: ' 
			),
			'Base Board Information' => array (
					'board_fabricante' => 'Manufacturer: ',
					'board_nombre' => 'Product Name: ',
					'board_version' => 'Version: ',
					'board_serial' => 'Serial Number: ' 
			),
			'Processor Information' => array (
					'cpu_version' => 'Version: ',
					'cpu_tipo' => 'Family: ',
					'cpu_fabricante' => 'Manufacturer:',
					'cpu_cantidad_nucleos' => 'Core Count: ',
					'cpu_velocidad' => 'Current Speed: ' 
			),
			'Memory Device' => array (
					'memoria_capacidad' => 'Size: ',
					'memoria_factorforma' => 'Form Factor: ',
					'memoria_tipo' => 'Type: ',
					'memoria_velocidad' => 'Speed: ',
					'memoria_fabricante' => 'Manufacturer: ',
					'memoria_serial' => 'Serial Number: ' 
			),
			'Portable Battery' => array (
					'bateria_tipo' => 'Chemistry: ',
					'bateria_version' => 'Name: ',
					'bateria_serial' => 'Serial Number: ',
					'bateria_autonomia' => 'Design Capacity: ' 
			) 
	);
	function __construct($lenguaje, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->lenguaje = $lenguaje;
		$this->sql = $sql;
		$this->conexion = $this->miConfigurador->fabricaConexiones->getRecursoDB ( 'conexionesdigitales', 'tabla' );
		
		$this->archivoXML = '0062.xml';
		$this->archivoTXT = 'b.txt';
	}
	function procesarArchivoXML() {
		$this->leerArchivo ( $this->archivoXML );
		
		$this->parser = xml_parser_create ();
		xml_parser_set_option ( $this->parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option ( $this->parser, XML_OPTION_SKIP_WHITE, 1 );
		xml_parse_into_struct ( $this->parser, $this->procesado, $this->arregloAtributos );
		xml_parser_free ( $this->parser );
		
		foreach ( $this->arregloAtributos as $clave => $valor ) {
			
			if (isset ( $valor ['attributes'] ) && isset ( $valor ['attributes'] ['id'] )) {
				
				switch($valor ['attributes'] ['id']){
					
					case 'cpu':
						$this->datosCPU ( $clave, $valor );
						break;
					case 'disk':
						$this->datosDisco ( $clave, $valor );
					
				}
				
			}
		}
		
		var_dump ( $this->equipo );
		var_dump ( $this->arregloAtributos );
	}
	
	
	function datosDisco($clave, $valor) {
		// Datos Disco
		$indice = $clave + 1;
	
		do {
				
			if ($this->arregloAtributos [$indice] ['tag'] == 'vendor') {
				$this->equipo ['disco_fabricante'] = $this->arregloAtributos [$indice] ['value'];
			} elseif ($this->arregloAtributos [$indice] ['tag'] == 'description') {
				$this->equipo ['disco_tipo'] = $this->arregloAtributos [$indice] ['value'];
			}elseif($this->arregloAtributos [$indice] ['tag'] == 'product'){
				$this->equipo ['disco_version'] = $this->arregloAtributos [$indice] ['value'];
			}elseif($this->arregloAtributos [$indice] ['tag'] == 'serial'){
				$this->equipo ['disco_version'] = $this->arregloAtributos [$indice] ['value'];
			}elseif($this->arregloAtributos [$indice] ['tag'] == 'size'){
				$this->equipo ['disco_capacidad'] = $this->arregloAtributos [$indice] ['value'];
			}
				
			$indice ++;
		} while ( $this->arregloAtributos [$indice] ['type'] == 'complete' );
	
		
	}
	
	
	
	function datosCPU($clave, $valor) {
		// Datos CPU
		$indice = $clave + 1;
		
		do {
			
			if ($this->arregloAtributos [$indice] ['tag'] == 'physid') {
				$this->equipo ['cpu_cantidad_fisico'] = $this->arregloAtributos [$indice] ['value'];
			} elseif ($this->arregloAtributos [$indice] ['tag'] == 'width') {
				$this->equipo ['bits'] = $this->arregloAtributos [$indice] ['value'];
			}
			
			$indice ++;
		} while ( $this->arregloAtributos [$indice] ['type'] == 'complete' );
		
		// Datos banderas
		
		if ($this->arregloAtributos [$indice] ['tag'] == 'capabilities') {
			do {
				
				if (isset ( $this->arregloAtributos [$indice] ['attributes'] ) && isset ( $this->arregloAtributos [$indice] ['attributes'] ['id'] )) {
					
					switch ($this->arregloAtributos [$indice] ['attributes'] ['id']) {
						
						case 'pae' :
							$this->equipo ['cpu_flag_pae'] = $this->arregloAtributos [$indice] ['value'];
							break;
						case 'sse' :
							$this->equipo ['cpu_flag_sse'] = $this->arregloAtributos [$indice] ['value'];
							break;
						case 'nx' :
							$this->equipo ['cpu_flag_nx'] = $this->arregloAtributos [$indice] ['value'];
							break;
					}
				}
				
				$indice ++;
			} while ( $this->arregloAtributos [$indice] ['type'] == 'complete' );
		}
	}
	function leerArchivo($archivo) {
		$this->procesado = file_get_contents ( $archivo );
	}
	function getListadoAtributos() {
		// 1. Rescatar los nombres de las columnas
		$cadenaSql = $this->sql->getCadenaSql ( 'nombresColumnas' );
		
		$arreglo = $this->conexion->ejecutarAcceso ( $cadenaSql, "acceso" );
	}
	function procesarArchivoTXT() {
		$this->leerArchivo ( $this->archivoTXT );
		
		foreach ( $this->campos as $llave => $valor ) {
			
			foreach ( $valor as $indice => $dato ) {
				$x = strpos ( $this->procesado, $dato, strpos ( $this->procesado, $llave ) ) + strlen ( $dato );
				$y = strpos ( $this->procesado, "\n", $x );
				$valor = substr ( $this->procesado, $x, $y - $x );
				
				if ($llave == 'Memory Device' && $valor = 'No Module Installed') {
					$x = strpos ( $this->procesado, $dato, strpos ( $this->procesado, $llave, $y ) ) + strlen ( $dato );
					$y = strpos ( $this->procesado, "\n", $x );
					$valor = substr ( $this->procesado, $x, $y - $x );
				}
				$this->equipo [$indice] = $valor;
			}
		}
	}
}

$myParser = new CargadorDatos ( $this->lenguaje, $this->sql );

// $resultado = $myParser->procesarArchivoTXT ();
$resultado = $myParser->procesarArchivoXML ();


