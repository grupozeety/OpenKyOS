<?php

namespace reportes\actaEntregaPortatil\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

include $ruta . "/plugin/html2pdf/html2pdf.class.php";
class GenerarDocumento
{
    public $miConfigurador;
    public $elementos;
    public $miSql;
    public $conexion;
    public $contenidoPagina;
    public $rutaURL;
    public $esteRecursoDB;
    public $clausulas;
    public $beneficiario;

    public $rutaAbsoluta;
    public function __construct($sql)
    {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        // Conexion a Base de Datos
        $conexion = "interoperacion";

        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

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
         * 1.
         * Estruturar Documento
         */

        $this->estruturaDocumento();

        /**
         * 2.
         * Crear PDF
         */
        $this->crearPDF();
    }
    public function crearPDF()
    {
        ob_start();
        $html2pdf = new \HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(
            2,
            2,
            2,
            10,
        ));

        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output('Acta_Entrega_servicio_CC_' . $this->infoCertificado['identificacion'] . '_' . date('Y-m-d') . '.pdf', 'D');
    }
    public function estruturaDocumento()
    {

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionCertificado');
        $infoCertificado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

        $this->infoCertificado = $infoCertificado;

        $_REQUEST = array_merge($_REQUEST, $infoCertificado);

        if (is_null($_REQUEST['serial'])) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionEquipoSerial', $_REQUEST['serial']);
            $this->infoPortatil = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            foreach ($this->infoPortatil as $key => $value) {
                $this->infoPortatil[$key] = trim($value);
            }

        } else {
            $this->informacionEstandarPortatil();
        }

        if (!is_null($this->infoCertificado['fecha_entrega']) && $this->infoCertificado['fecha_entrega'] != '') {

            $fecha = explode("-", $this->infoCertificado['fecha_entrega']);
            $dia = $fecha[0];
            $mes = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            $mes = $mes[$fecha[1]];
            $anno = $fecha[2];
            $fecha_letra = $dia . " del mes de " . $mes . " del Año " . $anno;

            $_REQUEST['fecha_entrega'] = $this->infoCertificado['fecha_entrega'];

        } else {

            $fecha_letra = "_________ del mes de _________ del Año _________";

            $_REQUEST['fecha_entrega'] = '';

        }

        {

            $tipo_vip = ($_REQUEST['tipo_beneficiario_contrato'] == "1") ? "<b>X</b>" : "";
            $tipo_residencial_1 = ($_REQUEST['tipo_beneficiario_contrato'] == "2") ? (($_REQUEST['estrato_socioeconomico_contrato'] == "1") ? "<b>X</b>" : "") : "";
            $tipo_residencial_2 = ($_REQUEST['tipo_beneficiario_contrato'] == "2") ? (($_REQUEST['estrato_socioeconomico_contrato'] == "2") ? "<b>X</b>" : "") : "";
        }

        setlocale(LC_ALL, "es_CO.UTF-8");

        {

            $anexo_dir = '';

            if ($this->infoCertificado['manzana'] != '0' && $this->infoCertificado['manzana'] != '') {
                $anexo_dir .= " Manzana  #" . $this->infoCertificado['manzana'] . " - ";
            }

            if ($this->infoCertificado['bloque'] != '0' && $this->infoCertificado['bloque'] != '') {
                $anexo_dir .= " Bloque #" . $this->infoCertificado['bloque'] . " - ";
            }

            if ($this->infoCertificado['torre'] != '0' && $this->infoCertificado['torre'] != '') {
                $anexo_dir .= " Torre #" . $this->infoCertificado['torre'] . " - ";
            }

            if ($this->infoCertificado['casa_apartamento'] != '0' && $this->infoCertificado['casa_apartamento'] != '') {
                $anexo_dir .= " Casa/Apartamento #" . $this->infoCertificado['casa_apartamento'];
            }

            if ($this->infoCertificado['interior'] != '0' && $this->infoCertificado['interior'] != '') {
                $anexo_dir .= " Interior #" . $this->infoCertificado['interior'];
            }

            if ($this->infoCertificado['lote'] != '0' && $this->infoCertificado['lote'] != '') {
                $anexo_dir .= " Lote #" . $this->infoCertificado['lote'];
            }

            if ($this->infoCertificado['piso'] != '0' && $this->infoCertificado['piso'] != '') {
                $anexo_dir .= " Piso #" . $this->infoCertificado['piso'];
            }

        }

        // Caraterizacioón Codigo Departamento

        if ($_REQUEST['codigo_departamento'] == '23') {

            $departamento_cordoba = 'X';
            $departamento_sucre = '';

        } elseif ($_REQUEST['codigo_departamento'] == '70') {

            $departamento_cordoba = ' ';
            $departamento_sucre = 'X';

        } else {
            $departamento_cordoba = ' ';
            $departamento_sucre = ' ';

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
                                page{
                                    font-size:9px;

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
                                                <td style='width:100%;border:none;text-align:center;font-size:9px;'><b>008 - ACTA DE ENTREGA DE COMPUTADOR PORTÁTIL</b></td>
                                                </tr>
                                                <tr>
                                                <td style='width:100%;border:none;text-align:center;'><br><br><b>008 - ACTA DE ENTREGA DE COMPUTADOR PORTÁTIL</b></td>
                                                </tr>

                                        </tr>
                                    </table>

                        </page_header>
                       ";

        $contenidoPagina .= "<p>El suscrito beneficiario del Proyecto Conexiones Digitales II, cuyos datos se presentan a continuación:</p>
                            <table width:100%;>
                                <tr>
                                    <td style='width:20%;'><b>Nombres y Apellidos</b></td>
                                    <td style='width:35%;'><b>" . $_REQUEST['nombre_contrato'] . " " . $_REQUEST['primer_apellido_contrato'] . " " . $_REQUEST['segundo_apellido_contrato'] . "</b></td>
                                    <td style='width:15%;'><b>Fecha Entrega</b></td>
                                    <td colspan='2' style='width:30%;'>" . $_REQUEST['fecha_entrega'] . "</td>
                                </tr>

                                <tr>
                                    <td style='width:20%;'><b>Dirección</b></td>
                                    <td style='width:35%;'>" . $_REQUEST['direccion'] . " " . $anexo_dir . "</td>
                                    <td style='width:15%;'><b>Cedula</b></td>
                                    <td colspan='2' style='width:30%;'><b>" . number_format($_REQUEST['numero_identificacion_contrato'], 0, '', '.') . "</b></td>
                                </tr>
                                <tr>
                                    <td rowspan='2' style='width:20%;'><b>Urbanización</b></td>
                                    <td rowspan='2' style='width:35%;'>" . $this->limpiar_caracteres_especiales($_REQUEST['nombre_urbanizacion']) . "</td>
                                    <td style='width:15%;'><b>Municipio</b></td>
                                    <td colspan='2' style='width:30%;'>" . $_REQUEST['municipio'] . "</td>
                                </tr>

                                <tr>
                                    <td style='width:15%;'><b>Departamento</b></td>
                                    <td style='width:15%;'>CORDOBA(<b>" . $departamento_cordoba . "</b>)</td>
                                    <td style='width:15%;'>SUCRE(<b>" . $departamento_sucre . "</b>)</td>
                                </tr>
                            </table>
                            <p style='text-align:justify'>
                            El contratista entrega un computador portátil Marca HP 245 G4 Notebook PC nuevo, a título de uso, goce y disfrute hasta la terminación del contrato de aporte suscrito entre el Fondo TIC y Corporación Politécnica. En consecuencia, el computador no puede ser vendido, arrendado, trasferido, dado en prenda, servir de garantía, so pena de perder el beneficio. Tal como mis datos aparecen en la parte superior de este formato, confirmo que, recibí en comodato el computador portátil con las siguientes características:
                            </p>
                                   <table style='width:100%;border;none;font-size:90%'>
                                        <tr>
                                            <td align='left'  style='width:49%;border:none;'>
                                               <table style='width:100%;border;0.1px;'>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'><b>HARDWARE/SOFTWARE</b></td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'><b>EXIGIDO</b></td>
                                                        <td align='center'  style='width:15%;border:0.1px;'><b>CUMPLE<br>(SI/NO)</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>¿El Operador realizó la Entrega del Computador?</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Serial: <b>" . $this->infoPortatil['serial'] . "</b></td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Procesador</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['procesador'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Arquitectura</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['arquitectura'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Memoria RAM</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['memoria_ram'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Compatibilidad</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['compatibilidad_memoria_ram'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Tecnologia</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['tecnologia_memoria_ram'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>ANTIVIRUS</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['antivirus'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Disco duro protegido contrao impacto</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['disco_anti_impacto'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Monitor</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['pantalla'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Teclado</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['teclado'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                </table>
                                           </td>
                                           <td align='center'  style='width:2%;border:none;'>
                                            </td>
                                            <td align='rigth' style='width:49%;border:none;'>
                                                <table style='width:100%;border;0.1px;'>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'><b>HARDWARE/SOFTWARE</b></td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'><b>EXIGIDO</b></td>
                                                        <td align='center'  style='width:15%;border:0.1px;'><b>CUMPLE<br>(SI/NO)</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Baterías</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['bateria_tipo'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Fuente Alimentación</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['cargador'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Salida Video</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['salida_video'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Tarjeta Memoria</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['targeta_memoria'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Rango Voltaje<br>Frecuencia</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['voltaje'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Puerto USB</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['puerto_usb'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Autonomía</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['autonomia'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Disco Duro</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['disco_duro'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Targeta de audio, micrófono y parlantes</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['targeta_audio_video'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Software</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['sistema_operativo'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <table style='width:100%;border;0.1px;font-size:90%'>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'><b>HARDWARE/SOFTWARE</b></td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'><b>EXIGIDO</b></td>
                                                        <td align='center'  style='width:15%;border:0.1px;'><b>CUMPLE<br>(SI/NO)</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Dispositivo Apuntador</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['mouse_tipo'] . ", con botones equivalentes a “mouse” estándar y dispositivo de desplazamiento vertical en pantalla (“Scroll”) </td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Cámara</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>" . $this->infoPortatil['camara'] . "</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Conectividad a Red (Alámbrica)</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>WiFi Integrada, Estándar IEEE 802.11 b/g/n, Encriptación WEP 64/128,Compatibilidad IPV4 / IPV6, Bluetooth 4.0</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Conectividad Inalámabrica</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>WiFi Integrada, Estándar IEEE 802.11 b/g/n, Encriptación WEP 64/128,Compatibilidad IPV4 / IPV6, Bluetooth 4.0</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Otro</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Opción de Activación / Desactivación desde teclado por tecla o combinación de teclas o desde funcionalidad directa externa. Ajuste automático de potencia</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                    <td align='center'  style='width:42.5%;border:0.1px;'>El software y el hardware del Equipo funciona correctamente</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>SI</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>La Carcaza del PC o portátil se encuentra personalizada con los logos del ministerio</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>SI</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>Estado del Regulador que alimenta el Equipo</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>SI</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>El equipo se entregó embalado, garantizando la integridad del mismo</td>
                                                        <td align='center'  style='width:42.5%;border:0.1px;'>SI</td>
                                                        <td align='center'  style='width:15%;border:0.1px;'>SI</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan='3' align='center'  style='width:100%;border:0.1px;'>Tener en cuenta las consideraciones generales, condiciones del software y personalización, garantía y condiciones de entrega</td>
                                                    </tr>
                                                </table>
                                    <p style='text-align:justify'>Respecto al computador portátil descrito se deja constancia que para la entrega no se me cobró ningún tipo de cargo como usuario beneficiado del Proyecto Conexiones Digitales II y que el equipo fue entregado embalado, garantizando la integridad del mismo. Además certifico que se realizaron las siguientes pruebas de funcionalidad:</p>
                                    <table align='center' style='width:85%;border:0.1px;'>
                                            <tr>
                                                <td align='center'  style='width:40%;border:0.1px;'>Correcto encendido/apagado</td>
                                                <td align='center'  style='width:10%;border:0.1px;'>SI( ) NO( )</td>
                                                <td align='center'  style='width:40%;border:0.1px;'>Equipo funcionando y navegando</td>
                                                <td align='center'  style='width:10%;border:0.1px;'>SI( ) NO( )</td>
                                            </tr>
                                            <tr>
                                                <td align='center' colspan='3' style='width:90%;border:0.1px;'>Funcionamiento de los periféricos, (teclado, parlante, touchpad)</td>
                                                <td align='center'  style='width:10%;border:0.1px;'>SI( ) NO( )</td>
                                            </tr>
                                    </table>
                                    <p style='text-align:justify'>A continuación, se detalla información importante en caso tal que se requiera soporte técnico para el equipo portátil entregado:</p>
                                    <table align='center' style='width:100%;border:0.1px;'>
                                            <tr>
                                                <td align='center'  style='width:50%;border:0.1px;'>Garantía del equipo: Un año a partir de la fecha en que se firma la recepción</td>
                                                <td align='center'  style='width:50%;border:0.1px;'>Mantenimiento Preventivo: 1 visitas por año</td>
                                            </tr>
                                            <tr>
                                                <td align='center'  style='width:50%;border:0.1px;'>Contacto Garantía: Corporación Politécnica</td>
                                                <td align='center'  style='width:50%;border:0.1px;'>Teléfono: 018000 961 016</td>
                                            </tr>
                                    </table>
                                    <p style='text-align:justify'>Advertencia: Con el fin de no perder la garantía del fabricante en la eventualidad de presentarse fallas, el beneficiario ni un tercero no autorizado por el fabricante, puede manipular el equipo tratando de resolver el problema presentado.En caso de daño, hurto, el usuario de hacer el reporte a la mesa de ayuda al número 018000 961016, lo cual debe quedar consignado en
un ticket para la gestión y seguimiento del mismo. En caso de hurto o pérdida no habrá reposición del equipo.Luego de la verificación de funcionamiento pleno del computador portátil y de sus características y accesorios, manifiesto mi entera
conformidad y satisfacción del bien que recibo en la fecha, y me obligo a realizar su correcto uso, custodia y conservación, autorizando al prestador del servicio Corporación Politécnica a, para que ejerza el seguimiento y control sobre el adecuado y correcto uso, custodia y conservación del mismo.<br>A la terminación del plazo de ejecución de este contrato de comodato, tendré la opción de adquirir el bien antes descrito entregado en comodato. Para constancia de lo anterior, firmo con copia de mi documento de identidad hoy día " . $fecha_letra . ". En el municipio de <b>" . $_REQUEST['municipio'] . "</b>, Departamento <b>" . $_REQUEST['departamento'] . "</b>.</p>

                            <table width:100%;>
                             <tr>
                                <td align='center' colspan='2' style='width:45%;border:none;'>________________________________</td>
                                <td align='center'  style='width:10%;border:none;'> </td>
                                <td align='center' colspan='2' style='width:45%;border:none;'>________________________________</td>
                             </tr>
                             <tr>
                                <td align='center' colspan='2' style='width:45%;border:none;'>Firma Beneficiario</td>
                                <td align='center'  style='width:10%;border:none;'> </td>
                                <td align='center' colspan='2' style='width:45%;border:none;'>Firma Representante<br>Operador</td>
                             </tr>
                             <tr>
                                <td align='center'  style='width:10%;border:none;'>Nombre</td>
                                <td align='center'  style='width:35%;border:none;font-size:6px'><b>" . $_REQUEST['nombre_contrato'] . " " . $_REQUEST['primer_apellido_contrato'] . " " . $_REQUEST['segundo_apellido_contrato'] . "</b></td>
                                <td align='center'  style='width:10%;border:none;'> </td>
                                <td align='center'  style='width:10%;border:none;'>Nombre</td>
                                <td align='center'  style='width:35%;border:none;'>_________________________</td>
                             </tr>
                             <tr>
                                <td align='center'  style='width:10%;border:none;'>Cedula</td>
                                <td align='center'  style='width:35%;border:none;font-size:6px'><b>" . $_REQUEST['numero_identificacion_contrato'] . "</b></td>
                                <td align='center'  style='width:10%;border:none;'> </td>
                                <td align='center'  style='width:10%;border:none;'>Cedula</td>
                                <td align='center'  style='width:35%;border:none;'>_________________________</td>
                             </tr>
                            </table>


                    ";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;
    }

    public function informacionEstandarPortatil()
    {

        $this->infoPortatil = array(

            'camara' => 'Integrada 720 px HD Grabación, Video y Fotografía',

            'mouse_tipo' => 'Touchpad con capacidad multi-touch',
            'sistema_operativo' => 'Ubuntu',

            'targeta_audio_video' => 'Incorporados',

            'disco_duro' => '500 GB velocidad de 5.400 rpm',

            'autonomia' => 'Mín. Cuatro horas – 6 celdas',

            'puerto_usb' => '(2)Usb 2.0 y (3) Ubs 3.0',

            'voltaje' => '100 v a 120 v - 50 Hz a 60 Hz',

            'targeta_memoria' => 'multi-format digital media reader(soporta SD, SDHC, SDXC)',

            'salida_video' => 'VGA 1 y HMDI 1',

            'cargador' => 'Adaptador Smart AC 100 v a 120 v',

            'bateria_tipo' => 'Recargable Lithium Ion',

            'teclado' => 'Español(Internacional)',

            'marca' => 'Hewlett Packard',

            'modelo' => 'HP 245 G4 Notebook PC',

            'procesador' => 'AMD A8-7410 2200 MHz cores 2.2 GHz',

            'arquitectura' => '64 Bits',

            'memoria_ram' => 'DDR3 4096 MB',

            'compatibilidad_memoria_ram' => 'PAE, NX, y SSE 4.x',

            'tecnologia_memoria_ram' => 'DDR3',

            'antivirus' => 'Clamav Antivirus',

            'disco_anti_impacto' => 'N/A',

            'serial' => '',

            'audio' => 'Integrado Mono/Estereo',

            'bateria' => '41610 mWh',

            'targeta_red_alambrica' => 'Integrada',

            'targeta_red_inalambrica' => 'Integrada',

            'pantalla' => 'HD SVA anti-brillo LED14"',

        );

    }

    public function limpiar_caracteres_especiales($s)
    {
        $s = ereg_replace("[áàâãª]", "a", $s);
        $s = ereg_replace("[ÁÀÂÃ]", "A", $s);
        $s = ereg_replace("[éèê]", "e", $s);
        $s = ereg_replace("[ÉÈÊ]", "E", $s);
        $s = ereg_replace("[íìî]", "i", $s);
        $s = ereg_replace("[ÍÌÎ]", "I", $s);
        $s = ereg_replace("[óòôõº]", "o", $s);
        $s = ereg_replace("[ÓÒÔÕ]", "O", $s);
        $s = ereg_replace("[úùû]", "u", $s);
        $s = ereg_replace("[ÚÙÛ]", "U", $s);
        $s = str_replace("ñ", "n", $s);
        $s = str_replace("Ñ", "N", $s);
        //para ampliar los caracteres a reemplazar agregar lineas de este tipo:
        //$s = str_replace("caracter-que-queremos-cambiar","caracter-por-el-cual-lo-vamos-a-cambiar",$s);
        $s = str_replace("urbanizacion", "", strtolower($s));

        return trim(strtoupper($s));
    }
}
$miDocumento = new GenerarDocumento($this->sql);
