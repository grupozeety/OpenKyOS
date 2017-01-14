<?php
namespace reportes\generarActasMasivos\entidad;

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
    public $rutaAbsoluta;
    public $nombreContrato;
    public $esteRecursoDB;
    public $esteRecursoDBPR;
    public $clausulas;
    public $beneficiario;
    public $esteRecursoOP;
    public $miSesionSso;
    public $info_usuario;
    public $nombre_contrato;
    public $miProceso;
    public $ruta_archivos;
    public function __construct($sql, $proceso, $ruta_archivos) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miSesionSso = \SesionSso::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->miProceso = $proceso;
        $this->ruta_archivos = $ruta_archivos;

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $esteBloque = $this->miConfigurador->configuracion['esteBloque'];

        if (!isset($esteBloque["grupo"]) || $esteBloque["grupo"] == "") {

            $this->rutaURL .= "/blocks/" . $esteBloque["nombre"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $esteBloque["grupo"] . "/" . $esteBloque["nombre"] . "/";
        }

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDBPR = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $this->nombre = base64_decode($this->miProceso['nombre_archivo']);

        $this->nombre = explode("-", $this->nombre);
        {
            //Acta de Servicios

            /**
             *  1. Información de Beneficiario
             **/

            $this->obtenerInformacionBeneficiarioActaServicios();

            /**
             *  2. Estructuración Documentos
             **/

            /*  foreach ($this->beneficiario_aes as $key => $value) {

            $this->estruturaDocumentoActaServicios($value);
            $this->asosicarNombreArchivo($value, "Acta_Servicios");
            $this->crearPDFActaServicios();

            $this->contenidoPagina = NULL;

            unset($this->nombre_archivo);
            $this->nombreContrato = NULL;
            $value = NULL;

            }*/

            unset($this->beneficiario_aes);
            $this->beneficiario_aes = NULL;

        }

        {
            //Acta de Portatil y Cartel

            /**
             *  1. Información de Beneficiario
             **/

            $this->obtenerInformacionBeneficiarioActaPortatil();

            /**
             *  2. Estructuración Documentos
             **/

            foreach ($this->beneficiario_aes as $key => $value) {

                $this->estruturaDocumentoActaServicios($value);
                $this->asosicarNombreArchivo($value, "Acta_Servicios");
                $this->crearPDFActaServicios();

                $this->contenidoPagina = NULL;

                unset($this->nombre_archivo);
                $this->nombreContrato = NULL;
                $value = NULL;

            }

        }

    }

    public function asosicarNombreArchivo($beneficiario, $nombre = '') {
        $this->nombre_archivo = '';
        foreach ($this->nombre as $key => $value) {

            $this->nombre_archivo .= $beneficiario[$value] . "_";
            $value = NULL;

        }

        $prefijo = substr(md5(uniqid(time())), 0, 6);

        $this->nombre_archivo .= $nombre . "_";

        $this->nombre_archivo = str_replace(".", "_", $this->nombre_archivo);
        $this->nombre_archivo .= $prefijo . ".pdf";

    }

    public function obtenerInformacionBeneficiarioActaPortatil() {
        $arreglo = explode(";", $this->miProceso['datos_adicionales']);

        $arreglo = "'" . implode("','", $arreglo) . "'";

        $cadenaSql = $this->miSql->getCadenaSql('ConsultaBeneficiariosActaPortatil', $arreglo);

        $this->beneficiario_aes = $this->esteRecursoDBPR->ejecutarAcceso($cadenaSql, "busqueda");

    }

    public function obtenerInformacionBeneficiarioActaServicios() {
        $arreglo = explode(";", $this->miProceso['datos_adicionales']);

        $arreglo = "'" . implode("','", $arreglo) . "'";

        $cadenaSql = $this->miSql->getCadenaSql('ConsultaBeneficiariosActaServicio', $arreglo);

        $this->beneficiario_aes = $this->esteRecursoDBPR->ejecutarAcceso($cadenaSql, "busqueda");

    }

    public function crearPDFActaServicios() {
        ob_start();
        $html2pdf = new \HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(
            2,
            2,
            2,
            10,
        ));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output($this->ruta_archivos . "/" . $this->nombre_archivo, 'F');

    }
    public function estruturaDocumentoActaServicios($beneficiario) {

        $anexo_dir = '';

        if ($beneficiario['manzana'] != '0' && $beneficiario['manzana'] != '') {
            $anexo_dir .= " Manzana  #" . $beneficiario['manzana'] . " - ";
        }

        if ($beneficiario['bloque'] != '0' && $beneficiario['bloque'] != '') {
            $anexo_dir .= " Bloque #" . $beneficiario['bloque'] . " - ";
        }

        if ($beneficiario['torre'] != '0' && $beneficiario['torre'] != '') {
            $anexo_dir .= " Torre #" . $beneficiario['torre'] . " - ";
        }

        if ($beneficiario['casa_apartamento'] != '0' && $beneficiario['casa_apartamento'] != '') {
            $anexo_dir .= " Casa/Apartamento #" . $beneficiario['casa_apartamento'];
        }

        if ($beneficiario['interior'] != '0' && $beneficiario['interior'] != '') {
            $anexo_dir .= " Interior #" . $beneficiario['interior'];
        }

        if ($beneficiario['lote'] != '0' && $beneficiario['lote'] != '') {
            $anexo_dir .= " Lote #" . $beneficiario['lote'];
        }

        if ($beneficiario['piso'] != '0' && $beneficiario['piso'] != '') {
            $anexo_dir .= " Piso #" . $beneficiario['piso'];
        }

        if (!is_null($beneficiario['barrio']) && $beneficiario['barrio'] != '') {
            $anexo_dir .= " Barrio " . $beneficiario['barrio'];
        }

        {

            $tipo_vip = ($beneficiario['estrato'] == 1) ? "<b>X</b>" : "";
            $tipo_residencial_1 = ($beneficiario['estrato'] == 2) ? (($beneficiario['estrato_socioeconomico'] == 1) ? "<b>X</b>" : "") : "";
            $tipo_residencial_2 = ($beneficiario['estrato'] == 2) ? (($beneficiario['estrato_socioeconomico'] == 2) ? "<b>X</b>" : "") : "";

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
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . $beneficiario['numero_contrato'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Beneficiario</td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . $beneficiario['nombres'] . " " . $beneficiario['primer_apellido'] . " " . $beneficiario['segundo_apellido'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>No de Identificación</td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . number_format($beneficiario['numero_identificacion'], 0, '', '.') . "</b></td>
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
                                    <td colspan='3' style='width:75%;text-align:center;'>" . $beneficiario['direccion_domicilio'] . $anexo_dir . "</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Departamento</td>
                                    <td style='width:25%;text-align:center;'>" . $beneficiario['departamento'] . "</td>
                                    <td style='width:25%;'>Municipio</td>
                                    <td style='width:25%;text-align:center;'>" . $beneficiario['municipio'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Urbanización</td>
                                    <td colspan='3' style='width:75%;text-align:center;'>" . $beneficiario['urbanizacion'] . "</td>
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
                            <td align='center'style='width:18%;'>" . $beneficiario['mac1_esc'] . "<br>" . $beneficiario['mac2_esc'] . " </td>
                            <td align='center'style='width:18%;'>" . $beneficiario['serial_esc'] . " </td>
                            <td align='center'style='width:16%;'>" . $beneficiario['marca_esc'] . " </td>
                            <td align='center'style='width:16%;'>" . $beneficiario['cantidad_esc'] . " </td>
                            <td align='center'style='width:16%;'>" . $beneficiario['ip_esc'] . " </td>
                        </tr>
                    </table>
                    <br>
                    <b>Estado del Servicio</b>
                    <table width:100%;>
                        <tr>
                            <td align='rigth'style='width:20%;'><b>Tipo de Tecnología</b></td>
                            <td colspan='4' align='center'style='width:80%;'>" . $beneficiario['tipo_tecnologia'] . "</td>
                        </tr>
                        <tr>
                            <td align='rigth'style='width:20%;'><b>Hora de la Prueba</b></td>
                            <td colspan='4' align='center'style='width:80%;'>" . $beneficiario['hora_prueba'] . "</td>
                        </tr>
                        <tr>
                            <td align='rigth'style='width:20%;'><b></b></td>
                            <td align='center'style='width:30%;'><b>Resultado</b></td>
                            <td align='center'style='width:20%;'><b>Unidad</b></td>
                            <td align='center'style='width:30%;'><b>Observaciones</b></td>
                        </tr>
                        <tr>
                            <td align='rigth'style='width:20%;'><b>Velocidad de Subida</b></td>
                            <td align='center'style='width:30%;'>" . $beneficiario['resultado_vs'] . " </td>
                            <td align='center'style='width:20%;'>" . $beneficiario['unidad_vs'] . "</td>
                            <td align='center'style='width:30%;'>" . $beneficiario['observaciones_vs'] . " </td>
                        </tr>
                        <tr>
                            <td align='rigth'style='width:20%;'><b>Velocidad de Bajada</b></td>
                            <td align='center'style='width:30%;'>" . $beneficiario['resultado_vb'] . " </td>
                            <td align='center'style='width:20%;'>" . $beneficiario['unidad_vb'] . " </td>
                            <td align='center'style='width:30%;'>" . $beneficiario['observaciones_vb'] . " </td>
                        </tr>
                        <tr>
                            <td align='rigth'style='width:20%;'><b>Ping 1</b></td>
                            <td align='center'style='width:30%;'>" . $beneficiario['resultado_p1'] . " </td>
                            <td align='center'style='width:20%;'>" . $beneficiario['unidad_p1'] . " </td>
                            <td align='center'style='width:30%;'>" . $beneficiario['observaciones_p1'] . " </td>
                        </tr>
                        <tr>
                            <td align='rigth'style='width:20%;'><b>Ping 2</b></td>
                            <td align='center'style='width:30%;'>" . $beneficiario['resultado_p2'] . " </td>
                            <td align='center'style='width:20%;'>" . $beneficiario['unidad_p2'] . "</td>
                            <td align='center'style='width:30%;'>" . $beneficiario['observaciones_p2'] . " </td>
                        </tr>
                        <tr>
                            <td align='rigth'style='width:20%;'><b>Ping 3</b></td>
                            <td align='center'style='width:30%;'>" . $beneficiario['resultado_p3'] . " </td>
                            <td align='center'style='width:20%;'>" . $beneficiario['unidad_p3'] . " </td>
                            <td align='center'style='width:30%;'>" . $beneficiario['observaciones_p3'] . "</td>
                        </tr>
                        <tr>
                            <td align='rigth'style='width:20%;'><b>Traceroute</b></td>
                            <td align='center'style='width:20%;'>" . $beneficiario['resultado_tr1'] . " </td>
                            <td align='center'style='width:20%;'>" . $beneficiario['unidad_tr1'] . "</td>
                            <td align='center'style='width:25%;'>" . $beneficiario['observaciones_tr1'] . "</td>
                        </tr>
                        <tr>
                            <td align='rigth'style='width:20%;'><b>Traceroute</b></td>
                            <td align='center'style='width:20%;'>" . $beneficiario['resultado_tr2'] . " </td>
                            <td align='center'style='width:20%;'>" . $beneficiario['unidad_tr2'] . "</td>
                            <td align='center'style='width:25%;'>" . $beneficiario['observaciones_tr2'] . "</td>
                        </tr>
                    </table>
                            2. Que las obras civiles realizadas en el proceso de instalación por parte del contratista fueron culminadas satisfactoriamente, sin afectar la infraestructura y la estética del lugar, cumpliendo con las observaciones realizadas durante la instalación.<br><br>
                            3. Que acepta y reconoce que a la fecha ha consultado o ha sido informado por la Corporación Politécnica Nacional de Colombia sobre las condiciones mínimas requeridas de los equipos necesarios para hacer uso de los servicios contratados.<br><br>
                            4. Que se compromete a informar oportunamente a la Corporación Politécnica Nacional de Colombia sobre cualquier daño, pérdida o afectación de los equipos antes mencionados.<br>
                                <br>
                                    <br>
                            Para constancia de lo anterior, firma en la ciudad de " . $beneficiario['municipio'] . ", municipio de " . $beneficiario['municipio'] . ", departamento de " . $beneficiario['departamento'] . ", el día ___________________________" . ".
                            <br>
                            <br>
                                    <br>
                                    <br>
                            <table width:100%;>
                                <tr>
                                    <td rowspan='2' align='rigth' style='vertical-align:top;width:50%;'>Firma: <br>&nbsp;
                                    <br>&nbsp;
                                    <br>&nbsp;

                                    </td>
                                    <td style='width:50%;text-align:center;'><b>" . $beneficiario['nombres'] . " " . $beneficiario['primer_apellido'] . " " . $beneficiario['segundo_apellido'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:50%;text-align:center;'><b>" . number_format($beneficiario['numero_identificacion'], 0, '', '.') . "</b></td>
                                </tr>
                            </table>


                    ";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;

    }
}
$miDocumento = new GenerarDocumento($this->miSql, $this->proceso, $this->rutaAbsoluta_archivos);

?>
