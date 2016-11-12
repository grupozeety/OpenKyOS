<?php

namespace reportes\actaEntregaPortatil\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

$ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/plugin/html2pfd/";

include $ruta . "/plugin/html2pdf/html2pdf.class.php";
class GenerarDocumento {
	public $miConfigurador;
	public $elementos;
	public $miSql;
	public $conexion;
	public $contenidoPagina;
	public $rutaURL;
	public $esteRecursoDB;
	public $clausulas;
	public $beneficiario;
	public $esteRecursoOP;
	public $rutaAbsoluta;
	public function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		
		// Conexion a Base de Datos
		$conexion = "interoperacion";
		$this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$conexion = "openproject";
		$this->esteRecursoOP = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		$this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		
		if (! isset ( $_REQUEST ["bloqueGrupo"] ) || $_REQUEST ["bloqueGrupo"] == "") {
			$this->rutaURL .= "/blocks/" . $_REQUEST ["bloque"] . "/";
			$this->rutaAbsoluta .= "/blocks/" . $_REQUEST ["bloque"] . "/";
		} else {
			$this->rutaURL .= "/blocks/" . $_REQUEST ["bloqueGrupo"] . "/" . $_REQUEST ["bloque"] . "/";
			$this->rutaAbsoluta .= "/blocks/" . $_REQUEST ["bloqueGrupo"] . "/" . $_REQUEST ["bloque"] . "/";
		}
		
		/**
		 * 1.
		 * Estruturar Documento
		 */
		
		$this->estruturaDocumento ();
		
		/**
		 * 2.
		 * Crear PDF
		 */
		
		$this->crearPDF ();
	}
	public function crearPDF() {
		ob_start ();
		$html2pdf = new \HTML2PDF ( 'P', 'LETTER', 'es', true, 'UTF-8', array (
				2,
				2,
				2,
				10 
		) );
		$html2pdf->pdf->SetDisplayMode ( 'fullpage' );
		$html2pdf->WriteHTML ( $this->contenidoPagina );
		$html2pdf->Output ( 'Acta_Entrega_servicio_CC_' . $this->infoCertificado ['identificacion'] . '_' . date ( 'Y-m-d' ) . '.pdf', 'D' );
	}
	public function estruturaDocumento() {
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaInformacionCertificado' );
		$infoCertificado = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
		$this->infoCertificado = $infoCertificado;
		
		$fecha = explode("-",$this->infoCertificado['fecha_entrega']);
		
		$dia = $fecha[0];
		$mes = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
		$mes = $mes[$fecha[1]];
		$anno = $fecha[2];
		
		$vip = "";
		$est1 = "";
		$est2 = "";
		
		if($this->infoCertificado['tipo_beneficiario'] == 1){
			$vip = "X";
		}else if($this->infoCertificado['tipo_beneficiario'] == 2){
			$est1 = "X";
		}else if($this->infoCertificado['tipo_beneficiario'] == 3){
			$est2 = "X";
		}
		
		$cc = "";
		$ce = "";
		
		if($this->infoCertificado['tipo_documento'] == 1){
			$cc = "X";
		}else if($this->infoCertificado['tipo_documento'] == 2){
			$ce = "X";
		}
		
		setlocale ( LC_ALL, "es_CO.UTF-8" );
		$contenidoPagina = "
                            <style type=\"text/css\">
                                table {

                                    font-family:Helvetica, Arial, sans-serif; /* Nicer font */

                                    border-collapse:collapse; border-spacing: 3px;
                                }
                                td, th {
                                    border: 1px solid #000000;
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
                            </style>



                        <page backtop='35mm' backbottom='30mm' backleft='10mm' backright='10mm' footer='page'>
                            <page_header>
                             <br>
                            <br>
                                    <table  style='width:100%;' >
                                        <tr>
                                            <td rowspan='3' style='width:33.3%;text-align=center;'><img src='" . $this->rutaURL . "frontera/css/imagen/politecnica.png'  width='125' height='40'></td>
                                            <td rowspan='3' style='width:33.3%;text-align=center;'><b>ACTA DE ENTREGA DE SERVICIO DE BANDA ANCHA AL USUARIO</b></td>
                                            <td align='center' style='width:33.3%;'>CODIGO: CPN-FO-CDII-60</td>
                                        </tr>

                                        <tr>
                                             <td align='center' style='width:33.3%;'>VERSIÓN: 01</td>
                                        </tr>
                                        <tr>
                                             <td align='center' style='width:33.3%;'>FECHA: 2016-07-06</td>
                                        </tr>
                                    </table>

                        </page_header>
                                            		
                        <page_footer>
							<table  style='width:100%;' >
								<tr>
									<td align='center' style='width:100%;border=none;' >
										<img src='" . $this->rutaURL . "frontera/css/imagen/logos_contrato.png'  width='500' height='35'>
									</td>
								</tr>
							</table>
   					 	</page_footer>";
		
		
						$contenidoPagina .= "
							
							<br><br>
							
							<table width:100%;>
								<tr>
									<td style='vertical-align:top;border:none;width:20%;'>
										Yo:
									</td>
									<td style='border:none;width:80%;'>
										<table width:100%;>
											<tr>
												<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Apellidos</td>
												<td align='rigth' style='padding-rigth: 5px;width:75%;'>" . $this->infoCertificado['primer_apellido'] . "&nbsp;" . $this->infoCertificado['segundo_apellido'] . "</td>
											</tr>
											<tr>
												<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Nombres</td>
												<td align='rigth' style='width:75%;'>" . $this->infoCertificado['nombre'] . "</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							
							<br>
							
							<table width:100%;>
								<tr>
									<td style='vertical-align:top;border:none;width:20%;'>
										Identificado con:
									</td>
									<td style='border:none;width:80%;'>
										<table width:100%;>
											<tr>
												<td align='center' style='width:10%;'>" . $cc . "</td>
												<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:40%;'>Cédula de Ciudadanía</td>
												<td align='center' style='width:10%;'>" . $ce . "</td>
												<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:40%;'>Cédula de Extranjería</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							
							<br>
							
							<table width:100%;>
								<tr>
									<td style='vertical-align:top;border:none;width:20%;'>
										Número:
									</td>
									<td style='border:none;width:80%;'>
										<table width:100%;>
											<tr>
												<td align='rigth' style='padding: 5px 5px 5px 5px;width:100%;'>" . $this->infoCertificado['identificacion'] . "</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							
							<br>
							
							<table width:100%;>
								<tr>
									<td style='vertical-align:top;border:none;width:20%;'>
										Habitante de:
									</td>
									<td style='border:none;width:80%;'>
										<table width:100%;>
											<tr>
												<td align='center' style='width:25%;'>" . $vip . "</td>
												<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:75%;'>Proyecto de Vivienda de Interés Prioritario (VIP)</td>
												
											</tr>
											<tr>
												<td align='center' style='width:25%;'>" . $est1 . "</td>
												<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:75%;'>Vivienda de Estrato Uno de uso residencial</td>
											</tr>
											<tr>
												<td align='center' style='width:25%;'>" . $est2 . "</td>
												<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:75%;'>Vivienda de Estrato Dos de uso residencial</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
								
							<br>
							
							<table width:100%;>
								<tr>
									<td align='center' style='width:100%;border:none;'>
										<b>CERTIFICO BAJO GRAVEDAD JURAMENTADA:</b>
									</td>
								</tr>
							</table>
														
							<ol>

								<li value='1'>Que recibo un computador NUEVO y EN ÓPTIMAS CONDICIONES, con las siguientes características:
									<br>
									<br>
									<table width:100%;>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Marca</td>
											<td align='rigth' style='width:75%;'>" . $this->infoCertificado['marca'] . "</td>
										</tr>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Modelo</td>
											<td align='rigth' style='width:75%;'>" . $this->infoCertificado['modelo'] . "</td>
										</tr>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Serial</td>
											<td align='rigth' style='width:75%;'>" . $this->infoCertificado['serial'] . "</td>
										</tr>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Procesador</td>
											<td align='rigth' style='width:75%;'>" . $this->infoCertificado['procesador'] . "</td>
										</tr>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Memoria RAM</td>
											<td align='rigth' style='width:75%;'>" . $this->infoCertificado['memoria_ram'] . "</td>
										</tr>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Disco Duro</td>
											<td align='rigth' style='width:75%;'>" . $this->infoCertificado['disco_duro'] . "</td>
										</tr>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Sistema Operativo</td>
											<td align='rigth' style='width:75%;'>" . $this->infoCertificado['sistema_operativo'] . "</td>
										</tr>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Periféricos</td>
											<td align='rigth' style='width:75%;'>" . $this->infoCertificado['perifericos'] . "</td>
										</tr>
									</table>
									<br>
								</li>
								<li>Que entiendo que dicho terminal no tiene ningún costo adicional y se encuentra incluido con el
									servicio de internet al cual me suscribo con el proveedor.</li>
								<li>Que este computador portátil cuenta con el servicio de internet adquirido por el suscrito.</li>
								<li>Que me comprometo a mantener posesión y dominio de este equipo, para darle un adecuado uso permitiendo el acceso a Internet a los miembros de mi núcleo familiar.</li>
								<li>Que el mediante el presente documento manifiesto mi interés en participar en las capacitaciones que hacen parte del componente de apropiación social del contrato.</li>
							</ol>
						
								<br>Para constancia de lo anterior, firmo con copia de mi documento de identidad bajo gravedad de juramento:
								<br><br>
							
								<table width:100%;>
									<tr>
										<td bgcolor='#e2e0e0' align='rigth' style='padding: 5px 5px 5px 5px;width:10%;'>Fecha</td>
										<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:15%;'>Día</td>
										<td align='center' style='width:15%;'>". $dia . "</td>
										<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:15%;'>Mes</td>
										<td align='center' style='width:15%;'>". $mes . "</td>
										<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:15%;'>Año</td>
										<td align='center' style='width:15%;'>". $anno . "</td>
									</tr>
								</table>
							
								<br>
							
								<table width:100%;>
									<tr>
										<td bgcolor='#e2e0e0' align='rigth' style='padding: 5px 5px 5px 5px;width:10%;'>Lugar</td>
										<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:20%;'>Departamento</td>
										<td align='center' style='width:25%;'>" . $this->infoCertificado['departamento'] . "</td>
										<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:20%;'>Municipio</td>
										<td align='center' style='width:25%;'>" . $this->infoCertificado['municipio'] . "</td>
									</tr>
								</table>
							
								<br>
												
								<table style='width:100%;'>
									<tr>
										<td bgcolor='#FFFFFF' rowspan='2' align='rigth' style='vertical-align:top;padding: 5px 5px 5px 5px;width:40%;'>Firma</td>
										<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:20%;'>Nombre</td>
										<td  bgcolor='#FFFFFF' align='rigth' style='width:40%;'>" . $this->infoCertificado['nombre'] . "&nbsp;" . $this->infoCertificado['primer_apellido'] . "&nbsp;" . $this->infoCertificado['segundo_apellido'] . "</td>
									</tr>
									<tr>
										<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:20%;'>No. Identificación</td>
										<td bgcolor='#FFFFFF' align='rigth' style='width:40%;'>" . $this->infoCertificado['identificacion'] . "</td>
									</tr>
								</table>
							
								<br>
												
								<table style='width:100%;'>
									<tr>
										<td bgcolor='#f4f4f4'>
											<table style='width:100%;'>
												<tr>
													<td style='padding: 5px 5px 5px 5px;border:none;'>
														Para uso exclusivo de Corporación Politécnica
														<br><br>Funcionario que entrega
														<br><br>
													</td>
												</tr>
											</table>
											<table style='width:100%;'>
												<tr>
													<td style='padding: 5px 5px 5px 5px;border:none;width:100%;'>
														<table style='width:100%;'>
															<tr>
																<td bgcolor='#FFFFFF' rowspan='2' align='rigth' style='vertical-align:top;padding: 5px 5px 5px 5px;width:40%;'>Firma</td>
																<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:20%;'>Nombre</td>
																<td  bgcolor='#FFFFFF' align='rigth' style='width:40%;'>" . $this->infoCertificado['nombre_ins'] . "</td>
															</tr>
															<tr>
																<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:20%;'>No. Identificación</td>
																<td bgcolor='#FFFFFF' align='rigth' style='width:40%;'>" . $this->infoCertificado['identificacion_ins'] . "</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
											<br><br>
										</td>
									</tr>
								</table>
							
								<br>
							
								<table width:100%;>
									<tr>
										<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:100%;'>
											<b>Datos de Contacto del Fabricante</b>
											<br><br>Sitio web de soporte: http://www.hp.com/latam/co/soporte/cas/
											<br><br>Teléfono: 01-8000-51-474-68368 desde cualquier lugar del país.
										</td>
									</tr>
								</table>
							
					";
						
						if ($this->infoCertificado['soporte'] != '') {
						
							$contenidoPagina .= "<br> <div style='page-break-after:always; clear:both'></div>
                                         <P style='text-align:center'><b>Soporte</b></P><br><br>";
							$contenidoPagina .= "<table style='text-align:center;width:100%;border:none'>
                                            <tr>
                                                <td style='text-align:center;border:none;width:100%'>
                                                    <img src='" . $this->infoCertificado['soporte'] . "'  width='500' height='500'>
                                                </td>
                                            </tr>
                                        </table>
                                     ";
						}
						
						$contenidoPagina .= "";
						
		$contenidoPagina .= "</page>";
		
		$this->contenidoPagina = $contenidoPagina;
	}
}
$miDocumento = new GenerarDocumento ( $this->sql );

?>
