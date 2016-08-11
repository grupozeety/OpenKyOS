<?php

namespace complementos\registrarPortatil\funcion;

class CargadorDatos {
	var $miConfigurador;
	var $equipo = array (
			'id_distribuidor' => '0',
			'bits' => '64',
			'marca' => 'Hewlett Packard',
			'estado_registro' => 'activo',
			'estado_equipo' => 'excelente',
			'tipo_equipo' => 'Computador portatil',
			'ancho' => '34.54',
			'largo' => '24.15',
			'alto' => '2.29',
			'peso' => '1.9',
			'pantalla_tipo' => 'HD SVA anti-brillo retroiluminada LED',
			'pantalla_tamanno' => '14" diagonal ',
			'pantalla_resolucion' => '1366 x 768',
			'teclado_tipo' => 'Tamaño completo con textura estilo isla negro',
			'teclado_idioma' => 'Español',
			'mouse_tipo' => 'Touchpad con capacidad multi-touch',
			'mouse_fabricante' => 'PixArt',
			'camara_tipo' => 'Integrada',
			'camara_version' => 'HP Truevision HD',
			'audio_tipo' => 'Mono/Estereo',
			'audio_version' => 'DTS Sound+',
			'audio_fabricante' => 'Advanced Micro Devices, Inc. [AMD/ATI]',
			'audio_conector' => 'headphone/microphone combo jack',
			'parlantes_tipo' => 'Integrado',
			'parlantes_version' => 'Dual speakers',
			'red_estandar' => 'Ethernet',
			'red_tipo_conector' => 'RJ-45',
			'red_ipv4' => '',
			'red_ipv6' => '',
			'wifi_estandar' => '802.11b/g/n',
			'wifi_codificacion' => 'WPA-PSK + WPA2-PSK',
			'bluetooth_velocidad' => '12Mbit/s',
			'bluetooth_version' => '4.0',
			'puerto_usb2_total' => '2',
			'puerto_usb3_total' => '3',
			'puerto_hdmi_total' => '1',
			'puerto_vga_total' => '1',
			'slot_expansion_tipo' => 'multi-format digital media reader(soporta SD, SDHC, SDXC)',
			'alimentacion_tipo' => 'AC',
			'alimentacion_dispositivo' => 'Adaptador Smart AC',
			'alimentacion_voltaje' => '100 v a 120 v',
			'alimentacion_frecuencia' => '50 Hz a 60 Hz',
			'bateria_certificacion' => 'FCC - ENERGY STAR - UL' 
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
	}
	
	/**
	 * Cargar los datos del equipo que se encuentran en el archivo de texto
	 * generado por lshw
	 */
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
		
		// var_dump ( $this->equipo );
		// var_dump ( $this->arregloAtributos );
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
				$this->equipo ['disco_serial'] = $this->arregloAtributos [$indice] ['value'];
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
				$this->equipo ['cpu_bits'] = $this->arregloAtributos [$indice] ['value'];
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
		
		
		if (file_exists($archivo) && filesize ( $archivo ) > 0) {
			$this->procesado = file_get_contents ( $archivo );
			return true;
		} else {
			return false;
		}
	}
	function getListadoAtributos() {
		// 1. Rescatar los nombres de las columnas
		$cadenaSql = $this->sql->getCadenaSql ( 'nombresColumnas' );
		
		$arreglo = $this->conexion->ejecutarAcceso ( $cadenaSql, "acceso" );
	}
	
	/**
	 * Cargar los datos del equipo que se encuentran en el archivo de texto
	 * generado por dmidecode
	 */
	function procesarArchivoTXT() {
		$abrir = $this->leerArchivo ( $this->archivoTXT );
		
		if ($abrir) {
			
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
			
			//echo $this->equipo ['id_interno_bodega'] . ':' . $this->equipo ['serial'] . '<br>';
			//echo $this->equipo ['serial'] . '<br>';
			
		} else {
			//echo $this->equipo ['id_interno_bodega'] . ':ERROR<br>';
		}
	}
	function armarHoja($ruta) {
		include ($ruta . '/formulario/portatil.template.php');
		
		echo '<table style="font-family:arial; border-collapse: collapse; border: 1px solid black;width:900px">';
		echo '<tr>';
		echo '<td>';
		echo '<img src="img/politecnica2.png"';
		echo '</td>';
		echo '<td colspan="2" style="color:#4B5897;text-align:center"><h2>HOJA DE VIDA DE EQUIPO</h2>';
		echo '</td>';
		echo '<td>';
		echo '<table style="font-family:arial;font-size:10px; border:solid 1px">';
		echo '<tr>';
		echo '<td>';
		echo 'Codigo: CPN-FO-CDII-53';
		echo '</td>';
		echo '</tr>';
		echo '<td>';
		echo 'Versión: 1.0';
		echo '</td>';
		echo '<tr>';
		echo '<td>';
		echo 'Fecha:'.date('d/m/Y');
		echo '</td>';
		echo '</tr>';
		echo '<tr>';
		echo '</tr>';
		echo '</table>';
		echo '</td>';
		echo '</tr>';
		
		
		foreach ( $plantilla as $clave => $valor ) {
			
			
			echo '<tr>';
			echo '<td colspan="4" bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">'.$clave.'</td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">Paŕametro</td>';
			echo '<td bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">Especificación</td>';
			echo '<td bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">Aprobado</td>';
			echo '<td bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">Observación</td>';
			echo '</tr>';
			
			
			foreach ( $valor as $campo => $etiqueta ) {
				
				echo "<tr>\n";
				echo "<td style='font-size:11px; border: 1px solid black'>\n";
				echo $campo;
				echo "</td>\n";
				echo "<td style='font-size:11px; border: 1px solid black;'>\n";
				echo $this->equipo [$etiqueta] . "\n";
				echo "</td>\n";
				echo "<td style='font-size:11px; border: 1px solid black;width:10%'>\n";
				echo "</td>\n";
				echo "<td style='font-size:11px; border: 1px solid black;width:30%'>\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
			
			
		}
		echo '<tr>';
		echo '<td colspan="4" bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">Registro Fotográfico</td>';
		echo '</tr>';
		echo "<tr>\n";
		echo "<td colspan=2 style='font-size:11px; border: 1px solid black;width:10%'>\n";
		echo '<img src="img/1.png">';
		echo "</td>\n";
		echo "<td colspan=2 style='font-size:11px; border: 1px solid black;width:30%'>\n";
		echo '<img src="img/2.png">';
		echo "</td>\n";
		echo "</tr>\n";
		echo '<tr>';
		echo '<td colspan="4" bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">Observaciones</td>';
		echo '</tr>';
		echo "<tr>\n";
		echo "<td colspan=4 style='font-size:11px; border: 1px solid black;width:10%;height:100px'>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo '<tr>';
		echo '<td colspan="4" bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">Revisión</td>';
		echo '</tr>';
		echo "<tr>\n";
		echo "<td colspan=2 style='font-size:11px; border: 1px solid black;width:10%;height:100px'>\n";
		echo '<table>';
		echo '<tr>';
		echo '</tr>';
		echo "</td>\n";
		echo "<td colspan=2 style='font-size:11px; border: 1px solid black;width:10%;height:100px'>\n";
		echo "</td>\n";		
		echo "</tr>\n";
		
		
		
		
		echo "</table>\n";
		
		echo "<br><hr>";
		exit();
	}
	function cargarEnLote() {
		$cadenaCampos = '';
		$cadenaValores = '';
		foreach ( $this->equipo as $clave => $valor ) {
			
			$cadenaCampos .= $clave . ",";
			$cadenaValores .= "'" . $valor . "',";
		}
		$cadenaCampos = substr ( $cadenaCampos, 0, strlen ( $cadenaCampos ) - 1 );
		$cadenaValores = substr ( $cadenaValores, 0, strlen ( $cadenaValores ) - 1 );
		$cadenasql = 'INSERT INTO politecnica_portatil (' . $cadenaCampos . ') VALUES(' . $cadenaValores . ')';
		
		echo $cadenasql;
	}
}

$myParser = new CargadorDatos ( $this->lenguaje, $this->sql );

$this->archivoXML = '0062.xml';
$this->archivoTXT = 'b.txt';

for($i = 1; $i < 2000; $i ++) {
	
	if ($i < 100) {
		$directorio = '00' . $i;
	} elseif ($i < 1000) {
		$directorio = '0' . $i;
	} else {
		$directorio = $i;
	}
	if (file_exists ( 'equipos/' . $directorio )) {
		
		$myParser->equipo ['id_interno_bodega'] = $i;
		$myParser->equipo ['fecha_compra'] = 'N/D';
		$myParser->equipo ['fecha_instalacion'] = 'N/D';
		$myParser->equipo ['dimensiones'] = '34.54cm x 24.15 x 2.29';
		$myParser->equipo ['ubicacion'] = 'Bodega Sincelejo';
		$myParser->equipo ['responsable'] = 'Mauricio Cáceres';
		$myParser->equipo ['telefonoContacto'] = '3208549499';
		$myParser->equipo ['arquitectura'] = '64 bits';
		$myParser->equipo ['disco_cache'] = '8 Mb';
		$myParser->equipo ['disco_velocidad'] = '5.400 rpm';
		$myParser->equipo ['disco_proteccion'] = 'Active hard-drive protection';
		$myParser->equipo ['video_memoria'] = '256 MB Compartida';
		$myParser->equipo ['camara_formato'] = '720 px HD';
		$myParser->equipo ['camara_funcionalidad'] = 'Grabación, Video y Fotografía';
		$myParser->equipo ['microfono_tipo'] = 'Integrado direccional';
		$myParser->equipo ['manuales'] = 'Usuario, Sistema Operativo en español';
		$myParser->equipo ['sistema_operativo'] = 'Ubuntu';
		$myParser->equipo ['suite_ofimatica'] = 'OpenOffice';
		$myParser->equipo ['antivirus'] = 'Clamav Antivirus';
		$myParser->equipo ['distribuidor_nombre'] = 'TecnoMusic';
		$myParser->equipo ['distribuidor_direccion'] = 'Calle 34 #4-56';
		$myParser->equipo ['distribuidor_telefono'] = '8812382';
		
		
		// echo "Encontré los datos de :".$directorio.'<br>';
		$myParser->archivoXML = 'equipos/' . $directorio . '/a.xml';
		$myParser->archivoTXT = 'equipos/' . $directorio . '/b.txt';
		
		if (file_exists ( $myParser->archivoTXT )) {
			$resultado = $myParser->procesarArchivoTXT ();
			$resultado = $myParser->procesarArchivoXML ();
			
			$resultado = $myParser->armarHoja ( $this->ruta );
			
			// $resultado = $myParser->cargarEnLote();
		} else {
			echo $i . '<br>';
		}
	} else {
		
		// echo $directorio.'<br>';
	}
}exit;




