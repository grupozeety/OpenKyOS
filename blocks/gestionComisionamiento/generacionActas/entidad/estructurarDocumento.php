<?php

namespace gestionComisionamiento\generacionActas\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

include $ruta . "/plugin/html2pdf/html2pdf.class.php";

class GenerarDocumento {
    public $miConfigurador;
    public $agendamientos;
    public $miSql;
    public $conexion;
    public $contenidoPagina;
    public $rutaURL;
    public function __construct($sql, $agendamientos) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->agendamientos = $agendamientos;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {

            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }

        /**
         *  1. Estruturar Documento
         **/

        $this->estruturaDocumento();

        /**
         *  2. Crear PDF
         **/

        $this->crearPDF();

    }

    public function crearPDF() {

        ob_start();
        $html2pdf = new \HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(
            2,
            2,
            2,
            10,
        ));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output('ActaEntregaComisionamiento' . date('Y-m-d') . '.pdf', 'D');

    }
    public function estruturaDocumento() {

        $contenidoPagina = "	<style type=\"text/css\">
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
							</style>



						<page backtop='35mm' backbottom='10mm' backleft='5mm' backright='5mm' footer='page'>
							<page_header>
							<br>
							<br>
    								<table  style='width:100%;' >
							            <tr>
							            	<td rowspan='3' style='width:33.3%;text-align=center;'><img src='" . $this->rutaURL . "frontera/css/imagenes/politecnica.png'  width='125' height='40'></td>
					        				<td rowspan='3' style='width:33.3%;text-align=center;'><b>RECIBO A SATISFACCIÓN DE LA INSTALACIÓN Y PUESTA EN SERVICIO PARA ACCESOS VIP Y ESTRATOS 1 Y 2</b></td>
							                <td align='center' style='width:33.3%;'>CODIGO: CPN-FO-CDII-63</td>
							            </tr>

							            <tr>
							                 <td align='center' style='width:33.3%;'>VERSIÓN: 01</td>
							            </tr>
							            <tr>
							                 <td align='center' style='width:33.3%;'>FECHA: " . date('Y-m-d') . "</td>
							            </tr>
							        </table>

						</page_header>";

        $contenidoPagina .= "<table  style='width:100%;border:none;' >
							            <tr>
							            	<td align='center' style='width:12%;border:none;border-right:#CCC;'>Consecutivo</td>
							            	<td align='center' style='width:10%;'> </td>
							            	<td align='center' style='width:13%;border:none;border-right:#CCC;'>Id del Nodo</td>
							            	<td align='center' style='width:15%;'> </td>
							            	<td align='center' style='width:20%;border:none;border-right:#CCC;'>Fecha de Comisionamiento</td>
							            	<td align='center' style='width:10%;'>DD</td>
							            	<td align='center' style='width:10%;'>MM</td>
							            	<td align='center' style='width:10%;'>AAAA</td>
							            </tr>
							        </table>
									<br>
							        <table  style='width:100%;' >
							            <tr>
							            	<td align='center' style='width:100%;'>1. INFORMACIÓN GENERAL</td>
							           	</tr>
							           	<tr>
							            	<td align='center' style='width:100%;'>

								            	<table style='width:100%;'>
									            	<tr>
									            		<td colspan='4' style='width:100%;border:none;'>1.1 Lugar de instalación del equipo:<br>       </td>
									           		</tr>
									           		<tr>
									           			<td style='width:100%;border:none;'>
									           				<br>
										           			<table width:100%;>
										           			<tr>
											            		<td style='width:25%;border:none;border-right:#CCC;'>Departamento de instalación:</td>
											            		<td align='center' style='width:25%;border:#CCC;'> </td>
											            		<td style='width:20%;border:none;border-right:#CCC;'>&nbsp;Municipio o Ciudad:</td>
											            		<td align='center' style='width:25%;border:#CCC;'> </td>
											            		<td align='center' style='width:5%;border:none;'> </td>
										           			</tr>
										           			<tr>
											            		<td style='width:25%;border:none;border-right:#CCC;'>Codigo DANE:</td>
											            		<td align='center' style='width:25%;border:#CCC;'> </td>
											            		<td style='width:20%;border:none;border-right:#CCC;'>&nbsp;Localidad o Barrio:</td>
											            		<td align='center' style='width:25%;border:#CCC;'> </td>
											            		<td align='center' style='width:5%;border:none;'> </td>
										           			</tr>
										           			<tr>
											            		<td style='width:25%;border:none;border-right:#CCC;'>Dirección de instalación:</td>
											            		<td align='center' style='width:25%;border:#CCC;'> </td>
											            		<td style='width:20%;border:none;border-right:#CCC;'>&nbsp;Nombre del Proyecto:</td>
											            		<td align='center' style='width:25%;border:#CCC;'> </td>
											            		<td align='center' style='width:5%;border:none;'> </td>
										           			</tr>
										           			</table>
										           			<br>
									           			</td>
									           		</tr>
									           		<tr>
									            		<td colspan='4' style='width:100%;border:none;'>Coordenadas del lugar de instalación: </td>
									           		</tr>

									           		<tr>
									           			<td style='width:100%;border:none;'>
									           				<br>
										           			<table width:100%;>
											           			<tr>
												            		<td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Latitud: (grados)</td>
												            		<td align='center' style='width:12.5%;border:#CCC;'> </td>
												            		<td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Latitud: (minutos)</td>
												            		<td align='center' style='width:12.5%;border:#CCC;'> </td>
												            		<td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Latitud: (segundos)</td>
												            		<td align='center' style='width:12.5%;border:#CCC;'> </td>
												            		<td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Dirección:</td>
												            		<td align='center' style='width:12.5%;border:#CCC;'> </td>
											           			</tr>
											           			<tr>
												            		<td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Latitud: (grados)</td>
												            		<td align='center' style='width:12.5%;border:#CCC;'> </td>
												            		<td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Latitud: (minutos)</td>
												            		<td align='center' style='width:12.5%;border:#CCC;'> </td>
												            		<td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Latitud: (segundos)</td>
												            		<td align='center' style='width:12.5%;border:#CCC;'> </td>
												            		<td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Dirección:</td>
												            		<td align='center' style='width:12.5%;border:#CCC;'> </td>
											           			</tr>
										           			</table>
										           			<br>
									           			</td>
									           		</tr>
									           	</table>
							            	</td>
							           	</tr>
							           	<tr>
							          	 	<td align='center' style='width:100%;'>2. INFORMACIÓN TÉCNICO QUE COMISIONA</td>
							           	</tr>
							           	<tr>
								           	<td style='width:100%;'>
										           				<br>
											           			<table width:100%;>
												           			<tr>
													            		<td style='width:20%;border:none;border-right:#CCC;'>Nombre del Técnico:</td>
													            		<td align='center' style='width:30%;border:#CCC;'> </td>
													            		<td style='width:20%;border:none;border-right:#CCC;'>&nbsp;Empresa Contratista:</td>
													            		<td align='center' style='width:25%;border:#CCC;'>POLITÉCNICA</td>
													            		<td align='center' style='width:5%;border:none;'> </td>
												           			</tr>
																	<tr>
													            		<td style='width:20%;border:none;border-right:#CCC;'>Telefono de Contacto:</td>
													            		<td align='center' style='width:30%;border:#CCC;'> </td>
													            		<td style='width:20%;border:none;border-right:#CCC;'>&nbsp;E-mail:</td>
													            		<td align='center' style='width:25%;border:#CCC;'> </td>
													            		<td align='center' style='width:5%;border:none;'> </td>
												           			</tr>
											           			</table>
											           			<br>
										    </td>
							           	</tr>
							           	<tr>
							           	    <td align='center' style='width:100%;'>3. TIPO DE TECNOLOGÍA</td>
							            </tr>
							           	<tr>
								           	<td style='width:100%;'>
										           				<br>
											           			<table width:100%;>
												           			<tr>
													            		<td align='right' style='width:33%;border:none;border-right:#CCC;'>HFC:</td>
													            		<td align='center' style='width:10%;border:#CCC;'> </td>
													            		<td align='right' style='width:20%;border:none;border-right:#CCC;'>&nbsp;WMAN:</td>
													            		<td align='center' style='width:10%;border:#CCC;'> </td>
													            		<td align='center' style='width:10%;border:none;'> </td>
												           			</tr>
											           			</table>
											           			<br>
										    </td>
							           	</tr>
							           	<tr>
							           	    <td align='center' style='width:100%;'>4. INFORMACIÓN DE EQUIPOS</td>
							            </tr>
							            <tr>
								           	<td style='width:100%;'>
										           				<br>
											           			<table width:100%;>
												           			<tr>
													            		<td style='width:20%;border:none;'>Serial del EOC:</td>
													            		<td align='center' style='width:30%;border:none;border-bottom:#CCC;'> </td>
													            		<td style='width:20%;border:none;'>&nbsp;Serial Esclavo:</td>
													            		<td align='center' style='width:25%;border:none;border-bottom:#CCC;'> </td>
													            		<td align='center' style='width:5%;border:none;'> </td>
												           			</tr>
																	<tr>
													            		<td style='width:20%;border:none;'>Mac del EOC:</td>
													            		<td align='center' style='width:30%;border:none;border-bottom:#CCC;'> </td>
													            		<td style='width:20%;border:none;'>&nbsp;Mac del Esclavo:</td>
													            		<td align='center' style='width:25%;border:none;border-bottom:#CCC;'> </td>
													            		<td align='center' style='width:5%;border:none;'> </td>
												           			</tr>
												           			<tr>
													            		<td style='width:20%;border:none;'>IP del EOC:</td>
													            		<td align='center' style='width:30%;border:none;border-bottom:#CCC;'> </td>
													            		<td style='width:20%;border:none;'>&nbsp;IP del Esclavo:</td>
													            		<td align='center' style='width:25%;border:none;border-bottom:#CCC;'> </td>
													            		<td align='center' style='width:5%;border:none;'> </td>
												           			</tr>
											           			</table>
											           			<br>
										    </td>
							           	</tr>
							           	<tr>
							           	    <td align='center' style='width:100%;'>5. RELACIÓN DE EQUIPOS INSTALADOS</td>
							            </tr>
							            <tr>
								           	<td style='width:100%;'>
										           				<br>
											           			<table width:100%;>
												           			<tr>
													            		<td colspan='2' align='center'style='width:20%;'>EQUIPO(PC, Antena, Router,entre otros)</td>
													            		<td align='center'style='width:20%;'>MAC</td>
													            		<td align='center'style='width:20%;'>SERIAL</td>
													            		<td align='center'style='width:20%;'>MARCA</td>
													            		<td align='center'style='width:20%;'>MODELO</td>
													            	</tr>
													            	<tr>
													            		<td align='center'style='width:5%;'>1</td>
													            		<td align='center'style='width:15%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            	</tr>
													            	<tr>
													            		<td align='center'style='width:5%;'>2</td>
													            		<td align='center'style='width:15%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            	</tr>
													            	<tr>
													            		<td align='center'style='width:5%;'>3</td>
													            		<td align='center'style='width:15%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            	</tr>
																</table>
											    				<br>
										    </td>
										</tr>
										<tr>
										<td style='width:100%;'>5.1 Reporte de Fallas(reportar las fallas si aplica durante el proceso de instalación):<br>&nbsp;<br>&nbsp;<br>Observaciones:<br>&nbsp;<br>&nbsp;<br>&nbsp;</td>
							           	</tr>
							           	<tr>
							           		<td align='center' style='width:100%;'>6. ENTREGA DE TERMINAL Marque con una X según corresponda:</td>
							           	</tr>
							           	<tr>
								           	<td style='width:100%;'>
										           				<br>
											           			<table width:100%;>
												           			<tr>
													            		<td style='width:35%;border:none;'>Elementos que contiene la caja de entrega:</td>
													            		<td align='right' style='width:20%;border:none;border-right:#CCC;'>Computador</td>
													            		<td align='center' style='width:4%;'> </td>
													            		<td align='center' style='width:15%;border:none;border-right:#CCC;'>&nbsp;Manual de Uso</td>
													            		<td align='center' style='width:4%;'> </td>
													            		<td align='center' style='width:10%;border:none;border-right:#CCC;'>&nbsp;Cargador</td>
													            		<td align='center' style='width:4%;'> </td>
													            		<td align='center' style='width:5%;border:none;'> </td>
												           			</tr>
												           		</table>
											           			<br>
											           			<table width:100%;>
												           			<tr>
													            		<td style='width:35%;border:none;'>Estado físico del equipo:</td>
													            		<td align='right' style='width:20%;border:none;border-right:#CCC;'>Sin Rayados</td>
													            		<td align='center' style='width:4%;'> </td>
													            		<td align='center' style='width:15%;border:none;border-right:#CCC;'>Sin Golpes y/o Hendiduras</td>
													            		<td align='center' style='width:4%;'> </td>
													            		<td align='center' style='width:10%;border:none;'> </td>
													            		<td align='center' style='width:4%;border:none;'> </td>
													            		<td align='center' style='width:5%;border:none;'> </td>
												           			</tr>
												           		</table>
											           			<br>
											           			<table width:100%;>
												           			<tr>
													            		<td style='width:35%;border:none;'>Prueba de funcionalidad:</td>
													            		<td align='right' style='width:20%;border:none;border-right:#CCC;'>Equipo enciende correctamente</td>
													            		<td align='center' style='width:4%;'> </td>
													            		<td align='center' style='width:15%;border:none;border-right:#CCC;'>Equipo navega en Internet</td>
													            		<td align='center' style='width:4%;'> </td>
													            		<td align='center' style='width:10%;border:none;'> </td>
													            		<td align='center' style='width:4%;border:none;'> </td>
													            		<td align='center' style='width:5%;border:none;'> </td>
												           			</tr>
												           		</table>
											           			<br>
											           			<table width:100%;>
												           			<tr>
													            		<td style='width:35%;border:none;'>En general el estado físico y funcionalidad del equipo es:</td>
													            		<td align='right' style='width:20%;border:none;border-right:#CCC;'>Bueno</td>
													            		<td align='center' style='width:4%;'> </td>
													            		<td align='center' style='width:15%;border:none;border-right:#CCC;'>&nbsp;Regular</td>
													            		<td align='center' style='width:4%;'> </td>
													            		<td align='center' style='width:10%;border:none;border-right:#CCC;'>&nbsp;Malo</td>
													            		<td align='center' style='width:4%;'> </td>
													            		<td align='center' style='width:5%;border:none;'> </td>
												           			</tr>
												           		</table>
											           			<br>
										    </td>
							           	</tr>
							           	<tr>
							           		<td align='center' style='width:100%;'>7. PRUEBAS DE CONECTIVIDAD</td>
							           	</tr>
							           	<tr>
								           	<td style='width:100%;'>
																<br>
											           			<table width:100%;>
												           			<tr>
													            		<td style='width:100%;border:none;'>7.1. Información equipo del cliente (En caso de sistema operativo Android- No aplica)</td>
												           			</tr>
												           		</table>
											           			<br>
											           			<table width:100%;>
												           			<tr>
													            		<td style='width:20%;border:none;border-right:#CCC;'>Direccionamiento IP:</td>
													            		<td align='center' style='width:10%;border:#CCC;'> </td>
													            		<td style='width:15%;border:none;border-right:#CCC;'>&nbsp;Dirección MAC:</td>
													            		<td align='center' style='width:15%;border:#CCC;'> </td>
													            		<td style='width:20%;border:none;border-right:#CCC;'>&nbsp;Máscara de Subred:</td>
													            		<td align='center' style='width:15%;border:#CCC;'> </td>
													            		<td align='center' style='width:5%;border:none;'> </td>
												           			</tr>
												           			<tr>
													            		<td style='width:20%;border:none;border-right:#CCC;'>Gateway:</td>
													            		<td align='center' style='width:10%;border:#CCC;'> </td>
													            		<td style='width:15%;border:none;border-right:#CCC;'>&nbsp;Servidor DNS:</td>
													            		<td align='center' style='width:15%;border:#CCC;'> </td>
													            		<td style='width:20%;border:none;'> </td>
													            		<td align='center' style='width:15%;border:none;'> </td>
													            		<td align='center' style='width:5%;border:none;'> </td>
												           			</tr>
												           		</table>
											           			<br>
											           			<table width:100%;>
												           			<tr>
													            		<td style='width:100%;border:none;'>7.2. Pruebas Tracert</td>
												           			</tr>
												           		</table>
											           			<br>
											           			<table width:100%;>
												           			<tr>
													            		<td style='width:20%;border:none;border-right:#CCC;'> </td>
													            		<td align='center' style='width:30%;border:#CCC;'>LINUX</td>
													            		<td align='center' style='width:50%;border:none;'>tracert 'nodo'&nbsp;&nbsp;|&nbsp;&nbsp;traceroute 'nodo'</td>
												           			</tr>
												           		</table>
											           			<br>
											           			<table width:100%;>
												           			<tr>
													            		<td align='center'style='width:20%;'>REFERENCIA</td>
													            		<td align='center'style='width:20%;'>MAC</td>
													            		<td align='center'style='width:20%;'>SERIAL</td>
													            		<td align='center'style='width:20%;'>MARCA</td>
													            		<td align='center'style='width:20%;'>MODELO</td>
													            	</tr>
													            	<tr>
													            		<td align='center'style='width:5%;'>1</td>
													            		<td align='center'style='width:15%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            	</tr>
													            	<tr>
													            		<td align='center'style='width:5%;'>2</td>
													            		<td align='center'style='width:15%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            	</tr>
													            	<tr>
													            		<td align='center'style='width:5%;'>3</td>
													            		<td align='center'style='width:15%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            		<td align='center'style='width:20%;'> </td>
													            	</tr>
																</table>
											    				<br>
								           	</td>
								        </tr>
							      </table>";

        $contenidoPagina .= " </page> ";

        $this->contenidoPagina = $contenidoPagina;
    }
}
$miDocumento = new GenerarDocumento($this->miSql, $this->agendamientos);

?>