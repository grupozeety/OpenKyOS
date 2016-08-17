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
		
		$html='<html>';
		$html.='<head>';
		$html.='<meta charset="utf-8"/>';
		$html.='</head>';
		$html.= '<table style="font-family:arial; border-collapse: collapse; border: 1px solid black;width:900px; page-break-inside: avoid;">';
		$html.= '<tr>';
		$html.= '<td>';
		$html.= '<img src="img/politecnica2.png"';
		$html.= '</td>';
		$html.= '<td colspan="2" style="color:#4B5897;text-align:center"><h2>HOJA DE VIDA DE EQUIPO</h2>';
		$html.= '</td>';
		$html.= '<td style="text-align:center">';
		$html.= '<table style="font-family:arial;font-size:10px; border:solid 1px;margin: 0 auto;">';
		$html.= '<tr>';
		$html.= '<td>';
		$html.= 'Codigo: CPN-FO-CDII-53';
		$html.= '</td>';
		$html.= '</tr>';
		$html.= '<td>';
		$html.= 'Versión: 1.0';
		$html.= '</td>';
		$html.= '<tr>';
		$html.= '<td>';
		$html.= 'Fecha:'.date('d/m/Y');
		$html.= '</td>';
		$html.= '</tr>';
		$html.= '<tr>';
		$html.= '</tr>';
		$html.= '</table>';
		$html.= '</td>';
		$html.= '</tr>';
		
		
		foreach ( $plantilla as $clave => $valor ) {
			
			
			$html.= '<tr>';
			$html.= '<td colspan="4" bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">'.$clave.'</td>';
			$html.= '</tr>';
			$html.= '<tr>';
			$html.= '<td bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">Paŕametro</td>';
			$html.= '<td bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">Especificación</td>';
			$html.= '<td bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">Aprobado</td>';
			$html.= '<td bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">Observación</td>';
			$html.= '</tr>';
			
			
			foreach ( $valor as $campo => $etiqueta ) {
				
				$html.= "<tr>\n";
				$html.= "<td style='font-size:11px; border: 1px solid black'>\n";
				$html.= $campo;
				$html.= "</td>\n";
				$html.= "<td style='font-size:11px; border: 1px solid black;'>\n";
				$html.= $this->equipo [$etiqueta] . "\n";
				$html.= "</td>\n";
				$html.= "<td style='font-size:11px; border: 1px solid black;width:7%'>\n";
				$html.= "</td>\n";
				$html.= "<td style='font-size:11px; border: 1px solid black;width:30%'>\n";
				$html.= "</td>\n";
				$html.= "</tr>\n";
			}
			
			
		}
		$html.= '<tr>';
		$html.= '<td colspan="4" bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">Registro Fotográfico</td>';
		$html.= '</tr>';
		$html.= "<tr>\n";
		$html.= "<td colspan=2 style='font-size:11px; border: 1px solid black;width:7%'>\n";
		$html.= '<img src="img/1.png">';
		$html.= "</td>\n";
		$html.= "<td colspan=2 style='font-size:11px; border: 1px solid black;width:30%'>\n";
		$html.= '<img src="img/2.png">';
		$html.= "</td>\n";
		$html.= "</tr>\n";
		$html.= '<tr>';
		$html.= '<td colspan="4" bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">Observaciones</td>';
		$html.= '</tr>';
		$html.= "<tr>\n";
		$html.= "<td colspan=4 style='font-size:11px; border: 1px solid black;width:10%;height:100px'>\n";
		$html.= "</td>\n";
		$html.= "</tr>\n";
				
		//Revisión
		$html.= '<tr>';
		$html.= '<td colspan="4" bgcolor="#62B2FF" style="padding:3px;vertical-align: middle;font-size:13px; text-align:center; font-weight: bold; border: 1px solid black">Revisión</td>';
		$html.= '</tr>';
		$html.= "<tr>\n";
		$html.= "<td colspan=2 style='text-align:center;font-size:11px; border: 1px solid black'>\n";
		$html.= "Inspeccionado Por</td>\n";
		$html.= "<td colspan=2 style='text-align:center;font-size:11px; border: 1px solid black'>\n";
		$html.= "Supervisado Por</td>\n";		
		$html.= "</tr>\n";
		$html.= "<tr>\n";
		$html.= "<td colspan=2 style='font-size:11px; border: 1px solid black'>\n";
		$html.= "<br><br>Nombre:<br><br>No Documento:</td>\n";
		$html.= "<td colspan=2 style='font-size:11px; border: 1px solid black'>\n";
		$html.= "<br><br>Nombre:<br><br>No Documento:</td>\n";
		$html.= "</tr>\n";
		$html.= "<tr>\n";
		$html.= "<td colspan=2 style='vertical-align: top;font-size:11px; border: 1px solid black;height:100px'>\n";
		$html.= "Firma</td>\n";
		$html.= "<td colspan=2 style='vertical-align: top;font-size:11px; border: 1px solid black;height:100px'>\n";
		$html.= "Firma</td>\n";
		$html.= "</tr>\n";
		$html.= "</table>\n";
		$html.= "<br><hr>";
		$html.='</html>';
		
		$myfile = fopen("equiposhtml2/".$this->equipo ['serial'] .".html", "w") or die("Unable to open file!");
		fwrite($myfile, $html);
		fclose($myfile);
		
		
		//echo $html;
		//exit();
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

$faltante[1]=array('55CG6122V99','02254 2/01/2016','Z5CDWYTLT','70:5a:of:84:3a:6d','2c:6e:85:40:03:12','102173C1');
$faltante[2]=array('5CG6122RJ2','17003 2/27/2016','Z5CCWPQT','70:5a:of:81:cc:3b.','2c.6e:85:42:31:64','045D3844');
$faltante[3]=array('5CG6122RJY','02713 2/01/2016','Z55HCGIRT','70:5a:of:81:8a:81','34:de:1a:90:3b:52','215917');
$faltante[4]=array('5CG6122RQ8','02600 2/01/2016','Z55DWYLAT','70:5a:0f:81:6a:aa','2c:6e:85:3f:fa:84','14858669');
$faltante[5]=array('5CG6122S7D','01915 2/01/2016','Z5CCCP96T','70:5a:0f:81:4d:3c','2c:6e:85:40:02:86','10217347');
$faltante[6]=array('5CG6122SD9','05951 1/21/2016','Z5CJWUG0T','70:5a:0f:81:c9:6a','00:1e:64:fe:f5:33','0E2136F7');
$faltante[7]=array('5CG6122SFR','01710 1/20/2016','Z55GC9P0T','70:5a:0f:81:c9:6b','00:1e:64:fe:f6:0a','0E10FF2A');
$faltante[8]=array('5CG6122SJD','02156 2/01/2016','Z5CLC0SJT','70:5a:0f:81:3b:e0','18:3d:ad:e2:15:df','0929BFB2');
$faltante[9]=array('5CG6122SNB','11209 1/27/2016','Z5CLC0TMT','70:5a:0f:81:b9:5a','18:3d:a2:e2:16:0c','0919BFB0');
$faltante[10]=array('5CG6122SXD','01630 2/01/2016','Z55LCNMWT','70:5a:0f:81:d9:e1','2c:6e:85:3f:fb:f6','14858667');
$faltante[11]=array('5CG6122T5K','33154 2/01/2016','Z5CLC0UYT','70:5a:0f:81:7a:c5','18:3d:a2:e2:2c:be','123164DB');
$faltante[12]=array('5CG6122TDY','33154 2/01/2016','Z5CZCNT1T','70:5a:0f:81:99:3d','2c:6e:85:40:01:78','0F5184AE');
$faltante[13]=array('5CG6122TS6','02015 2/01/2016','Z5CHCHN9T','70:5a:0f:81:bc:15','34:de:1a:90:3d:d7','0420013F');
$faltante[14]=array('5CG6122TZP','01676 2/01/2016','Z5CDWYSLT','70:5a:0f:81:0a:1e','2c:6e:85:40:02:68','0F5184B0');
$faltante[15]=array('5CG6122VSM','05992 1/21/2016','Z55PCZN0T','70:5a:0f:81:5a.34','18:3d:a2:e2:24:b7','126164D2');
$faltante[16]=array('5CG6122W0C','01878 2/01/2016','Z55FCHD6T','70;5a:081:9c:8f','2c:6e:85:41:9b:6e','1221640000000');
$faltante[17]=array('5CG6122W1C','06261 3/04/2016','Z5CDWYSDT','70:5a:0f:81:2d:c0','2c:6e:85:40:02:90','0F21846F');
$faltante[18]=array('5CG6122W77','06433 1/21/2016','Z55DWYKXT','70:5a:0f:81:19:5f','18;:3d:a2:e2:26:b0','0630E8B2');
$faltante[19]=array('5CG6122WWL','01814 2/01/2016','Z55DWYK0T','70:5a:0f:81:5a:ea','2c:6e:85:40:05:2e','1485866B');
$faltante[20]=array('5CG6122WWR','05960 1/21/2016','Z5CICPTRT','70:5a:0f:81:2a:40','2c:6e:85:40:21:da','0F718510');
$faltante[21]=array('5CG6122WZR','01204 1/20/2016','Z55GC9PYT','70:5a:0f:81:d9:4c','00:1e:64:fe:f6:05','0E70FF29');
$faltante[22]=array('5CG6122X9W','01585 2/01/2016','Z55GC9R5T','70:5a:0f:81:7c:4f','18:3d:a2:e2:14:8b','0057170B');
$faltante[23]=array('5CG6122XB9','01860 2/01/2016','Z55PCZQAT','70:5a:0f:81:ca:64','18:3d:a2:e2:16:b1','9719203');
$faltante[24]=array('5CG6122XJ1','01515 1/20/2016','Z55CCP7FT','705a0f:81:d9.83','18:3d:a2:e2:27:00','1BAF7AFE');
$faltante[25]=array('5CG6122YGZ','12085 2/03/2016','Z5CZCNUST','70:5a:0f:81:4c:24','2c:6e:85:42:a2:ed','041C604A');
$faltante[26]=array('5CG6122YNS','30745 2/01/2016','Z5CLC0TTT','70:5a:0f:81:9c:dc','2c:6e:85:41:9b:73','124164FD');
$faltante[27]=array('5CG6122YOM','07699 2/27/2016','Z55PCZNXT','70:5a:0f:81:0a:63','18:3d:.a2:e2:25:70','0919BF32');
$faltante[28]=array('5CG6122YP5','03414 3/08/2016','Z5CDWYS8T','70:5a:0f:81:4d:8b','2c:6e:85:40:02:a4','0F7184B8');
$faltante[29]=array('5CG6122Z21','15163 1/28/2016','Z5CCCPGT','70:5a:0f:84:1a:c7','2c:6e:85:3f:f1:1a','0951946D');
$faltante[30]=array('5CG6122ZN2','0912 2/01/2016','Z55FCHCRT','70:5a:0f:81:4c:31','2c:6e:85:41:85:ed','128164FC');
$faltante[31]=array('5CG6122ZNB','30453 1/31/2016','Z5CDWYSQT','70:5a:0f:81:4d:9a','2c:6e:85:40:03:0d','0F1184F9');
$faltante[32]=array('5CG6122ZVC','05498 1/21/2016','Z5CJWUNJT','70:5a:0f:81:9a:49','18:3d:a2:e2:28:c7','123164F1');
$faltante[33]=array('5CG6122ZW1','02063 2/01/2016','Z5CCCP9KT','70:5a:0f:81:c5:2b','2c:6e:85:3f:f3:0e:','9119406');
$faltante[34]=array('5CG612300P','06149 1721/2016','Z5CLC0UBT','70:5a:0f:81:4a:e5','18:3d:a2:e2:26:2e','127164DA');
$faltante[35]=array('5CG61230V4','01954 2/01/2016','Z55ZCNE8T','70:5a:0f:81:c9:ea','2c.6e:85:32:0d:d4','09712F79');
$faltante[36]=array('5CG61230VT','01716 2/01/2016','Z55GC9R2T','70:5a:of:81:0b:5f','18:3d:a2:e2:13:5f','006171B');
$faltante[37]=array('5CG61230ZD','02777 2/01/2016','Z553CJB8T','70:5a:0f:81:5a:0f','2c:6e:85:33:0c:89','14358666');
$faltante[38]=array('5CG6123113','00862 1/20/2016','Z553CJBDT','70:5a:0f.81:3a:0e','2c:6e:85:33:1c:29','14758665');
$faltante[39]=array('5CG6123141','06347 1/21/2016','Z5CJWUNAT','70:5a:0f:84:2a:80','18:3d:a2:e2:15:da','0919BFAA');
$faltante[40]=array('5CG612316X','10899 2/27/2016','Z5CZCNTJT','70:5a:0f:81:3c:d9','2c:6e:85:42:a3:33','047C4560');
$faltante[41]=array('5CG61231F9','08974 2/17/2016','Z553CJC7T','70:5a:0f:81:ba:21','34:de:1a:90:3b:89','008158FC');
$faltante[42]=array('5CG61231GL','10639 1/22/2016','Z5CDWYSCT','70:5a:0f:81:89:de','2c:6e:85:40:03:8f','0F11849B');
$faltante[43]=array('5CG61231R1','02662 2/01/2016','Z55ZCNDTT','70:5a:0f:81:3a:d7','2c:6e:85:32:0d:e8','134CED8F');
$faltante[44]=array('5CG61231T5','00041 2/01/2016','Z5CCWPK7T','70:5a:0f:81:2d:b1','2c:6e:85:3f:fd:9f','043D3882');
$faltante[45]=array('5CG61231VF','05986 1/21/2016','Z5CJWUHHT','70:5a:0f:81:1d:50','2c:6e:85:41:86:38','127164B6');
$faltante[46]=array('5CG61231XQ','04120 3/08/2016','Z55DWYL5T','70:5a:0f:81:9c:0b','2c:6e::85:3f:fb:83','1475867B');
$faltante[47]=array('5CG61231ZR','01678 1/20/2016','Z55GC9RUT','70:5a:0f:81:1a:2b','70:5a:0f:81:1a:2b','571701');
$faltante[48]=array('5CG612322K','07707 2/27/2016','Z55CCP6ZT','7a:5a:0f:81:b9:ab','18:3d:a2:e2:26:8d','0680E8DD');
$faltante[49]=array('5CG612324L','06332 3/04/2016','Z5CCCPA3T','70:5a.0f:84:3a:6e','2c:6e:85:3f:f2:d7','0921945E');
$faltante[50]=array('5CG612327R','06102 1/21/2016','5GC9PHT','70:5a:0f:81:b9:9e','18:3d:a2:e2:25:9d','0949BF30');
$faltante[51]=array('5CG61232WY','06221 1/21/2016','Z5CCCPA1T','70:5a:0f:84:2a:c9','2c:6e:85:3f:f1:ec','096194A3');
$faltante[52]=array('5CG612339S','06522 1721/2016','Z5CHCNIT','70:5a:0f:81:fc:a9','70:5a:0f:81:fc:a9','04A001EF');
$faltante[53]=array('5CG61233SX','01822 2/01/2016','Z55PCZQ5T','70;5a:0f:81:59:e5','2c:6e:85:33:1b:6b','09612F4F');
$faltante[54]=array('5CG6123BJ2','02208 2/01/2016','Z5CLCOTT1T','70:5a:0f:8e:71:59','18:3d:a2:e2:15:f3','0929BFA4');
$faltante[55]=array('5CG6123BJH','10699 1/22/2016','Z55PCZNMT','70:5a:0f:81:aa:df','00:1e:64:fe:f6:0f','0E50FF25');
$faltante[56]=array('5CG6130P6L','0','0','0','0','0');
$faltante[57]=array('5CG613139Q','1','1','1','1','1');
$faltante[58]=array('5CG6131154','2','2','2','2','2');
$faltante[59]=array('5CG6123BNH','3','3','3','3','3');
$faltante[60]=array('5CG612258B','4','4','4','4','4');
$faltante[61]=array('5CG6130WLJ','5','5','5','5','5');
$faltante[62]=array('5CG6123BKX','6','6','6','6','6');
$faltante[63]=array('5CG6131157','7','7','7','7','7');
$faltante[64]=array('5CG6122YJD','8','8','8','8','8');
$faltante[65]=array('5CG6122W96','9','9','9','9','9');
$faltante[66]=array('5CG61310B9','10','10','10','10','10');
$faltante[67]=array('5CG6122T61','11','11','11','11','11');
$faltante[68]=array('5CG61311NP','12','12','12','12','12');
$faltante[69]=array('5CG6130FGQ','13','13','13','13','13');
$faltante[70]=array('5CG61312W0','14','14','14','14','14');
$faltante[71]=array('5CG61313H1','15','15','15','15','15');
$faltante[72]=array('5CG6130ZQ2','16','16','16','16','16');
$faltante[73]=array('5CG61311SW','17','17','17','17','17');
$faltante[74]=array('5CG6123BNM','18','18','18','18','18');
$faltante[75]=array('5CG613118V','19','19','19','19','19');
$faltante[76]=array('5CG6122R9H','20','20','20','20','20');
$faltante[77]=array('5CG61312P6','21','21','21','21','21');
$faltante[78]=array('5CG6130XGH','22','22','22','22','22');
$faltante[79]=array('5CG6130XCF','23','23','23','23','23');
$faltante[80]=array('5CG61312SF','24','24','24','24','24');
$faltante[81]=array('5CG612341P','25','25','25','25','25');
$faltante[82]=array('5CG6122VT0','26','26','26','26','26');
$faltante[83]=array('5CG6122VXS','27','27','27','27','27');
$faltante[84]=array('5CG6130L2W','28','28','28','28','28');
$faltante[85]=array('5CG6130XFB','29','29','29','29','29');
$faltante[86]=array('5CG61233DQ','30','30','30','30','30');
$faltante[87]=array('5CG61235RV','31','31','31','31','31');
$faltante[88]=array('5CG6130VXN','32','32','32','32','32');
$faltante[89]=array('5CG612R9D','33','33','33','33','33');


$myParser = new CargadorDatos ( $this->lenguaje, $this->sql );

$this->archivoXML = '0062.xml';
$this->archivoTXT = 'b.txt';

for($i = 1; $i < 90; $i ++) {
	
	/*if ($i < 100) {
		$directorio = '00' . $i;
	} elseif ($i < 1000) {
		$directorio = '0' . $i;
	} else {
		$directorio = $i;
	}
	if (file_exists ( 'equipos/' . $directorio )) {*/
		
	
		if($i<56){
		$myParser->equipo ['id_interno_bodega'] = $i;
		}else{
			$myParser->equipo ['id_interno_bodega'] = 'Garantía';
		}
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
		$myParser->equipo ['distribuidor_direccion'] = 'Calle 18a # 6-17 Neiva-Huila';
		$myParser->equipo ['distribuidor_telefono'] = '8812382';
		
		
		
		// echo "Encontré los datos de :".$directorio.'<br>';
		//$myParser->archivoXML = 'equipos/' . $directorio . '/a.xml';
		//$myParser->archivoTXT = 'equipos/' . $directorio . '/b.txt';
		
		$myParser->archivoXML = $this->archivoXML;
		$myParser->archivoTXT = $this->archivoTXT;
		
		if (file_exists ( $myParser->archivoTXT )) {
			$resultado = $myParser->procesarArchivoTXT ();
			$resultado = $myParser->procesarArchivoXML ();
			
			$myParser->equipo ['serial'] = $faltante[$i][0];
			$myParser->equipo ['bateria_serial'] = $faltante[$i][1];
			$myParser->equipo ['disco_serial'] = $faltante[$i][2];
			$myParser->equipo ['red_serial'] = $faltante[$i][3];
			$myParser->equipo ['wifi_serial'] = $faltante[$i][4];
			$myParser->equipo ['memoria_serial'] = $faltante[$i][5];
			
			
			$resultado = $myParser->armarHoja ( $this->ruta );
			
			// $resultado = $myParser->cargarEnLote();
		/*} else {
			echo $i . '<br>';
		*/}
	/*} else {
		
		// echo $directorio.'<br>';
	}*/
}exit;




