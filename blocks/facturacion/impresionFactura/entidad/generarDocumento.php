<?php

namespace facturacion\impresionFactura\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

$ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/plugin/html2pfd/";

require $ruta . "/plugin/html2pdf/html2pdf.class.php";
class GenerarDocumento {
	public $miConfigurador;
	public $miSql;
	public $conexion;
	public $contenidoPagina;
	public $rutaURL;
	public $esteRecursoDB;
	public $beneficiarios;
	public $rutaAbsoluta;
	public $rutaXML;
	public $estrutura;
	public $contenido;
	public function __construct($sql, $beneficiarios, $ruta_archivos) {
		date_default_timezone_set ( 'America/Bogota' );
		
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		
		// Conexion a Base de Datos
		$conexion = "interoperacion";
		$this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		// Datos Para envio de Correo
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarInformacionApi', 'gmail' );
		$this->datosConexion = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
		
		// Datos Rutas Directorios
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		$this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$bloque = $this->miConfigurador->getVariableConfiguracion ( 'esteBloque' );
		
		if (! isset ( $bloque ["grupo"] ) || $bloque ["grupo"] == "") {
			$this->rutaURL .= "/blocks/" . $bloque ["nombre"] . "/";
			$this->rutaAbsoluta .= "/blocks/" . $bloque ["nombre"] . "/";
		} else {
			$this->rutaURL .= "/blocks/" . $bloque ["grupo"] . "/" . $bloque ["nombre"] . "/";
			$this->rutaAbsoluta .= "/blocks/" . $bloque ["grupo"] . "/" . $bloque ["nombre"] . "/";
		}
		
		// Ruta XML para Creación PDF
		$this->rutaXML = $this->rutaAbsoluta . 'entidad/PlantillaXML/Facturacion25012017.xml';
		
		// Procedimiento
		
		$opciones = explode ( "&", $beneficiarios );
		
		if (end ( $opciones ) == 'correo') {
			$_REQUEST ['correo'] = true;
		}
		
		$this->beneficiarios = explode ( ";", $opciones [0] );
		
		$this->ruta_archivos = $ruta_archivos;
		
		foreach ( $this->beneficiarios as $key => $this->identificador_beneficiario ) {
			
			if ($this->validarBeneficiario ()) {
				
				/**
				 * Cargar Estructura XML
				 */
				
				$this->cargarEstructuraXML ();
				
				/**
				 * Parametrizacioón Posición
				 */
				
				$this->parametrizacionPosicion ();
				
				/**
				 * Parametrizacioón Posición
				 */
				
				$this->estruturaDocumento ();
				
				/**
				 * Parametrizacioón Posición
				 */
				
				$this->crearPDF ();
				
				if (isset ( $_REQUEST ['correo'] )) {
					
					$this->enviarNotificacion ();
				}
			}
		}
	}
	public function validarBeneficiario() {
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaInformacionFacturacion', $this->identificador_beneficiario );
		$this->InformacionFacturacion = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaValoresConceptos', $this->identificador_beneficiario );
		$this->Conceptos = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarBeneficiario', $this->identificador_beneficiario );
		$this->InformacionBeneficiario = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
		
		if ($this->InformacionBeneficiario && $this->Conceptos && $this->InformacionFacturacion) {
			return true;
		} else {
			return false;
		}
	}
	public function cargarEstructuraXML() {
		ini_set ( 'xdebug.var_display_max_depth', 5 );
		ini_set ( 'xdebug.var_display_max_children', 256 );
		ini_set ( 'xdebug.var_display_max_data', 1024 );
		
		$this->estruturaXML = simplexml_load_file ( $this->rutaXML );
		
		// var_dump($nodo->titulo->attributes());
		
		$estrutura = json_encode ( $this->estruturaXML );
		
		$this->estructura = json_decode ( $estrutura, true );
	}
	public function parametrizacionPosicion() {
		
		/**
		 * Configuracion Pagina Documento
		 *
		 * _____________Columna 1 Columna 2
		 * Seccion 1 | | |
		 * _____________________________________
		 * Seccion 2 | | |
		 * _____________________________________
		 * Seccion 3 | | |
		 * _____________________________________
		 * Seccion 4 | | |
		 * _____________________________________
		 *
		 * 1020px * 100%
		 */
		$this->contenido = "<table  style='width:100%; border: 0.1px; background-color: #fff' >";
		
		// Determina la utilización de colspan
		$this->determinacionTipoColumna ();
		
		$numero_secciones = count ( $this->estruturaXML );
		
		switch ($numero_secciones) {
			case 1 :
				$height = '1015px';
				break;
			
			case 2 :
				$height = '505px';
				break;
			
			case 3 :
				$height = '335px';
				break;
			
			case 4 :
				$height = '250px';
				break;
			
			default :
				echo "Error Numero Secciones";
				exit ();
				break;
		}
		
		foreach ( $this->estruturaXML as $key => $seccion ) {
			
			$this->contenido .= "<tr>";
			
			$numero_columnas = count ( $seccion->columna );
			
			switch ($numero_columnas) {
				case 1 :
					$width = '100%';
					break;
				
				case 2 :
					$width = '45%';
					break;
				
				default :
					echo "Error Numero columnas";
					exit ();
					break;
			}
			
			foreach ( $seccion as $key => $columna ) {
				
				if (isset ( $this->colspan ) && $width == '100%') {
					
					$this->contenido .= "<td colspan='2' style='width:" . $width . ";height:" . $height . ";border:none;font-size:100%'  nowrap >";
					
					// Permite generar el Contenido a unos Tipos de Parametros
					$this->caracterizacionContenido ( $columna );
					
					$this->contenido .= "</td>";
				} else {
					
					$this->contenido .= "<td style='width:" . $width . ";height:" . $height . ";border:none;'  nowrap >";
					$this->caracterizacionContenido ( $columna );
					
					$this->contenido .= "</td>";
				}
			}
			
			$this->contenido .= "</tr>";
		}
		
		$this->contenido .= "</table>";
	}
	public function caracterizacionContenido($objetoDatos) {
		foreach ( $objetoDatos as $key => $value ) {
			$this->atributos = $value->attributes ();
			$value = str_replace ( "%%", "<br>", $value );
			
			switch ($key) {
				case 'titulo' :
					
					$this->contenido .= "<div style='" . $this->atributos . "'><b>" . strtoupper ( $value ) . "</b></div>";
					break;
				
				case 'texto' :
					$this->contenido .= "<div style='" . $this->atributos . "'>" . $value . "</div>";
					break;
				
				case 'imagen' :
					$this->contenido .= "<div style='text-align:" . $this->atributos ['alineacionImagen'];
					$this->contenido .= "'><img src='" . $value . "' " . $this->atributos ['dimensionesImagen'] . "  ></div>";
					break;
				
				case 'variable' :
					// Ejecuta los procesos para obtener contenido de la variable
					$this->ejecutarContenidoVariable ( $value );
					break;
			}
			$this->contenido .= "<br>";
		}
	}
	public function ejecutarContenidoVariable($variable) {
		switch ($variable) {
			case 'FechaActual' :
				$this->contenido .= "<div style='" . $this->atributos . "'>" . date ( 'Y-m-d' ) . "</div>";
				break;
			
			case 'InformacionPago' :
				$this->contenido .= "<div style='" . $this->atributos . "'>INFORMACION DE PAGO</div>";
				break;
			
			case 'HistoricoConsumo' :
				$this->contenido .= "<div style='" . $this->atributos . "'>Gráfico Histórico</div>";
				break;
			
			case 'InformacionPagoResumido' :
				
				$this->contenido .= "<div style='" . $this->atributos . "'>";
				
				$this->contenido .= "<table style='border-collapse:collapse;border:1px;width:100%;' nowrap >
                            <tr>
                                <td colspan='2' style='vertical-align:middle;font-size:24px;height:30px;text-align:left;border:none;background-color:#bfbfbf;color:#fff'><b>Resumen</b></td>
                            </tr>
                            <tr>
                                <td style='font-size:16px;height:13px;text-align:left;border:none;width:50%;background-color:#bfbfbf;color:#fff'><b>Deuda Anterior </b></td>
                                <td style='font-size:16px;height:13px;text-align:right;border:none;width:50%;background-color:#bfbfbf;color:#fff'>0</td>
                            </tr>
                            <tr>
                                <td style='height:18px;font-size:16px;text-align:left;border:none;width:50%;background-color:#bfbfbf;color:#fff'><b>Cuota Mes </b></td>
                                <td style='height:18px;font-size:16px;text-align:right;border:none;width:50%;background-color:#bfbfbf;color:#fff'>$ " . number_format ( $this->InformacionFacturacion ['total_factura'], 2 ) . " </td>
                            </tr>
                      		<tr>
                                <td style='vertical-align:middle;font-size:20px;height:22px;text-align:left;border:none;width:50%;background-color:#009933;color:#fff'><b>Total a pagar</b></td>
                                <td style='vertical-align:middle;height:22px;font-size:20px;text-align:right;border:none;width:50%;background-color:#009933;color:#fff'>$ " . number_format ( $this->InformacionFacturacion ['total_factura'], 2 ) . "</td>
                            </tr>
                        </table>";
				
				$this->contenido .= "</div>";
				break;
			
			case 'Conceptos' :
				$this->contenido .= "<div style='" . $this->atributos . "'>";
				
				$table = "<table style='border-collapse:collapse;border:0.1px;width:100%;' >
                            <tr>
                                <td colspan='4' style='margin: 0 auto;font-size:16px;height:18px;text-align:left;border:0.1px;'><b>Descripción</b></td>
                            </tr>";
				
				/*$table .= "<tr>
                                  <td style='height:13px;text-align:center;border:none;width:5%;'><br><b>N°</b><br></td>
                                  <td style='height:13px;text-align:center;border:none;width:25%;'><br><b>Periodo Facturado</b><br></td>
                                  <td style='height:13px;text-align:center;border:none;width:50%;'><br><b>Concepto</b><br></td>
                                  <td style='height:13px;text-align:center;border:none;width:20%;'><br><b>Valor</b><br></td>
                               </tr>";*/
				$i = 1;
				foreach ( $this->Conceptos as $key => $value ) {
					$table .= "<tr>
                                  <td style='height:13px;text-align:center;border:none;width:5%;'><br>" . $i . ".<br></td>
                                  <td style='height:13px;text-align:center;border:none;width:25%;'><br>" . $value ['inicio_periodo'] . "  /  " . $value ['fin_periodo'] . "<br></td>
                                  <td style='height:13px;text-align:left;border:none;width:50%;'><br>" . $value ['concepto'] . "<br></td>
                                  <td style='height:13px;text-align:left;border:none;width:20%;'><br>$ " . number_format ( $value ['valor_concepto'], 2 ) . "<br></td>
                               </tr>";
					
					$i ++;
				}
				
				$table .= "</table>";
				
				$this->contenido .= $table;
				$this->contenido .= "</div>";
				
				break;
			
			case 'InformacionBeneficiario' :
				$this->contenido .= "<div style='" . $this->atributos . "'>";
				
				$table = "<table style='margin: 0 auto;border-collapse:collapse;border:1px;width:100%;' nowrap >
                            <tr>
                                <td style='font-size: 14px;height:20px;text-align:left;border:0.1px;background-color:#4766cc;border-top-left-radius: 4px; border-bottom-left-radius: 4px; color:#fff'><b>Fecha Oportuna de Pago</b></td>
                		        <td style='height:15px;text-align:left;border:0.1px;background-color:#d6f4f9;border-top-right-radius:4px;border-bottom-right-radius:4px;'><b></b></td>
                		    </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px><b>Factura </b> " . $this->InformacionFacturacion ['id_factura'] . " </td>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px'><b>" . wordwrap ( $this->InformacionBeneficiario ['nombre_beneficiario'], 35, "<br>\n" ) . "</b></td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px'>" . wordwrap ( $this->InformacionBeneficiario ['direccion_beneficiario'], 35, "<br>\n" ) . "</td>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px'><b>" . wordwrap ( $this->InformacionBeneficiario ['departamento'] . " - " . $this->InformacionBeneficiario ['municipio'], 35, "<br>\n" ) . "</b></td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 5px'><b>" . wordwrap ( $this->InformacionBeneficiario ['departamento'] . " - " . $this->InformacionBeneficiario ['municipio'], 35, "<br>\n" ) . "</b></td>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 5px'><b>Estrato " . $this->InformacionBeneficiario ['estrato'] . "</b></td>
                            </tr>
                        </table>";
				
				$this->contenido .= $table;
				$this->contenido .= "</div>";
				break;
			
			case 'InformacionFacturacion' :
				$this->contenido .= "<div style='" . $this->atributos . "'>";
				
				$this->contenido .= "<table style='border-collapse:collapse;border:1px;width:100%;' nowrap >
                            <tr>
                                <td colspan='2' style='font-size: 16px;height:20px;text-align:left;border:none;background-color:#ff1a75;border-top-left-radius: 4px; border-top-right-radius:4px;border-bottom-right-radius:4px;border-bottom-left-radius: 4px;color:#fff'><b>Estado de Cuenta</b><br></td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:none;width:50%;font-style:italic;'><b>En Mora </b></td>
                                <td style='height:13px;text-align:right;border:none;width:50%;'>0</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:none;width:50%;border-top-left-radius: 4px; border-bottom-left-radius: 4px;color:#444444;border-spacing: 3px'><b>Saldo Vencido </b></td>
                                <td style='height:13px;text-align:right;border:none;width:50%;border-top-right-radius:4px;border-bottom-right-radius:4px;color:#444444'>0</td>
                            </tr>
                          </table>";
				
				$this->contenido .= "<br><br><table style='border-collapse:collapse;border:1px;width:100%;' nowrap >
                            <tr>
                                <td colspan='2' style='font-size: 16px;height:20px;text-align:left;border:0.1px;background-color:#4766cc;border-top-left-radius: 4px; border-top-right-radius:4px;border-bottom-right-radius:4px;border-bottom-left-radius: 4px;color:#fff'><b>Cuota de Mes  </b>".$this->InformacionFacturacion ['id_ciclo']."<br></td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:none;width:50%;'><b>Factura</b></td>
                                <td style='height:13px;text-align:right;border:none;width:50%;'>" . $this->InformacionFacturacion ['id_factura'] . "</td>
                            </tr>
							<tr>
                                <td style='height:13px;text-align:left;border:none;width:50%;'><b>Valor</b></td>
                                <td style='height:13px;text-align:right;border:none;width:50%;'>$ " . number_format ( $this->InformacionFacturacion ['total_factura'], 2 ) . "</td>
                            </tr>
							<tr>
                                <td style='height:13px;text-align:left;border:none;width:50%;'><b>IVA</b></td>
                                <td style='height:13px;text-align:right;border:none;width:50%;'></td>
                            </tr>
                          </table>";
				
				/**
				 * $this->contenido .
				 *
				 * = "<table style='border-collapse:collapse;border:1px;width:100%;' nowrap >
				 * <tr>
				 * <td colspan='2' style='height:13px;text-align:center;border:0.1px;background-color:#97b5f4;'><b>INFORMACIÓN PAGO RESUMIDO</b><br></td>
				 * </tr>
				 * <tr>
				 * <td style='height:13px;text-align:left;border:0.1px;width:50%'><b>Fecha de Venta: </b></td>
				 * <td style='height:13px;text-align:right;border:0.1px;width:50%'>" . $this->InformacionFacturacion['fecha_venta'] . "</td>
				 * </tr>
				 * <tr>
				 * <td style='height:13px;text-align:left;border:0.1px;width:50%;'><b>Fecha Factura: </b></td>
				 * <td style='height:13px;text-align:right;border:0.1px;width:50%;'>" . $this->InformacionFacturacion['fecha_factura'] . "</td>
				 * </tr>
				 * <tr>
				 * <td style='height:13px;text-align:left;border:0.1px;width:50%;'><b>Periodo: </b></td>
				 * <td style='height:13px;text-align:right;border:0.1px;width:50%;'>" . $this->InformacionFacturacion['id_ciclo'] . "</td>
				 * </tr>
				 * <tr>
				 * <td style='height:13px;text-align:left;border:0.1px;width:50%;'><b>Contrato-Ref.Pago: </b></td>
				 * <td style='height:13px;text-align:right;border:0.1px;width:50%;'>" . $this->InformacionFacturacion['numero_contrato'] . "</td>
				 * </tr>
				 * <tr>
				 * <td style='height:13px;text-align:left;border:0.1px;width:50%;'><b>Fecha Pago Oportuno: </b></td>
				 * <td style='height:13px;text-align:right;border:0.1px;width:50%;'></td>
				 * </tr>
				 * </table>";
				 */
				$this->contenido .= "</div>";
				break;
			
			case 'CodigoBarras' :
				
				$this->contenido .= "<div style='text-align:" . $this->atributos ['alineacionCodigoBarras'];
				
				$fecha = str_replace ( '-', '', $this->InformacionFacturacion ['fecha_factura'] );
				
				$valorCodigo = $fecha . $this->InformacionFacturacion ['departamento'] . $this->InformacionFacturacion ['municipio'] . $this->InformacionFacturacion ['id_beneficiario'];
				
				$valorCodigo = ereg_replace ( "[a-zA-Z]", "", $valorCodigo );
				
				$this->contenido .= "'><barcode type='CODABAR' value='" . $valorCodigo . "' style='" . $this->atributos ['dimensionesCodigoBarras'] . "'></barcode></div>";
				break;
		}
	}
	public function determinacionTipoColumna() {
		foreach ( $this->estruturaXML as $key => $seccion ) {
			
			$numero_columnas = count ( $seccion );
			
			switch ($numero_columnas) {
				case 1 :
					$columna_1 = true;
					break;
				
				case 2 :
					$columna_2 = true;
					break;
				
				default :
					echo "Error Numero columnas";
					exit ();
					break;
			}
		}
		
		if (isset ( $columna_1 ) && isset ( $columna_2 )) {
			
			$this->colspan = true;
		}
	}
	// ----------------------------------------------------------------------
	public function crearPDF() {
		ob_start ();
		$html2pdf = new \HTML2PDF ( 'P', 'LETTER', 'es', true, 'UTF-8', array (
				1,
				1,
				1,
				1 
		) );
		$html2pdf->pdf->SetDisplayMode ( 'fullpage' );
		$html2pdf->WriteHTML ( $this->contenidoPagina );
		
		if (isset ( $_REQUEST ['documento_intantaneo'] )) {
			
			$html2pdf->Output ( 'Factura_actual.pdf', 'D' );
			
			echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
			
			exit ();
		} else {
			
			$this->archivo_adjunto = $this->ruta_archivos . "/Factura_" . $this->InformacionBeneficiario ['numero_identificacion'] . "_" . str_replace ( ' ', '_', $this->InformacionBeneficiario ['nombre_beneficiario'] ) . ".pdf";
			$html2pdf->Output ( $this->archivo_adjunto, 'F' );
		}
	}
	public function estruturaDocumento() {
		$contenidoPagina = "<style type=\"text/css\">
                           table {

                                    font-family:Helvetica, Arial, sans-serif; /* Nicer font */

                                    border-collapse:collapse; border-spacing: 3px;
                                }
                                td, th {
                                    border: 1px solid #CCC;
                                    height: 13px;
                                } /* Make cells a bit taller */

                                th {

                                    font-weight: bold; /* Make sure they're bold */
                                    text-align: center;
                                    font-size:10px;
                                }
                                td {

                                    text-align: left;
                                }

                            </style>";
		
		$contenidoPagina = "<page backtop='2mm' backbottom='2mm' backleft='2mm' backright='2mm'>";
		
		$contenidoPagina .= $this->contenido;
		
		$contenidoPagina .= "</page>";
		
		$this->contenidoPagina = $contenidoPagina;
	}
	public function enviarNotificacion() {
		
		/**
		 * This example shows settings to use when sending via Google's Gmail servers.
		 */
		
		// SMTP needs accurate times, and the PHP time zone MUST be set
		// This should be done in your php.ini, but this is how to do it if you don't have access to that
		require $this->ruta . '/plugin/PHPMailer/PHPMailerAutoload.php';
		
		// Create a new PHPMailer instance
		$mail = new \PHPMailer ();
		
		$mail->CharSet = 'UTF-8';
		
		// Tell PHPMailer to use SMTP
		$mail->isSMTP ();
		
		// Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;
		
		// Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		
		// Set the hostname of the mail server
		$mail->Host = 'smtp.gmail.com';
		// use
		// $mail->Host = gethostbyname('smtp.gmail.com');
		// if your network does not support SMTP over IPv6
		
		// Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$mail->Port = 587;
		
		// Set the encryption system to use - ssl (deprecated) or tls
		$mail->SMTPSecure = 'tls';
		
		// Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		
		// Username to use for SMTP authentication - use full email address for gmail
		$mail->Username = $this->datosConexion ['usuario'];
		
		// Password to use for SMTP authentication
		$mail->Password = $this->datosConexion ['password'];
		
		// Set who the message is to be sent from
		$mail->setFrom ( $this->datosConexion ['usuario'], 'Conexiones Digitales - Sistema OpenKyOS' );
		
		// Set an alternative reply-to address
		// $mail->addReplyTo ( 'replyto@example.com', 'First Last' );
		
		// Set who the message is to be sent to
		
		$this->designatariosCorreo = array (
				'0' => $this->InformacionFacturacion ['correo'],
				'1' => $this->InformacionFacturacion ['correo_institucional'] 
		);
		
		if (is_array ( $this->designatariosCorreo ) == true) {
			
			foreach ( $this->designatariosCorreo as $key => $value ) {
				
				if (! is_null ( $value ) && $value != '') {
					$mail->addAddress ( $value );
				}
			}
		}
		// Set the subject line
		$mail->Subject = 'Factura - Conexiones Digitales II';
		$body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
                        <html>
                        <head>
                          <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                          <title>Factura - Conexiones Digitales II</title>
                        </head>
                        <body>
                        <div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
                          <h1>Factura - Conexiones Digitales II</h1>
                          <p>Factura de pago para el periodo ' . $this->InformacionFacturacion ['id_ciclo'] . '.<br><br>Conexiones Digitales II<br>Notificación de Sistema OpenKyOS</p>
                          <div align="center">
                          </div>
                        </div>
                        </body>
                        </html>
        ';
		
		// Archivo Adjunto
		$mail->addAttachment ( $this->archivo_adjunto, 'Factura.pdf' );
		
		// Read an HTML message body from an external file, convert referenced images to embedded,
		// convert HTML into a basic plain-text alternative body
		$mail->msgHTML ( $body, dirname ( __FILE__ ) );
		
		// Replace the plain text body with one created manually
		// $mail->AltBody = 'Hemos recibido una solicitud de restauración de contraseña, si usted realizo la solicitud de clic sobre el siguiente link . Si usted no realizo dicha solicitud por favor omita este mensaje';
		
		// Attach an image file
		// $mail->addAttachment ( $this->ruta . '/plugin/PHPMailer/examples/images/phpmailer_mini.png' );
		
		// send the message, check for errors
		$mail->send ();
	}
}
$miDocumento = new GenerarDocumento ( $this->miSql, $this->proceso ['datos_adicionales'], $this->rutaAbsoluta_archivos );
