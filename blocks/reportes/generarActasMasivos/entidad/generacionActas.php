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

            foreach ($this->beneficiario_aes as $key => $value) {

                $this->estruturaDocumentoActaServicios($value);
                $this->asosicarNombreArchivo($value, "Acta_Servicios");
                $this->crearPDF('P');

                unset($this->contenidoPagina);
                $this->contenidoPagina = NULL;

                unset($this->nombre_archivo);
                $this->nombre_archivo = NULL;
                $value = NULL;

            }

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

                $this->estruturaDocumentoActaPortatil($value);

                $this->asosicarNombreArchivo($value, "Acta_Portatil");

                $this->crearPDF('P');

                unset($this->contenidoPagina);
                $this->contenidoPagina = NULL;

                unset($this->nombre_archivo);
                $this->nombre_archivo = NULL;

                $this->estruturaDocumentoCartel($value);

                $this->asosicarNombreArchivo($value, "Cartel");

                $this->crearPDF('L');

                unset($this->contenidoPagina);
                $this->contenidoPagina = NULL;

                unset($this->nombre_archivo);
                $this->nombre_archivo = NULL;

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

    public function crearPDF($orientacion = '') {
        ob_start();
        $html2pdf = new \HTML2PDF($orientacion, 'LETTER', 'es', true, 'UTF-8', array(
            2,
            2,
            2,
            10,
        ));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output($this->ruta_archivos . "/" . $this->nombre_archivo, 'F');

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

            if (!is_null($beneficiario['barrio_contrato']) && $beneficiario['barrio_contrato'] != '') {
                $anexo_dir .= " Barrio " . $beneficiario['barrio_contrato'];
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

        {
            $tipo_vip = ($beneficiario['tipo_beneficiario_contrato'] == "1") ? "<b>VIP</b>" : "";
            $tipo_residencial_1 = ($beneficiario['tipo_beneficiario_contrato'] == "2") ? (($beneficiario['estrato_socioeconomico_contrato'] == "1") ? "<b>Adicional Est. 1</b>" : "") : "";
            $tipo_residencial_2 = ($beneficiario['tipo_beneficiario_contrato'] == "2") ? (($beneficiario['estrato_socioeconomico_contrato'] == "2") ? "<b>Adicional Est. 1</b>" : "") : "";

            switch ($beneficiario['tipo_beneficiario_contrato']) {
                case '1':
                    $tipo = 'VIP';
                    break;

                case '2':

                    if ($beneficiario['estrato_socioeconomico_contrato'] == "1") {
                        $tipo = 'Adicional Estrato. 1';
                    }

                    if ($beneficiario['estrato_socioeconomico_contrato'] == "2") {
                        $tipo = 'Adicional Estrato. 2';
                    }

                    break;

            }
        }

        $contenidoPagina .= "
                        <table>
                               <tr>
                                    <td style='width:100%;border:none;font-size:30px;'>
                                                <br>
                                                <b>CODIGO DANE Y ESTRATO: </b>" . $beneficiario['codigo_municipio'] . " - " . $tipo . "<br><br>
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
                                                <img src='" . $this->rutaURL . "frontera/css/imagen/logos_contrato.png'  width='950' height='90'>
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

    public function estruturaDocumentoActaPortatil($beneficiario) {

        if ($beneficiario['fecha_entrega'] == '') {

            $fecha = 'el día___________________________';
        } else {

            $fecha = explode("-", $beneficiario['fecha_entrega']);

            $dia = $fecha[2];
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
            $anno = $fecha[0];

            $fecha = 'el día ' . $dia . " de " . $mes . " del " . $anno;
        }

        {
            $tipo_vip = ($beneficiario['tipo_beneficiario_contrato'] == "1") ? "<b>X</b>" : "";
            $tipo_residencial_1 = ($beneficiario['tipo_beneficiario_contrato'] == "2") ? (($beneficiario['estrato_socioeconomico_contrato'] == "1") ? "<b>X</b>" : "") : "";
            $tipo_residencial_2 = ($beneficiario['tipo_beneficiario_contrato'] == "2") ? (($beneficiario['estrato_socioeconomico_contrato'] == "2") ? "<b>X</b>" : "") : "";
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

            if (!is_null($beneficiario['barrio_contrato']) && $beneficiario['barrio_contrato'] != '') {
                $anexo_dir .= " Barrio " . $beneficiario['barrio_contrato'];
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
                                            <td align='rigth' style='width:30%;'>" . trim($beneficiario['serial']) . "</td>
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

                            Para constancia de lo anterior, firma en la ciudad de " . $beneficiario['municipio'] . ", municipio de " . $beneficiario['municipio'] . ", departamento de " . $beneficiario['departamento'] . ", " . $fecha . ".
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
