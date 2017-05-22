<?php

namespace facturacion\pagoFactura\entidad;

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
	public $esteRecursoDB;
	public $rutaAbsoluta;
	public $contenido;
	public function __construct($lenguaje, $sql) {
		date_default_timezone_set ( 'America/Bogota' );
		setlocale ( LC_ALL, "es_ES.UTF8" );
		
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		
		// Conexion a Base de Datos
		$conexion = "interoperacion";
		$this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		// Datos Rutas Directorios
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		$this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$this->rutaProceso = $this->rutaAbsoluta . "/archivos/comprobantesPago/";
		
		$bloque = $this->miConfigurador->getVariableConfiguracion ( 'esteBloque' );
		
		if (! isset ( $bloque ["grupo"] ) || $bloque ["grupo"] == "") {
			$this->rutaURL .= "/blocks/" . $bloque ["nombre"] . "/";
			$this->rutaAbsoluta .= "/blocks/" . $bloque ["nombre"] . "/";
		} else {
			$this->rutaURL .= "/blocks/" . $bloque ["grupo"] . "/" . $bloque ["nombre"] . "/";
			$this->rutaAbsoluta .= "/blocks/" . $bloque ["grupo"] . "/" . $bloque ["nombre"] . "/";
		}
	}
	public function comprobante() {
		$this->contenidoDocumento ();
		
		$this->estructuraDocumento ();
		
		$this->crearPDF ();
	
	/**
	 * Registrar generación comprobante
	 */
		// $cadenaSql = $this->miSql->getCadenaSql ( 'actualizarFacturaBeneficiario', $this->identificador_beneficiario );
		// $actualizacionEstadoFactura = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
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
	public function contenidoDocumento() {
		$this->atributos = '';
		$this->contenido = '';
		$this->contenido .= "<div style='text-aling:center'><b>Corporación Politécnica Nacional de Colombia</b></div>";
		$this->contenido .= "<div style='text-aling:center'><b>NIT. 830.115.993-4</b></div>";
		$this->contenido .= "<div style='" . $this->atributos . "'><b>Comprobante de Pago</b></div><br>";
		$this->contenido .= "<div style='" . $this->atributos . "'><b>No. Pago:</b> " . $_REQUEST ['idPago'] . "</div>";
		$this->contenido .= "<div style='" . $this->atributos . "'><b>Fecha del Pago:  </b>" . strftime ( " %a %d %B %Y  %X" ) . "</div>";
		$this->contenido .= "<div style='" . $this->atributos . "'><b>Cliente:  </b>" . $_REQUEST ['beneficiario'] . "</div>";
		$this->contenido .= "<div style='" . $this->atributos . "'><b>Cajero:  </b>" . $_REQUEST ['usuarioN'] . "</div>";
		
		$this->contenido .= "<div style='" . $this->atributos . "'><br><b>INFORMACION DE PAGO</b></div>";
		
		$this->contenido .= "<div style='" . $this->atributos . "'>";
		
		$table = "<br><table style='margin: 0 auto;border-collapse:collapse;border:1px;width:100%;' nowrap >
				  			<tr>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px><b>Concepto </b></td>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Valor</b></td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px>Factura No. " . $_REQUEST ['id_factura'] . "</td>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$ " . number_format ( $_REQUEST ['valor_factura'], 2, '.', ',' ) . "</td>
                            </tr>";
		
		if ($_REQUEST ['valor_abono'] > 0) {
			$table .= "     <tr>
		
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px>Abono Adicional</td>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$ " . number_format ( $_REQUEST ['valor_abono'], 2, '.', ',' ) . "</td>
                            </tr>";
		}
		$table .= "         <tr>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px></td>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px'></td>
                            </tr>
                     
                            <tr>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px'><i>Valor Recibido</i></td>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$ " . number_format ( $_REQUEST ['valor_recibido'], 2, '.', ',' ) . "</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 5px'><i>Cambio</i></td>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 3px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$ " . number_format ( $_REQUEST ['valor_devuelto'], 2, '.', ',' ) . "</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 5px'><i>Medio de Pago</i></td>
                                <td style='height:13px;text-align:left;border:none;border-spacing: 5px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $_REQUEST ['medioTexto'] . "</td>
                            </tr>
                                		
                        </table>";
		
		$this->contenido .= $table;
		$this->contenido .= "</div>";
		
		$this->contenido .= "<br><div style='" . $this->atributos . "'><b>Gracias por su pago.</b></div>";
	}
	
	// ----------------------------------------------------------------------
	public function crearPDF() {
		ob_start ();
		$html2pdf = new \HTML2PDF ( 'P', 'LETTER', 'es', true, 'UTF-8' );
		$html2pdf->pdf->SetDisplayMode ( 'fullpage' );
		$html2pdf->WriteHTML ( $this->contenidoPagina );
		
		$html2pdf->Output ( 'comprobantePago.pdf', 'D' );

	}
	public function estructuraDocumento() {
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
}

