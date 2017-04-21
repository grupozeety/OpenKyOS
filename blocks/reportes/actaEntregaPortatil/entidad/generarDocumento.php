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

        if (!is_null($this->infoCertificado['fecha_entrega']) && $this->infoCertificado['fecha_entrega'] != '') {

            $fecha = explode("-", $this->infoCertificado['fecha_entrega']);
            $dia = $fecha[0];
            $mes = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            $mes = $mes[$fecha[1]];
            $anno = $fecha[2];
            $fecha_letra = $dia . " de " . $mes . " de " . $anno . ".";

            $_REQUEST['fecha_entrega'] = $this->infoCertificado['fecha_entrega'];

        } else {

            $fecha_letra = "__________________________________________.";

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

        $contenidoPagina .= "
                            <br>
                            <br>
                            El suscrito beneficiario del Proyecto Conexiones Digitales II, cuyos datos se presentan a continuación:
                            <br>
                            <br>
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
                                   <table style='width:100%;border;none;'>
                                        <tr>
                                            <td align='left'  style='width:48%;border:none;'>

                                            </td>
                                            <td align='center'  style='width:4%;border:none;'>
                                            </td>
                                            <td align='rigth' style='width:48%;border:none;'>
                                            </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <br>
                                    <table width:100%;>
                                        <tr>
                                            <td align='rigth'  style=' width:20%;'><b>Marca</b></td>
                                            <td align='rigth' style='width:30%;'>" . $_REQUEST['marca'] . "</td>
                                            <td align='rigth' style=' width:20%;'><b>Modelo</b></td>
                                            <td align='rigth' style='width:30%;'>" . $_REQUEST['modelo'] . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Serial</b></td>
                                            <td align='rigth' style='width:30%;'>" . $_REQUEST['serial'] . "</td>
                                            <td align='rigth' style=' width:20%;'>Procesador</td>
                                            <td align='rigth' style='width:30%;'>" . $_REQUEST['procesador'] . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Memoria RAM</b></td>
                                            <td align='rigth' style='width:30%;'>" . $_REQUEST['memoria_ram'] . "</td>
                                            <td align='rigth' style=' width:20%;'><b>Disco Duro</b></td>
                                            <td align='rigth' style='width:30%;'>" . $_REQUEST['disco_duro'] . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Sistema Operativo</b></td>
                                            <td align='rigth' style='width:30%;'>" . $_REQUEST['sistema_operativo'] . "</td>
                                            <td align='rigth' style=' width:20%;'><b>Cámara</b></td>
                                            <td align='rigth' style='width:30%;'>" . $_REQUEST['camara'] . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Audio</b></td>
                                            <td align='rigth' style='width:30%;'>" . $_REQUEST['audio'] . "</td>
                                            <td align='rigth' style=' width:20%;'><b>Batería</b></td>
                                            <td align='rigth' style='width:30%;'>" . $_REQUEST['bateria'] . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Tarjeta de Red (Alámbrica)</b></td>
                                            <td align='rigth' style='width:30%;'>" . $_REQUEST['targeta_red_alambrica'] . "</td>
                                            <td align='rigth' style=' width:20%;'><b>Tarjeta de Red (Inalámbrica)</b></td>
                                            <td align='rigth' style='width:30%;'>" . $_REQUEST['targeta_red_inalambrica'] . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Cargador</b></td>
                                            <td align='rigth' style='width:30%;'>" . $_REQUEST['cargador'] . "</td>
                                            <td align='rigth' style=' width:20%;'><b>Pantalla</b></td>
                                            <td align='rigth' style='width:30%;'>" . $_REQUEST['pantalla'] . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth'  style=' width:20%;'><b>Sitio web de soporte</b></td>
                                            <td align='rigth' colspan='3' style='width:80%;'>" . $_REQUEST['web_soporte'] . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth'  style=' width:20%;'><b>Teléfono de soporte</b></td>
                                            <td align='rigth' colspan='3' style='width:80%;'>" . $_REQUEST['telefono_soporte'] . "</td>
                                        </tr>
                                    </table>
                                    <br>
                            2. Que el computador recibido no presenta rayones, roturas, hendiduras o elementos sueltos.<br><br>
                            3. Que entiende que el computador recibido no tiene costo adicional y se encuentra incorporado al contrato de servicio suscrito con la Corporación Politécnica Nacional de Colombia.<br><br>
                            4. Que se compromete a velar por la seguridad del equipo y a cuidarlo para mantener su capacidad de uso y goce en el marco del contrato de servicio suscrito con la Corporación Politécnica Nacional de Colombia.<br>
                                <br>
                            5. Que se compromete a participar en por lo menos 20 horas de  capacitación sobre el manejo del equipo y/o aplicativos de uso productivo de esta herramienta como parte del proceso de apropiación social contemplado en el Anexo Técnico del proyecto Conexiones Digitales II<br><br><br>

                            Para constancia de lo anterior, firma en la ciudad de " . $_REQUEST['municipio'] . ", municipio de " . $_REQUEST['municipio'] . ", departamento de " . $_REQUEST['departamento'] . ", el día " . $fecha_letra . "
                            <br>
                            <br>
                            <br>
                            <br>

                            <table width:100%;>
                                <tr>
                                    <td rowspan='2' style='width:50%;'>Firma: </td>
                                    <td style='width:50%;text-align:center;'><b>" . $_REQUEST['nombre_contrato'] . " " . $_REQUEST['primer_apellido_contrato'] . " " . $_REQUEST['segundo_apellido_contrato'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:50%;text-align:center;'><b>" . number_format($_REQUEST['numero_identificacion_contrato'], 0, '', '.') . "</b></td>
                                </tr>
                            </table>


                    ";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;
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
