<?php

namespace complementos\registrarPortatil\funcion;

class CargadorDatos {
	var $miConfigurador;
	var $equipo=array(
			'estado_registro'=>'activo',
			'estado_equipo'=>'excelente',
			'tipo_equipo'=>'Computador portatil',
			'ancho'=>'34.54cm',
			'largo'=>'24.15cm',
			'alto'=>'2.29 cm',
			'peso'=>'1.9 kg',
			'pantalla_tipo'=>'HD SVA anti-brillo retroiluminada LED',
			'pantalla_tamanno'=>'14" diagonal ',
			'pantalla_resolucion'=>'1366 x 768',
			'teclado_tipo'=>'Tamaño completo con textura estilo isla negro',
			'teclado_idioma'=>'Español',
			'mouse_tipo'=>'Touchpad con capacidad multi-touch',
			'mouse_fabricante'=>'PixArt',
			'camara_tipo'=>'Integrada',
			'camara_version'=>'HP Truevision HD',
			'audio_tipo'=>'Mono/Estereo',
			'audio_version'=>'DTS Sound+',
			'audio_fabricante'=>'Advanced Micro Devices, Inc. [AMD/ATI]',
			'audio_conector'=>'headphone/microphone combo jack',
			'parlantes_tipo'=>'Integrado',
			'parlantes_version'=>'Dual speakers',
			'red_estandar'=>'Ethernet',
			'red_tipo_conector'=>'RJ-45',
			'red_ipv4'=>'',
			'red_ipv6'=>'',
			'wifi_estandar'=>'802.11b/g/n',
			'wifi_codificacion'=>'WPA-PSK + WPA2-PSK',
			'bluetooth_velocidad'=>'12Mbit/s',
			'puerto_usb2_total'=>'2',
			'puerto_usb3_total'=>'3',
			'puerto_hdmi_total'=>'1',
			'puerto_vga_total'=>'1',
			'slot_expansion_tipo'=>'multi-format digital media reader(soporta SD, SDHC, SDXC)',
			'alimentacion_tipo'=>'AC',
			'alimentacion_dispositivo'=>'Adaptador Smart AC',
			'alimentacion_voltaje'=>'100 v a 120 v',
			'alimentacion_frecuencia'=>'50 Hz a 60 Hz',
			'bateria_certificacion'=>'FCC - ENERGY STAR - UL'
			
	);
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
				
				switch ($valor ['attributes'] ['id']) {
					
					case 'cpu' :
						$this->datosCPU ( $clave, $valor );
						break;
					case 'disk' :
						$this->datosDisco ( $clave, $valor );
						break;
					
					case 'display' :
						$this->datosVideo ( $clave, $valor );
						break;
					
					case 'network' :
						$this->datosNetwork ( $clave, $valor );
						break;
					
					case 'usb' :
						$this->datosBluetooth ( $clave, $valor );
						break;
				}
			}
		}
		
		//var_dump ( $this->equipo );
		//var_dump ( $this->arregloAtributos );
	}
	function datosNetwork($clave, $valor) {
		$indice = $clave + 1;
		
		// echo strpos($this->arregloAtributos [$indice] ['value'],'Ethernet');exit;
		
		if ($this->arregloAtributos [$indice] ['tag'] == 'description' && strpos ( $this->arregloAtributos [$indice] ['value'], 'Ethernet' ) === 0) {
			$red = 'red';
		} else {
			$red = 'wifi';
		}
		
		do {
			
			if ($this->arregloAtributos [$indice] ['tag'] == 'vendor') {
				$this->equipo [$red . '_fabricante'] = $this->arregloAtributos [$indice] ['value'];
			} elseif ($this->arregloAtributos [$indice] ['tag'] == 'description') {
				$this->equipo [$red . '_tipo'] = $this->arregloAtributos [$indice] ['value'];
			} elseif ($this->arregloAtributos [$indice] ['tag'] == 'product') {
				$this->equipo [$red . '_version'] = $this->arregloAtributos [$indice] ['value'];
			} elseif ($this->arregloAtributos [$indice] ['tag'] == 'serial') {
				$this->equipo [$red . '_serial'] = $this->arregloAtributos [$indice] ['value'];
			} elseif ($this->arregloAtributos [$indice] ['tag'] == 'product') {
				$this->equipo [$red . '_version'] = $this->arregloAtributos [$indice] ['value'];
			} elseif ($this->arregloAtributos [$indice] ['tag'] == 'capacity') {
				$this->equipo [$red . '_velocidad'] = $this->arregloAtributos [$indice] ['value'];
			}
			$indice ++;
		} while ( $this->arregloAtributos [$indice] ['type'] == 'complete' );
	}
	function datosBluetooth($clave, $valor) {
		$indice = $clave + 1;
		
		// echo strpos($this->arregloAtributos [$indice] ['value'],'Ethernet');exit;
		
		if ($this->arregloAtributos [$indice] ['tag'] == 'description' && strpos ( $this->arregloAtributos [$indice] ['value'], 'Bluetooth' )) {
			$red = 'bluetooth';
			
			do {
				
				if ($this->arregloAtributos [$indice] ['tag'] == 'vendor') {
					$this->equipo [$red . '_fabricante'] = $this->arregloAtributos [$indice] ['value'];
				} elseif ($this->arregloAtributos [$indice] ['tag'] == 'description') {
					$this->equipo [$red . '_tipo'] = $this->arregloAtributos [$indice] ['value'];
				} elseif ($this->arregloAtributos [$indice] ['tag'] == 'product') {
					$this->equipo [$red . '_version'] = $this->arregloAtributos [$indice] ['value'];
				} elseif ($this->arregloAtributos [$indice] ['tag'] == 'capacity') {
					$this->equipo [$red . '_velocidad'] = $this->arregloAtributos [$indice] ['value'];
				}
				$indice ++;
			} while ( $this->arregloAtributos [$indice] ['type'] == 'complete' );
		}
	}
	function datosVideo($clave, $valor) {
		// Datos Video
		$indice = $clave + 1;
		
		do {
			
			if ($this->arregloAtributos [$indice] ['tag'] == 'vendor') {
				$this->equipo ['video_fabricante'] = $this->arregloAtributos [$indice] ['value'];
			} elseif ($this->arregloAtributos [$indice] ['tag'] == 'description') {
				$this->equipo ['video_tipo'] = $this->arregloAtributos [$indice] ['value'];
			} elseif ($this->arregloAtributos [$indice] ['tag'] == 'product') {
				$this->equipo ['video_version'] = $this->arregloAtributos [$indice] ['value'];
			}
			$indice ++;
		} while ( $this->arregloAtributos [$indice] ['type'] == 'complete' );
	}
	function datosDisco($clave, $valor) {
		// Datos Disco
		$indice = $clave + 1;
		
		do {
			
			if ($this->arregloAtributos [$indice] ['tag'] == 'vendor') {
				$this->equipo ['disco_fabricante'] = $this->arregloAtributos [$indice] ['value'];
			} elseif ($this->arregloAtributos [$indice] ['tag'] == 'description') {
				$this->equipo ['disco_tipo'] = $this->arregloAtributos [$indice] ['value'];
			} elseif ($this->arregloAtributos [$indice] ['tag'] == 'product') {
				$this->equipo ['disco_version'] = $this->arregloAtributos [$indice] ['value'];
			} elseif ($this->arregloAtributos [$indice] ['tag'] == 'serial') {
				$this->equipo ['disco_version'] = $this->arregloAtributos [$indice] ['value'];
			} elseif ($this->arregloAtributos [$indice] ['tag'] == 'size') {
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

$resultado = $myParser->procesarArchivoTXT ();
$resultado = $myParser->procesarArchivoXML ();


