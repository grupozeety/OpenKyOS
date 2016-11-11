<?php
namespace reportes\actaEntregaPortatil\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

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
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $conexion = "openproject";
        $this->esteRecursoOP = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }

        /**
         *  1. Estruturar Documento
         **/

        $this->estruturaDocumento();

        /**
         *  2. Crear PDF
         **/

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
        $this->rutaURL .= '/archivos/actas_entrega_servicios/';
        $this->rutaAbsoluta .= '/archivos/actas_entrega_servicios/';
        $this->asosicarCodigoDocumento();

        $this->crearPDF();

        $arreglo = array(
            'nombre_contrato' => $this->nombreDocumento,
            'ruta_contrato' => $this->rutaURL . $this->nombreDocumento);

        $cadenaSql = $this->miSql->getCadenaSql('registrarDocumentoCertificado', $arreglo);

        $this->registro_certificado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

        $arreglo = array(
            'id_beneficiario' => $_REQUEST['id_beneficiario'],
            'tipologia' => "555",
            'nombre_documento' => $this->nombreDocumento,
            'ruta_relativa' => $this->rutaURL . $this->nombreDocumento,
        );

        //$cadenaSql = $this->miSql->getCadenaSql('registrarRequisito', $arreglo);
        //$this->registroRequisito = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
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
        $html2pdf->Output($this->rutaAbsoluta . $this->nombreDocumento, 'F');

    }

    public function asosicarCodigoDocumento() {

        $this->prefijo = substr(md5(uniqid(time())), 0, 6);
        $cadenaSql = $this->miSql->getCadenaSql('consultarParametro', '900');
        $id_parametro = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
        $tipo_documento = $id_parametro['id_parametro'];
        $descripcion_documento = $id_parametro['id_parametro'] . '_' . $id_parametro['descripcion'];
        $nombre_archivo = "AES";
        $this->nombreDocumento = $_REQUEST['id_beneficiario'] . "_" . $nombre_archivo . "_" . $this->prefijo . '.pdf';
    }

    public function estruturaDocumento() {
/*
$cadenaSql = $this->miSql->getCadenaSql('consultaNombreProyecto', $this->beneficiario['urbanizacion']);
$urbanizacion = $this->esteRecursoOP->ejecutarAcceso($cadenaSql, "busqueda");
$urbanizacion = $urbanizacion[0];
 */
        $archivo_datos = '';
        foreach ($_FILES as $key => $archivo) {

            if ($archivo['error'] == 0) {

                $this->prefijo = substr(md5(uniqid(time())), 0, 6);
                /*
                 * obtenemos los datos del Fichero
                 */
                $tamano = $archivo['size'];
                $tipo = $archivo['type'];
                $nombre_archivo = str_replace(" ", "", $archivo['name']);
                /*
                 * guardamos el fichero en el Directorio
                 */
                $ruta_absoluta = $this->rutaAbsoluta . "/entidad/firmas/" . $this->prefijo . "_" . $nombre_archivo;

                $ruta_relativa = $this->rutaURL . "/entidad/firmas/" . $this->prefijo . "_" . $nombre_archivo;

                $archivo['rutaDirectorio'] = $ruta_absoluta;

                if (!copy($archivo['tmp_name'], $ruta_absoluta)) {

                }

                $archivo_datos = array(
                    'ruta_archivo' => $ruta_relativa,
                    'nombre_archivo' => $archivo['name'],
                    'campo' => $key,
                );
            }

        }

        $firma_beneficiario = (isset($archivo_datos['ruta_archivo'])) ? "<img src='" . $archivo_datos['ruta_archivo'] . "'  width='125' height='40'>" : " ";

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

                        </page_header>";
		
		$contenidoPagina .= "
        			<h4 align='center'> ACTA DE ENTREGA DE SERVICIO DE BANDA ANCHA AL USUARIO </h4> 
                    <b>PRODUCTO	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b><br><br>
        			<b>CLIENTE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> &nbsp;&nbsp;" . $_REQUEST['nombres'] . "&nbsp;" . $_REQUEST['primer_apellido'] . "&nbsp;" . $_REQUEST['segundo_apellido'] . "<br><br>
        			<b>N° CEDULA &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> &nbsp;&nbsp;".  $_REQUEST['numero_identificacion'] . "<br><br>
        			<b>FECHA INSTALACIÓN &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['fecha_instalacion'] . "<br><br>
        			<b>TIPO DE VIVIENDA &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['tipo_beneficiario'] . "<br><br>
        			<b>DATOS DEL SERVICIO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;<br><br>
        			<b>DIRECCIÓN DEL PREDIO &nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['direccion'] . "<br><br>
	        		<b>DEPARTAMENTO	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['departamento'] . "<br><br>
	        		<b>MUNICIPIO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['municipio'] . "<br><br>
	        		<b>NOMBRE DEL PROYECTO &nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['urbanizacion'] . "<br><br>
	        		<b>CODIGO DANE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['codigo_dane'] . "<br><br>
	        		<b>LATITUD &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['contacto'] . "<br><br>
	        		<b>LONGITUD &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['contacto'] . "<br><br>
	        		<b>CONTACTO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['contacto'] . "<br><br>
	        		<b>TELÉFONO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['telefono'] . "<br><br>
	        		<b>E-MAIL &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['correo'] . "<br><br>
	        		<b>TIPO DE TECNOLOGÍA &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['tipo_tecnologia'] . "<br><br><br>
	        		<b>DETALLE DE LOS EQUIPOS INSTALADOS</b><br><br>
        		
                    <br>

        		 	<table width:100%;>
                        <tr>
	                        <td align='center'style='width:14%;'><b>EQUIPO</b></td>
							<td align='center'style='width:16%;'><b>No. ACTIVO FIJO</b></td>
							<td align='center'style='width:14%;'><b>MAC</b></td>
	                        <td align='center'style='width:14%;'><b>SERIAL</b></td>
	                        <td align='center'style='width:14%;'><b>MARCA</b></td>
	                        <td align='center'style='width:14%;'><b>CANT</b></td>
					 		<td align='center'style='width:14%;'><b>IP</b></td>
                       	</tr>
                        <tr>
                        	<td align='center'style='width:14%;'>ESCLAVO</td>
                        	<td align='center'style='width:16%;'> </td>
                            <td align='center'style='width:14%;'> </td>
                            <td align='center'style='width:14%;'> </td>
                            <td align='center'style='width:14%;'> </td>
				 			<td align='center'style='width:14%;'> </td>
							<td align='center'style='width:14%;'> </td>
                        </tr>
						<tr>
                        	<td align='center'style='width:14%;'>COMPUTADOR</td>
                        	<td align='center'style='width:16%;'> </td>
                            <td align='center'style='width:14%;'> </td>
                            <td align='center'style='width:14%;'> </td>
                            <td align='center'style='width:14%;'> </td>
				 			<td align='center'style='width:14%;'> </td>
							<td align='center'style='width:14%;'> </td>
                        </tr>
                    </table>
					<br>
					<b>PRUEBAS</b>
					<table width:100%;>
                        <tr>
							<td align='rigth'style='width:20%;'><b></b></td>
							<td align='center'style='width:15%;'><b>Hora de Prueba</b></td>
	                        <td align='center'style='width:20%;'><b>Resultado</b></td>
	                        <td align='center'style='width:20%;'><b>Unidad</b></td>
							<td align='center'style='width:25%;'><b>Observaciones</b></td>
                       	</tr>
                        <tr>
                        	<td align='rigth'style='width:20%;'><b>Velocidad de Subida</b></td>
                        	<td align='center'style='width:15%;'> </td>
                            <td align='center'style='width:20%;'> </td>
                            <td align='center'style='width:20%;'>Mbps </td>
                            <td align='center'style='width:25%;'> </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Velocidad de Bajada</b></td>
                        	<td align='center'style='width:15%;'> </td>
                            <td align='center'style='width:20%;'> </td>
                            <td align='center'style='width:20%;'>Mbps </td>
                            <td align='center'style='width:25%;'> </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Ping 1</b></td>
                        	<td align='center'style='width:15%;'> </td>
                            <td align='center'style='width:20%;'> </td>
                            <td align='center'style='width:20%;'>ms </td>
                            <td align='center'style='width:25%;'> </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Ping 2</b></td>
                        	<td align='center'style='width:15%;'> </td>
                            <td align='center'style='width:20%;'> </td>
                            <td align='center'style='width:20%;'>ms </td>
                            <td align='center'style='width:25%;'>www.mintic.gov.co </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Ping 3</b></td>
                        	<td align='center'style='width:15%;'> </td>
                            <td align='center'style='width:20%;'> </td>
                            <td align='center'style='width:20%;'>ms </td>
                            <td align='center'style='width:25%;'>http://www.louvre.fr/en </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Traceroute</b></td>
                        	<td align='center'style='width:15%;'> </td>
                            <td align='center'style='width:20%;'> </td>
                            <td align='center'style='width:20%;'>estado conexión </td>
                            <td align='center'style='width:25%;'>https://www.wikipedia.org/ </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Traceroute</b></td>
                        	<td align='center'style='width:15%;'> </td>
                            <td align='center'style='width:20%;'> </td>
                            <td align='center'style='width:20%;'>Paso NAP Colombia </td>
                            <td align='center'style='width:25%;'>https://www.sivirtual.gov.co/ </td>
                        </tr>
                    </table>
					<br>
					<b>OBRAS CIVILES</b>
					<table width:100%;>
                        <tr>
							<td align='justify' style='padding: 5px 5px 5px 5px;width:80%;'>Si aplica, el beneficiario certifica, que las obras fueron realizadas en el proceso de instalación por parte del contratista y fueron culminadas satisfactoriamente, sin afectar la infraestructura y la estética del lugar, cumpliendo con las observaciones realizadas durante la instalación.</td>
							<td style='padding: 5px 5px 5px 5px;width:10%;text-align:left;vertical-align:top;'><b>SI</b></td>
	                        <td style='padding: 5px 5px 5px 5px;width:10%;text-align:left;vertical-align:top;'><b>NO</b></td>
                       	</tr>
					</table>
					<br>
					<table width:100%;>
                        <tr>
							<td align='justify' style='padding: 5px 5px 5px 5px;width:100%;'>Yo________________________________________ identificado con cédula de ciudadanía número ___________, como beneficiario del proyecto “Conexiones  Digitales II” – Proyecto Conexiones Digitales redes de acceso última milla para la masificación de accesos de banda ancha en viviendas de interés prioritario, hogares en estratos 1 y 2, – Ministerio de las Tecnologías de la Información y las Comunicaciones, declaro que conozco claramente las condiciones de prestación del servicio de acceso a Internet en banda ancha que adquirí; que la tarifa mensual a pagar por dicho servicio es _____ pesos y que esta condición aplica por un periodo de 15 meses. Igualmente manifiesto que este predio pertenece al estrato ___ y no he contado con el servicio de internet en el mismos en los últimos seis (6) meses. 
								Asimismo me comprometo a informar oportunamente a la Corporación Politécnica Nacional de Colombia. sobre cualquier daño, pérdida o afectación de los equipos antes mencionados.
								Acepta y reconozco que a la fecha he consultado o he sido informado por la Corporación Politécnica Nacional de Colombia sobre las condiciones mínimas requeridas para los equipos necesarios para hacer uso de los servicios contratados. 
								Como constancia de recibo a satisfacción, se firma a los _________ días del mes de ______________ de 2016 en la ciudad de ________________.
							</td>
                       	</tr>
					</table>
					<br>
					<br>
					<table width:100%;>
                        <tr>
							<td align='justify' style='padding: 5px 5px 5px 5px;width:100%;'>Recuerde que cualquier inquietud sobre las funcionalidades del servicio, soporte,  los términos y condiciones, así como las peticiones, quejas o reclamos, serán atendidos en los siguientes canales:
								<br><br>Línea gratuita nacional 018000961016
								<br>Portal Web: http://conexionesdigitales.politecnica.edu.co/.
								<br>Correo: soportecd2@soygenial.co.
								<br><br>En caso de que desee efectuar la devolución de equipos instalados por la Corporación Politécnica Nacional de Colombia para la prestación del servicio, podrá comunicarse a la línea gratuita de atención nacional.
								<br><br>Debe tener en cuenta que existen riesgos sobre la seguridad de la red y de los servicios contratados
								los cuales incluyen: a. Riesgos relacionados con fraudes electrónicos, Riesgos relacionados con la información, Riesgos relacionados con las actividades económicas, Riesgos relacionados con el funcionamiento del Internet y Riesgos relacionados con hábitos adictivos. 
							</td>
                       	</tr>
					</table>
					<br>
					<table width:100%;>
                        <tr>
							<td colspan='2' align='rigth' style='width:50%;'>Recibí a Satisfacción</td>
							<td colspan='2' align='rigth' style='width:50%;'>Responsable de Instalación</td>
                       	</tr>
						<tr>
							<td rowspan='3' align='rigth' style='vertical-align:top;width:25%;color:#c5c5c5;'>" . $firma_beneficiario . "</td>
							<td align='rigth' style='width:25%;color:#c5c5c5;'>Nombre</td>
							<td rowspan='3' align='rigth' style='vertical-align:top;width:25%;color:#c5c5c5;'>Firma</td>
							<td align='rigth' style='width:25%;color:#c5c5c5;'>Nombre</td>
						</tr>
						<tr>
							<td align='rigth' style='width:25%;color:#c5c5c5;'>No. Identificación</td>
							<td align='rigth' style='width:25%;color:#c5c5c5;'>No. Identificación</td>
						</tr>
						<tr>
							<td align='rigth' style='width:25%;color:#c5c5c5;'>Celular</td>
							<td align='rigth' style='width:25%;color:#c5c5c5;'>Celular</td>
						</tr>
					</table>";
		
		$contenidoPagina .= "</page>";
		
		$this->contenidoPagina = $contenidoPagina;
    }
}
$miDocumento = new GenerarDocumento($this->miSql);

?>
