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
		
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		$this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$this->rutaURL .= '/archivos/actas_entrega_portatil/';
		$this->rutaAbsoluta .= '/archivos/actas_entrega_portatil/';
		$this->asosicarCodigoDocumento ();
		
		$this->crearPDF ();
		
		$arreglo = array (
				'nombre_contrato' => $this->nombreDocumento,
				'ruta_contrato' => $this->rutaURL . $this->nombreDocumento 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'registrarDocumentoCertificado', $arreglo );
		
		$this->registro_certificado = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		
		$arreglo = array (
				'id_beneficiario' => $_REQUEST ['id_beneficiario'],
				'tipologia' => "555",
				'nombre_documento' => $this->nombreDocumento,
				'ruta_relativa' => $this->rutaURL . $this->nombreDocumento 
		);
		
		// $cadenaSql = $this->miSql->getCadenaSql('registrarRequisito', $arreglo);
		// $this->registroRequisito = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
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
		$html2pdf->Output ( $this->rutaAbsoluta . $this->nombreDocumento, 'F' );
	}
	public function asosicarCodigoDocumento() {
		$this->prefijo = substr ( md5 ( uniqid ( time () ) ), 0, 6 );
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarParametro', '900' );
		$id_parametro = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
		$tipo_documento = $id_parametro ['id_parametro'];
		$descripcion_documento = $id_parametro ['id_parametro'] . '_' . $id_parametro ['descripcion'];
		$nombre_archivo = "AEP";
		$this->nombreDocumento = $_REQUEST ['id_beneficiario'] . "_" . $nombre_archivo . "_" . $this->prefijo . '.pdf';
	}
	public function estruturaDocumento() {
		/*
		 * $cadenaSql = $this->miSql->getCadenaSql('consultaNombreProyecto', $this->beneficiario['urbanizacion']);
		 * $urbanizacion = $this->esteRecursoOP->ejecutarAcceso($cadenaSql, "busqueda");
		 * $urbanizacion = $urbanizacion[0];
		 */
		$archivo_datos = '';
		foreach ( $_FILES as $key => $archivo ) {
			
			if ($archivo ['error'] == 0) {
				
				$this->prefijo = substr ( md5 ( uniqid ( time () ) ), 0, 6 );
				/*
				 * obtenemos los datos del Fichero
				 */
				$tamano = $archivo ['size'];
				$tipo = $archivo ['type'];
				$nombre_archivo = str_replace ( " ", "", $archivo ['name'] );
				/*
				 * guardamos el fichero en el Directorio
				 */
				$ruta_absoluta = $this->rutaAbsoluta . "/entidad/firmas/" . $this->prefijo . "_" . $nombre_archivo;
				
				$ruta_relativa = $this->rutaURL . "/entidad/firmas/" . $this->prefijo . "_" . $nombre_archivo;
				
				$archivo ['rutaDirectorio'] = $ruta_absoluta;
				
				if (! copy ( $archivo ['tmp_name'], $ruta_absoluta )) {
				}
				
				$archivo_datos = array (
						'ruta_archivo' => $ruta_relativa,
						'nombre_archivo' => $archivo ['name'],
						'campo' => $key 
				);
			}
		}
		
		{
			
			{
				$firmaBeneficiario = base64_decode ( $_REQUEST ['firmaBeneficiario'] );
				$firmaBeneficiario = str_replace ( "image/svg+xml,", '', $firmaBeneficiario );
				$firmaBeneficiario = str_replace ( '<?xml version="1.0" encoding="UTF-8" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">', '', $firmaBeneficiario );
				$firmaBeneficiario = str_replace ( "svg", 'draw', $firmaBeneficiario );
			}
			
			{
				
				$firmacontratista = base64_decode ( $_REQUEST ['firmaInstalador'] );
				$firmacontratista = str_replace ( "image/svg+xml,", '', $firmacontratista );
				$firmacontratista = str_replace ( '<?xml version="1.0" encoding="UTF-8" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">', '', $firmacontratista );
				$firmacontratista = str_replace ( "svg", 'draw', $firmacontratista );
			}
			
			$firmaBeneficiario = str_replace ( "height", 'height="40" pasos2', $firmaBeneficiario );
			$firmaBeneficiario = str_replace ( "width", 'width="125" pasos1', $firmaBeneficiario );
			$firmacontratista = str_replace ( "height", 'height="40" pasos2', $firmacontratista );
			$firmacontratista = str_replace ( "width", 'width="125" pasos1', $firmacontratista );
			
			$cadena = $_SERVER ['HTTP_USER_AGENT'];
			$resultado = stristr ( $cadena, "Android" );
			
			if ($resultado) {
				$firmacontratista = str_replace ( "<path", '<g viewBox="0 0 50 50" transform="scale(0.2,0.2)"><path', $firmacontratista );
				$firmacontratista = str_replace ( "/>", ' /></g>', $firmacontratista );
				$firmaBeneficiario = str_replace ( "<path", '<g viewBox="0 0 50 50" transform="scale(0.2,0.2)"><path', $firmaBeneficiario );
				$firmaBeneficiario = str_replace ( "/>", ' /></g>', $firmaBeneficiario );
			} else {
				$firmacontratista = str_replace ( "<path", '<g viewBox="0 0 50 50" transform="scale(0.08,0.08)"><path', $firmacontratista );
				$firmacontratista = str_replace ( "/>", ' /></g>', $firmacontratista );
				$firmaBeneficiario = str_replace ( "<path", '<g viewBox="0 0 50 50" transform="scale(0.08,0.08)"><path', $firmaBeneficiario );
				$firmaBeneficiario = str_replace ( "/>", ' /></g>', $firmaBeneficiario );
			}
		}
		
		ini_set ( 'xdebug.var_display_max_depth', 20000 );
		ini_set ( 'xdebug.var_display_max_children', 20000 );
		ini_set ( 'xdebug.var_display_max_data', 20000 );
		
		$firma_beneficiario = $firmaBeneficiario;
		
		$firma_contratista = $firmacontratista;
		
		$fecha = explode ( "-", $_REQUEST ['fecha_entrega'] );
		
		$dia = $fecha [0];
		$mes = [ 
				"",
				"Enero",
				"Febrero",
				"Marzo",
				"Abril",
				"Mayo",
				"Junio",
				"Julio",
				"Agosto",
				"Septiembre",
				"Octubre",
				"Noviembre",
				"Diciembre" 
		];
		$mes = $mes [$fecha [1]];
		$anno = $fecha [2];
		
		$vip = "";
		$est1 = "";
		$est2 = "";
		
		if ($_REQUEST ['tipo_beneficiario'] == 1) {
			$vip = "X";
		} else if ($_REQUEST ['tipo_beneficiario'] == 2) {
			$est1 = "X";
		} else if ($_REQUEST ['tipo_beneficiario'] == 3) {
			$est2 = "X";
		}
		
		$cc = "";
		$ce = "";
		
		if ($_REQUEST ['tipo_documento'] == 1) {
			$cc = "X";
		} else if ($_REQUEST ['tipo_documento'] == 2) {
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
												<td align='rigth' style='padding-rigth: 5px;width:75%;'>" . $_REQUEST ['primer_apellido'] . "&nbsp;" . $_REQUEST ['segundo_apellido'] . "</td>
											</tr>
											<tr>
												<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Nombres</td>
												<td align='rigth' style='width:75%;'>" . $_REQUEST ['nombres'] . "</td>
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
												<td align='rigth' style='padding: 5px 5px 5px 5px;width:100%;'>" . $_REQUEST ['numero_identificacion'] . "</td>
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
											<td align='rigth' style='width:75%;'>" . $_REQUEST ['marca'] . "</td>
										</tr>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Modelo</td>
											<td align='rigth' style='width:75%;'>" . $_REQUEST ['modelo'] . "</td>
										</tr>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Serial</td>
											<td align='rigth' style='width:75%;'>" . $_REQUEST ['serial'] . "</td>
										</tr>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Procesador</td>
											<td align='rigth' style='width:75%;'>" . $_REQUEST ['procesador'] . "</td>
										</tr>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Memoria RAM</td>
											<td align='rigth' style='width:75%;'>" . $_REQUEST ['memoria_ram'] . "</td>
										</tr>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Disco Duro</td>
											<td align='rigth' style='width:75%;'>" . $_REQUEST ['disco_duro'] . "</td>
										</tr>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Sistema Operativo</td>
											<td align='rigth' style='width:75%;'>" . $_REQUEST ['sistema_operativo'] . "</td>
										</tr>
										<tr>
											<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:25%;'>Periféricos</td>
											<td align='rigth' style='width:75%;'>" . $_REQUEST ['perifericos'] . "</td>
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
										<td align='center' style='width:15%;'>" . $dia . "</td>
										<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:15%;'>Mes</td>
										<td align='center' style='width:15%;'>" . $mes . "</td>
										<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:15%;'>Año</td>
										<td align='center' style='width:15%;'>" . $anno . "</td>
									</tr>
								</table>
							
								<br>
							
								<table width:100%;>
									<tr>
										<td bgcolor='#e2e0e0' align='rigth' style='padding: 5px 5px 5px 5px;width:10%;'>Lugar</td>
										<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:20%;'>Departamento</td>
										<td align='center' style='width:25%;'>" . $_REQUEST ['departamento'] . "</td>
										<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:20%;'>Municipio</td>
										<td align='center' style='width:25%;'>" . $_REQUEST ['municipio'] . "</td>
									</tr>
								</table>
							
								<br>
							
								<table style='width:100%;'>
									<tr>
										<td bgcolor='#FFFFFF' rowspan='2' align='center' style='padding: 5px 5px 5px 5px;width:40%;'>$firmaBeneficiario</td>
										<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:20%;'>Nombre</td>
										<td  bgcolor='#FFFFFF' align='rigth' style='width:40%;'>" . $_REQUEST['nombres'] . "&nbsp;" . $_REQUEST['primer_apellido'] . "&nbsp;" . $_REQUEST['segundo_apellido'] . "</td>
									</tr>
									<tr>
										<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:20%;'>No. Identificación</td>
										<td bgcolor='#FFFFFF' align='rigth' style='width:40%;'>" . $_REQUEST['numero_identificacion'] . "</td>
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
																<td bgcolor='#FFFFFF' rowspan='2' align='center' style='padding: 5px 5px 5px 5px;width:40%;'>$firmacontratista</td>
																<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:20%;'>Nombre</td>
																<td  bgcolor='#FFFFFF' align='rigth' style='width:40%;'>" . $_REQUEST ['nombre_ins'] . "</td>
															</tr>
															<tr>
																<td bgcolor='#f4f4f4' align='rigth' style='padding: 5px 5px 5px 5px;width:20%;'>No. Identificación</td>
																<td bgcolor='#FFFFFF' align='rigth' style='width:40%;'>" . $_REQUEST ['identificacion_ins'] . "</td>
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
		
		if ($_REQUEST['soporte'] != '') {
		
			$contenidoPagina .= "<br> <div style='page-break-after:always; clear:both'></div>
                                         <P style='text-align:center'><b>Soporte</b></P><br><br>";
			$contenidoPagina .= "<table style='text-align:center;width:100%;border:none'>
                                            <tr>
                                                <td style='text-align:center;border:none;width:100%'>
                                                    <img src='" . $_REQUEST['soporte'] . "'  width='500' height='500'>
                                                </td>
                                            </tr>
                                        </table>
                                     ";
		}
		
		$contenidoPagina .= "</page>";
		
		$this->contenidoPagina = $contenidoPagina;
	}
}
$miDocumento = new GenerarDocumento ( $this->miSql );

?>
