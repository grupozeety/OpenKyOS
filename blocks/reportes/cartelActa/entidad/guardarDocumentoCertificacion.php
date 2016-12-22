<?php

namespace reportes\cartelActa\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

include $ruta . "/plugin/html2pdf/html2pdf.class.php";
require_once 'sincronizar.php';
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
    public function __construct($sql, $lenguaje) {
        ini_set('memory_limit', '650M');
        ini_set('max_execution_time', 100000);
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->lenguaje = $lenguaje;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        $this->sincronizacion = new Sincronizar($lenguaje, $sql);

        // Conexion a Base de Datos
        $conexion = "produccion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        $this->rutaURLArchivo = $this->rutaURL . '/archivos/actas_entrega_portatil/';

        $this->rutaAbsolutaArchivo = $this->rutaAbsoluta . '/archivos/actas_entrega_portatil/';

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }

        $this->rutaURL_Bloque = $this->rutaURL;
        $this->rutaAbsoluta_Bloque = $this->rutaAbsoluta;

        $this->obtenerInformacionBeneficiario();
        //var_dump($this->beneficiario);EXIT;
        foreach ($this->beneficiario as $key => $value) {

            $this->asosicarCodigoDocumento($value);

            $this->estruturaDocumento($value);
            $this->crearPDF();

            unset($this->contenidoPagina);
            $this->contenidoPagina = NULL;

            $this->estruturaDocumentoCartel($value);
            $this->crearPDFCartel();

            unset($this->contenidoPagina);
            $this->contenidoPagina = NULL;

            $archivo_datos = array(
                'ruta_archivo' => $this->rutaURLArchivo . $this->nombreDocumento,
                'rutaabsoluta' => $this->rutaAbsolutaArchivo . $this->nombreDocumento,
                'nombre_archivo' => $this->nombreDocumento,
                'campo' => " ",
                'tipo_documento' => '131',
            );
            //var_dump($archivo_datos);exit;
            //$this->sincronizacion->sincronizarAlfresco($value['id_beneficiario'], $archivo_datos);

            unset($value);
            $value = NULL;

            unset($archivo_datos);
            $archivo_datos = NULL;

        }

        echo "TERMINE ... RECONFIGURAR SCRIPT PARA INICIAR";
        exit;
/*
$arreglo = array(
'nombre_contrato' => $this->nombreDocumento,
'ruta_contrato' => $this->rutaURL . $this->nombreDocumento,
);

$cadenaSql = $this->miSql->getCadenaSql('registrarDocumentoCertificado', $arreglo);

$this->registro_certificado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

$arreglo = array(
'id_beneficiario' => $_REQUEST['id_beneficiario'],
'tipologia' => "555",
'nombre_documento' => $this->nombreDocumento,
'ruta_relativa' => $this->rutaURL . $this->nombreDocumento,
);

// $cadenaSql = $this->miSql->getCadenaSql('registrarRequisito', $arreglo);
// $this->registroRequisito = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
 */
    }

    public function obtenerInformacionBeneficiario() {

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionCertificador');
        //echo $cadenaSql;exit;
        $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $this->beneficiario = $beneficiario;

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
        $html2pdf->Output($this->rutaAbsolutaArchivo . $this->nombreDocumento, 'F');
    }

    public function crearPDFCartel() {

        ob_start();
        $html2pdf = new \HTML2PDF('L', 'LETTER', 'es', true, 'UTF-8', array(
            2,
            2,
            2,
            10,
        ));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output($this->rutaAbsolutaArchivo . $this->nombreCartel, 'F');
    }

    public function asosicarCodigoDocumento($beneficiario) {

        unset($this->prefijo);
        $this->prefijo = NULL;

        unset($this->nombreCartel);
        $this->nombreCartel = NULL;

        unset($this->nombreDocumento);
        $this->nombreDocumento = NULL;

        $this->prefijo = substr(md5(uniqid(time())), 0, 6);
        $cadenaSql = $this->miSql->getCadenaSql('consultarParametro', '008');
        $id_parametro = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
        $tipo_documento = $id_parametro['id_parametro'];
        $descripcion_documento = $id_parametro['codigo'] . '_' . str_replace(" ", "_", $id_parametro['descripcion']);
        //$nombre_archivo = "AEP";
        //$this->nombreDocumento = $beneficiario['id_beneficiario'] . "_" . $descripcion_documento . "_" . $this->prefijo . '.pdf';

        $this->nombreDocumento = $beneficiario['manzana'] . "_" . $beneficiario['piso'] . "_" . $beneficiario['numero_identificacion_contrato'] . "_" . $beneficiario['casa_apartamento'] . "_Acta_Entrega_Portatil_" . $this->prefijo . '.pdf';
        $this->nombreCartel = $beneficiario['manzana'] . "_" . $beneficiario['piso'] . "_" . $beneficiario['numero_identificacion_contrato'] . "_" . $beneficiario['casa_apartamento'] . "_Cartel_" . $this->prefijo . '.pdf';

        //$this->nombreCartel = $beneficiario['id_beneficiario'] . "_" . $beneficiario['identificacion'] . "_Cartel_" . $this->prefijo . '.pdf';

    }
    public function estruturaDocumento($beneficiario) {

        //$firma_contratista = $firmacontratista;
        /*
        $fecha = explode("-", $beneficiario['fecha_entrega']);

        $dia = $fecha[0];
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
        "Diciembre",
        ];
        $mes = $mes[$fecha[1]];
        $anno = $fecha[2];
         */
        {
            $tipo_vip = ($beneficiario['tipo_beneficiario_contrato'] == "1") ? "<b>X</b>" : "";
            $tipo_residencial_1 = ($beneficiario['tipo_beneficiario_contrato'] == "2") ? (($beneficiario['estrato_socioeconomico'] == "1") ? "<b>X</b>" : "") : "";
            $tipo_residencial_2 = ($beneficiario['tipo_beneficiario_contrato'] == "2") ? (($beneficiario['estrato_socioeconomico'] == "2") ? "<b>X</b>" : "") : "";
        }

        setlocale(LC_ALL, "es_CO.UTF-8");

        {
            $anexo_dir = '';

            if ($beneficiario['manzana'] != '0' && $beneficiario['manzana'] != '') {
                $anexo_dir .= " Manzana  #" . $beneficiario['manzana'];
            }

            if ($beneficiario['bloque'] != '0' && $beneficiario['bloque'] != '') {
                $anexo_dir .= " Bloque #" . $beneficiario['bloque'];
            }

            if ($beneficiario['torre'] != '0' && $beneficiario['torre'] != '') {
                $anexo_dir .= " Torre #" . $beneficiario['torre'];
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



                        <page backtop='25mm' backbottom='5mm' backleft='10mm' backright='10mm' footer='page'>
                            <page_header>
                                 <table  style='width:100%;' >
                                          <tr>
                                                <td align='center' style='width:100%;border=none;' >
                                                <img src='" . $this->rutaURL_Bloque . "frontera/css/imagen/logos_contrato.png'  width='500' height='45'>
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
//var_dump($beneficiario);exit;

        // trim($beneficiario['serial'])
        $contenidoPagina .= "
                            <br>
                            <br>
                            El suscrito beneficiario del Proyecto Conexiones Digitales II, cuyos datos se presentan a continuación:
                            <br>
                            <br>
                            <table width:100%;>
                                <tr>
                                    <td style='width:25%;'><b>Contrato de Servicio</b></td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . $beneficiario['numero_contrato'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Beneficiario</td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . $beneficiario['nombre_contrato'] . " " . $beneficiario['primer_apellido_contrato'] . " " . $beneficiario['segundo_apellido_contrato'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>No de Identificación</td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . number_format($beneficiario['numero_identificacion_contrato'], 0, '', '.') . "</b></td>
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
                                    <td colspan='3' style='width:75%;text-align:center;'>" . $beneficiario['direccion_domicilio'] . "  " . $anexo_dir . "</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Departamento</td>
                                    <td style='width:25%;text-align:center;'>" . $beneficiario['nombre_departamento'] . "</td>
                                    <td style='width:25%;'>Municipio</td>
                                    <td style='width:25%;text-align:center;'>" . $beneficiario['nombre_municipio'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Urbanización</td>
                                    <td colspan='3' style='width:75%;text-align:center;'>" . $beneficiario['nombre_urbanizacion'] . "</td>
                                </tr>
                            </table>
                            <br>
                            <table  style='width:100%;' >
                                          <tr>
                                                <td align='center' style='width:100%;border=none;' ><b>CERTIFICA BAJO GRAVEDAD DE JURAMENTO</b></td>

                                        </tr>
                            </table>
                             <br>
                            1. Que recibe un computador portátil NUEVO, sin uso, original de fábrica y en perfecto estado de funcionamiento, con las siguientes características:<br>
                            <br>
                                    <table width:100%;>
                                        <tr>
                                            <td align='rigth'  style=' width:20%;'><b>Marca</b></td>
                                            <td align='rigth' style='width:30%;'>Hewlett Packard" . trim($beneficiario['marca']) . "</td>
                                            <td align='rigth' style=' width:20%;'><b>Modelo</b></td>
                                            <td align='rigth' style='width:30%;'>HP 245 G4 Notebook PC" . trim($beneficiario['modelo']) . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Serial</b></td>
                                            <td align='rigth' style='width:30%;'> </td>
                                            <td align='rigth' style=' width:20%;'>Procesador</td>
                                            <td align='rigth' style='width:30%;'>AMD A8-7410 4 cores 2.2 GHz" . trim($beneficiario['procesador']) . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Memoria RAM</b></td>
                                            <td align='rigth' style='width:30%;'>DDR3 4096 MB" . trim($beneficiario['memoria_ram']) . "</td>
                                            <td align='rigth' style=' width:20%;'><b>Disco Duro</b></td>
                                            <td align='rigth' style='width:30%;'>500 GB" . trim($beneficiario['disco_duro']) . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Sistema Operativo</b></td>
                                            <td align='rigth' style='width:30%;'>Ubuntu" . trim($beneficiario['sistema_operativo']) . "</td>
                                            <td align='rigth' style=' width:20%;'><b>Cámara</b></td>
                                            <td align='rigth' style='width:30%;'>Integrada 720 px HD" . trim($beneficiario['camara']) . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Audio</b></td>
                                            <td align='rigth' style='width:30%;'>Integrado Estéreo" . trim($beneficiario['audio']) . "</td>
                                            <td align='rigth' style=' width:20%;'><b>Batería</b></td>
                                            <td align='rigth' style='width:30%;'>41440 mWh" . trim($beneficiario['bateria']) . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Tarjeta de Red (Alámbrica)</b></td>
                                            <td align='rigth' style='width:30%;'>Integrada" . trim($beneficiario['targeta_red_alambrica']) . "</td>
                                            <td align='rigth' style=' width:20%;'><b>Tarjeta de Red (Inalámbrica)</b></td>
                                            <td align='rigth' style='width:30%;'>Integrada" . trim($beneficiario['targeta_red_inalambrica']) . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Cargador</b></td>
                                            <td align='rigth' style='width:30%;'>Smart AC 100 v a 120 v" . trim($beneficiario['cargador']) . "</td>
                                            <td align='rigth' style=' width:20%;'><b>Pantalla</b></td>
                                            <td align='rigth' style='width:30%;'>HD SVA anti-brillo LED 14\"" . trim($beneficiario['pantalla']) . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth'  style=' width:20%;'><b>Sitio web de soporte</b></td>
                                            <td align='rigth' colspan='3' style='width:80%;'>" . trim($beneficiario['web_soporte']) . "</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth'  style=' width:20%;'><b>Teléfono de soporte</b></td>
                                            <td align='rigth' colspan='3' style='width:80%;'>" . trim($beneficiario['telefono_soporte']) . "</td>
                                        </tr>
                                    </table>
                                    <br>
                            2. Que el computador recibido no presenta rayones, roturas, hendiduras o elementos sueltos.<br><br>
                            3. Que entiende que el computador recibido no tiene costo adicional y se encuentra incorporado al contrato de servicio suscrito con la Corporación Politécnica Nacional de Colombia.<br><br>
                            4. Que se compromete a velar por la seguridad del equipo y a cuidarlo para mantener su capacidad de uso y goce en el marco del contrato de servicio suscrito con la Corporación Politécnica Nacional de Colombia.<br>
                                <br>
                            5. Que se compromete a participar en por lo menos 20 horas de  capacitación sobre el manejo del equipo y/o aplicativos de uso productivo de esta herramienta como parte del proceso de apropiación social contemplado en el Anexo Técnico del proyecto Conexiones Digitales II<br><br><br>

                            Para constancia de lo anterior, firma en la ciudad de " . $beneficiario['municipio'] . ", municipio de " . $beneficiario['municipio'] . ", departamento de " . $beneficiario['departamento'] . ", el día __________________________________________.
                            <br>
                            <br>
                            <br>
                            <br>

                            <table width:100%;>
                                <tr>
                                    <td rowspan='2' style='width:50%;'>Firma:<br>&nbsp;
                                    <br>&nbsp;
                                    <br>&nbsp;
                                    <br>&nbsp;
                                    <br>&nbsp;
                                    </td>
                                    <td style='width:50%;text-align:center;'><b>" . $beneficiario['nombre_contrato'] . " " . $beneficiario['primer_apellido_contrato'] . " " . $beneficiario['segundo_apellido_contrato'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:50%;text-align:center;'><b>" . number_format($beneficiario['numero_identificacion_contrato'], 0, '', '.') . "</b></td>
                                </tr>
                            </table>

                    ";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;

        unset($beneficiario);
        $beneficiario = NULL;

        unset($contenidoPagina);
        $contenidoPagina = NULL;

    }

    public function estruturaDocumentoCartel($beneficiario) {

        {
            $anexo_dir = '';

            if ($beneficiario['manzana'] != '0' && $beneficiario['manzana'] != '') {
                $anexo_dir .= " Manzana  #" . $beneficiario['manzana'];
            }

            if ($beneficiario['bloque'] != '0' && $beneficiario['bloque'] != '') {
                $anexo_dir .= " Bloque #" . $beneficiario['bloque'];
            }

            if ($beneficiario['torre'] != '0' && $beneficiario['torre'] != '') {
                $anexo_dir .= " Torre #" . $beneficiario['torre'];
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
                                    font-size:30px;
                                }
                                td {

                                    text-align: left;

                                }
                            </style>



                        <page backtop='25mm' backbottom='10mm' backleft='10mm' backright='10mm'>

                       ";
//var_dump($beneficiario);exit;
        $contenidoPagina .= "
                        <table>
                               <tr>
                                    <td style='width:100%;border:none;font-size:30px;'>
                                                <br>
                                                <b>CODIGO DANE Y ESTRATO: </b>" . $beneficiario['codigo_municipio'] . " - VIP" . "<br><br>
                                                <b>MUNICIPIO:</b>  " . $beneficiario['nombre_municipio'] . "<br><br>
                                                <b>SUBPROYECTO: </b>" . $beneficiario['nombre_urbanizacion'] . "<br><br>
                                                <b>BENEFICIARIO: </b>" . $beneficiario['nombre_contrato'] . " " . $beneficiario['primer_apellido_contrato'] . " " . $beneficiario['segundo_apellido_contrato'] . "<br><br>
                                                <b>DIRECCIÓN: </b>" . $beneficiario['direccion_domicilio'] . "  " . $anexo_dir . "<br><br>
                                                <br>
                                                <br>


                                    </td>
                                </tr>
                            </table>



                            <table>
                               <tr>
                                    <td style='width:100%;text-align:center;border:none;font-size:30px;'><b>CONEXIONES DIGITALES II</b>
                                    <br>CONTRATO DE APORTE 681/2015<</td>
                                </tr>
                            </table>


                            <page_footer>
                            <table  style='width:100%;' >
                                        <tr>
                                                <td align='center' style='width:100%;border=none;' >
                                                <img src='" . $this->rutaURL_Bloque . "frontera/css/imagen/logos_contrato.png'  width='950' height='90'>
                                                </td>
                                        </tr>
                                    </table>
                            </page_footer>
                            ";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;

        unset($beneficiario);
        $beneficiario = NULL;

        unset($contenidoPagina);
        $contenidoPagina = NULL;

    }

}
$miDocumento = new GenerarDocumento($this->miSql, $this->lenguaje);

?>
