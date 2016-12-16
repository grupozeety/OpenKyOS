<?php
namespace reportes\masivoActas\entidad;

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
    public $rutaDocumento;
    
    public function crearActa($sql, $ruta, $generarActa) {

    	$this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->rutaURL = $ruta;

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        /**
         *  1. Estruturar Documento
         **/

        $this->estruturaDocumento();

        /**
         *  2. Crear PDF
         **/

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
        $this->rutaURL .= '/archivos/actas_entrega_portatil_servicios/';
        $this->rutaAbsoluta .= '/archivos/actas_entrega_portatil_servicios/';
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
        
        $this->contenidoPagina = NULL;

    }

    public function asosicarCodigoDocumento() {

        $this->prefijo = substr(md5(uniqid(time())), 0, 6);
        $cadenaSql = $this->miSql->getCadenaSql('consultarParametro', '900');
        $id_parametro = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
        $tipo_documento = $id_parametro['id_parametro'];
        $descripcion_documento = $id_parametro['id_parametro'] . '_' . $id_parametro['descripcion'];
        $nombre_archivo = "acta_entrega_servicios";
        $this->nombreDocumento = $_REQUEST['interior'] . "_" . $_REQUEST['direccion_domicilio'] . "_" . $_REQUEST['numero_identificacion'] . "_" . $nombre_archivo . '.pdf';
    }

    public function estruturaDocumento() {

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

        $anexo_dir = '';
        
        if ($_REQUEST['manzana'] != '0' && $_REQUEST['manzana'] != '') {
        	$anexo_dir .= " Manzana  #" . $_REQUEST['manzana'] . " - ";
        }
        
        if ($_REQUEST['bloque'] != '0' && $_REQUEST['bloque'] != '') {
        	$anexo_dir .= " Bloque #" . $_REQUEST['bloque'] . " - ";
        }
        
        if ($_REQUEST['torre'] != '0' && $_REQUEST['torre'] != '') {
        	$anexo_dir .= " Torre #" . $_REQUEST['torre'] . " - ";
        }
        
        if ($_REQUEST['casa_apartamento'] != '0' && $_REQUEST['casa_apartamento'] != '') {
        	$anexo_dir .= " Casa/Apartamento #" . $_REQUEST['casa_apartamento'];
        }
        
        if ($_REQUEST['interior'] != '0' && $_REQUEST['interior'] != '') {
        	$anexo_dir .= " Interior #" . $_REQUEST['interior'];
        }
        
        if ($_REQUEST['lote'] != '0' && $_REQUEST['lote'] != '') {
        	$anexo_dir .= " Lote #" . $_REQUEST['lote'];
        }
        
        if ($_REQUEST['piso'] != '0' && $_REQUEST['piso'] != '') {
        	$anexo_dir .= " Piso #" . $_REQUEST['piso'];
        }
        
       $fecha = explode("-", $_REQUEST['fecha_instalacion']);

        $dia = $fecha[0];
        $mes = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        $mes = $mes[$fecha[1]];
        $anno = $fecha[2];
        {

			$tipo_vip = ($_REQUEST['estrato'] == 1) ? "<b>X</b>" : "";
            $tipo_residencial_1 = ($_REQUEST['estrato'] == 2) ? (($_REQUEST['estrato_socioeconomico'] == 1) ? "<b>X</b>" : "") : "";
            $tipo_residencial_2 = ($_REQUEST['estrato'] == 2) ? (($_REQUEST['estrato_socioeconomico'] == 2) ? "<b>X</b>" : "") : "";
        	
        }

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



                        <page backtop='25mm' backbottom='10mm' backleft='10mm' backright='10mm' footer='page'>
                            <page_header>
                                 <table  style='width:100%;' >
                                          <tr>
                                                <td align='center' style='width:100%;border=none;' >
                                                <img src='" . $this->rutaURL . "frontera/css/imagen/logos_contrato.png'  width='500' height='45'>
                                                </td>
                                                <tr>
                                                <td style='width:100%;border:none;text-align:center;font-size:9px;'><b>004/009 ACTA DE ENTREGA DE SERVICIO DE BANDA ANCHA AL USUARIO</b></td>
                                                </tr>
                                                <tr>
                                                <td style='width:100%;border:none;text-align:center;'><br><br><b>004/009 ACTA DE ENTREGA DE SERVICIO DE BANDA ANCHA AL USUARIO</b></td>
                                                </tr>

                                        </tr>
                                    </table>

                        </page_header>
                       ";

        $contenidoPagina .= "
                            <br>
                            El suscrito beneficiario del Proyecto Conexiones Digitales II, cuyos datos se presentan a continuación:
                            <br>
                            <table width:100%;>
                                <tr>
                                    <td style='width:25%;'><b>Contrato de Servicio</b></td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . $_REQUEST['numero_contrato'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Beneficiario</td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . $_REQUEST['nombres'] . " " . $_REQUEST['primer_apellido'] . " " . $_REQUEST['segundo_apellido'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>No de Identificación</td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . number_format($_REQUEST['numero_identificacion'], 0, '', '.') . "</b></td>
                                </tr>
                                <tr>
                                    <td colspan='4'><b>Datos de Vivienda</b></td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Tipo</td>
                                    <td style='width:25%;text-align:center;'>VIP (" . $tipo_vip . ")</td>
                                    <td style='width:25%;text-align:center;'>Estrato 1 (" . $tipo_residencial_1 . ")</td>
                                    <td style='width:25%;text-align:center;'>Estrato 2 (" . $tipo_residencial_2 . ")</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Dirección</td>
                                    <td colspan='3' style='width:75%;text-align:center;'>" . $_REQUEST['direccion_domicilio'] . $anexo_dir . "</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Departamento</td>
                                    <td style='width:25%;text-align:center;'>" . $_REQUEST['departamento'] . "</td>
                                    <td style='width:25%;'>Municipio</td>
                                    <td style='width:25%;text-align:center;'>" . $_REQUEST['municipio'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Urbanización</td>
                                    <td colspan='3' style='width:75%;text-align:center;'>" . $_REQUEST['urbanizacion'] . "</td>
                                </tr>
                            </table>
                            <br>
                            <table  style='width:100%;' >
                                          <tr>
                                                <td align='center' style='width:100%;border=none;' ><b>CERTIFICA:</b></td>

                                        </tr>
                            </table>
                            1. Que ha recibido a satisfacción los equipos y el servicio de acceso de banda ancha con las características descritas a continuación:<br>
        		 	<table width:100%;>
                        <tr>
	                        <td align='center'style='width:16%;'><b>EQUIPO</b></td>
							<td align='center'style='width:18%;'><b>MAC</b></td>
	                        <td align='center'style='width:18%;'><b>SERIAL</b></td>
	                        <td align='center'style='width:16%;'><b>MARCA</b></td>
	                        <td align='center'style='width:16%;'><b>CANT</b></td>
					 		<td align='center'style='width:16%;'><b>IP</b></td>
                       	</tr>
                        <tr>
                        	<td align='center'style='width:16%;'>Esclavo</td>
                            <td align='center'style='width:18%;'>".  $_REQUEST['mac1_esc'] . "<br>" . $_REQUEST['mac2_esc'] . " </td>
                            <td align='center'style='width:18%;'>".  $_REQUEST['serial_esc'] . " </td>
                            <td align='center'style='width:16%;'>".  $_REQUEST['marca_esc'] . " </td>
				 			<td align='center'style='width:16%;'>".  $_REQUEST['cantidad_esc'] . " </td>
							<td align='center'style='width:16%;'>".  $_REQUEST['ip_esc'] . " </td>
                        </tr>
                    </table>
					<br>
					<b>Estado del Servicio</b>
					<table width:100%;>
						<tr>
							<td align='rigth'style='width:20%;'><b>Tipo de Tecnología</b></td>
							<td colspan='4' align='center'style='width:80%;'>" . $_REQUEST['tipo_tecnologia'] . "</td>
						</tr>
						<tr>
							<td align='rigth'style='width:20%;'><b>Hora de la Prueba</b></td>
							<td colspan='4' align='center'style='width:80%;'>" . $_REQUEST['hora_prueba'] . "</td>
						</tr>
                        <tr>
							<td align='rigth'style='width:20%;'><b></b></td>
	                        <td align='center'style='width:30%;'><b>Resultado</b></td>
	                        <td align='center'style='width:20%;'><b>Unidad</b></td>
							<td align='center'style='width:30%;'><b>Observaciones</b></td>
                       	</tr>
                        <tr>
                        	<td align='rigth'style='width:20%;'><b>Velocidad de Subida</b></td>
                            <td align='center'style='width:30%;'>".  $_REQUEST['resultado_vs'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['unidad_vs'] . "</td>
                            <td align='center'style='width:30%;'>".  $_REQUEST['observaciones_vs'] . " </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Velocidad de Bajada</b></td>
                            <td align='center'style='width:30%;'>".  $_REQUEST['resultado_vb'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['unidad_vb'] . " </td>
                            <td align='center'style='width:30%;'>".  $_REQUEST['observaciones_vb'] . " </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Ping 1</b></td>
                            <td align='center'style='width:30%;'>".  $_REQUEST['resultado_p1'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['unidad_p1'] . " </td>
                            <td align='center'style='width:30%;'>".  $_REQUEST['observaciones_p1'] . " </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Ping 2</b></td>
                            <td align='center'style='width:30%;'>".  $_REQUEST['resultado_p2'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['unidad_p2'] . "</td>
                            <td align='center'style='width:30%;'>".  $_REQUEST['observaciones_p2'] . " </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Ping 3</b></td>
                            <td align='center'style='width:30%;'>".  $_REQUEST['resultado_p3'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['unidad_p3'] . " </td>
                            <td align='center'style='width:30%;'>".  $_REQUEST['observaciones_p3'] . "</td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Traceroute</b></td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['resultado_tr1'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['unidad_tr1'] . "</td>
                            <td align='center'style='width:25%;'>".  $_REQUEST['observaciones_tr1'] . "</td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Traceroute</b></td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['resultado_tr2'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['unidad_tr2'] . "</td>
                            <td align='center'style='width:25%;'>".  $_REQUEST['observaciones_tr2'] . "</td>
                        </tr>
                    </table>
                            2. Que las obras civiles realizadas en el proceso de instalación por parte del contratista fueron culminadas satisfactoriamente, sin afectar la infraestructura y la estética del lugar, cumpliendo con las observaciones realizadas durante la instalación.<br><br>
                            3. Que acepta y reconoce que a la fecha ha consultado o ha sido informado por la Corporación Politécnica Nacional de Colombia sobre las condiciones mínimas requeridas de los equipos necesarios para hacer uso de los servicios contratados.<br><br>
                            4. Que se compromete a informar oportunamente a la Corporación Politécnica Nacional de Colombia sobre cualquier daño, pérdida o afectación de los equipos antes mencionados.<br>
                                <br>
                            		<br>
                            Para constancia de lo anterior, firma en la ciudad de " . $_REQUEST['municipio'] . ", municipio de " . $_REQUEST['municipio'] . ", departamento de " . $_REQUEST['departamento'] . ", el día ___________________________" . ".
                            <br>
                            <br>
                            		<br>
                            <table width:100%;>
                                <tr>
                                    <td rowspan='2' align='rigth' style='vertical-align:top;width:50%;'>Firma: <br>&nbsp;
                            		<br>&nbsp;
                            		<br>&nbsp;
                            		<br>&nbsp;
                            		<br>&nbsp;
                            		</td>
                                    <td style='width:50%;text-align:center;'><b>" . $_REQUEST['nombres'] . " " . $_REQUEST['primer_apellido'] . " " . $_REQUEST['segundo_apellido'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:50%;text-align:center;'><b>" . number_format($_REQUEST['numero_identificacion'], 0, '', '.') . "</b></td>
                                </tr>
                            </table>


                    ";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;
        
        unset($contenidoPagina);
        $contenidoPagina = NULL;
        
    }
}


?>
