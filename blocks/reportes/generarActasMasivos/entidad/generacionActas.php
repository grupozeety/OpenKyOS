<?php
namespace reportes\generarActasMasivos\entidad;

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
    public function __construct($sql, $proceso, $ruta_archivos)
    {
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
                $this->contenidoPagina = null;

                unset($this->nombre_archivo);
                $this->nombre_archivo = null;
                $value = null;

            }

            unset($this->beneficiario_aes);
            $this->beneficiario_aes = null;

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
                $this->contenidoPagina = null;

                unset($this->nombre_archivo);
                $this->nombre_archivo = null;

                $this->estruturaDocumentoCartel($value);

                $this->asosicarNombreArchivo($value, "Cartel");

                $this->crearPDF('L');

                unset($this->contenidoPagina);
                $this->contenidoPagina = null;

                unset($this->nombre_archivo);
                $this->nombre_archivo = null;

                $value = null;

            }

        }

    }

    public function asosicarNombreArchivo($beneficiario, $nombre = '')
    {
        $this->nombre_archivo = '';
        foreach ($this->nombre as $key => $value) {

            $this->nombre_archivo .= $beneficiario[$value] . "_";
            $value = null;

        }

        $prefijo = substr(md5(uniqid(time())), 0, 6);

        $this->nombre_archivo .= $nombre . "_";

        $this->nombre_archivo = str_replace(".", "_", $this->nombre_archivo);
        $this->nombre_archivo .= $prefijo . ".pdf";

    }

    public function obtenerInformacionBeneficiarioActaPortatil()
    {
        $arreglo = explode(";", $this->miProceso['datos_adicionales']);

        $arreglo = "'" . implode("','", $arreglo) . "'";

        $cadenaSql = $this->miSql->getCadenaSql('ConsultaBeneficiariosActaPortatil', $arreglo);

        $this->beneficiario_aes = $this->esteRecursoDBPR->ejecutarAcceso($cadenaSql, "busqueda");

    }

    public function obtenerInformacionBeneficiarioActaServicios()
    {
        $arreglo = explode(";", $this->miProceso['datos_adicionales']);

        $arreglo = "'" . implode("','", $arreglo) . "'";

        $cadenaSql = $this->miSql->getCadenaSql('ConsultaBeneficiariosActaServicio', $arreglo);

        $this->beneficiario_aes = $this->esteRecursoDBPR->ejecutarAcceso($cadenaSql, "busqueda");

    }

    public function crearPDF($orientacion = '')
    {
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

    public function estruturaDocumentoCartel($beneficiario)
    {

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

            if (!is_null($beneficiario['barrio']) && $beneficiario['barrio'] != '') {
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
        $beneficiario = null;

        unset($contenidoPagina);
        $contenidoPagina = null;

    }

    public function estruturaDocumentoActaPortatil($beneficiario)
    {

        if (!is_null($beneficiario['fecha_entrega']) && $beneficiario['fecha_entrega'] != '') {

            $fecha = explode("-", $beneficiario['fecha_entrega']);
            $dia = $fecha[2];
            $mes = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            $mes = $mes[$fecha[1] + 0];
            $anno = $fecha[0];
            $fecha_letra = $dia . " del mes de " . $mes . " del Año " . $anno;

            $beneficiario['fecha_entrega'] = $beneficiario['fecha_entrega'];

        } else {

            $fecha_letra = "_________ del mes de _________ del Año _________";

            $beneficiario['fecha_entrega'] = '';

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

        }

        // Caraterizacioón Codigo Departamento

        if ($beneficiario['codigo_departamento'] == '23') {

            $departamento_cordoba = 'X';
            $departamento_sucre = '';

        } elseif ($beneficiario['codigo_departamento'] == '70') {

            $departamento_cordoba = ' ';
            $departamento_sucre = 'X';

        } else {
            $departamento_cordoba = ' ';
            $departamento_sucre = ' ';

        }

        if (!is_null($beneficiario['serial']) && $beneficiario['serial'] != '') {

            $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionEquipoSerial', $beneficiario['serial']);
            $this->infoPortatil = $this->esteRecursoDBPR->ejecutarAcceso($cadenaSql, "busqueda")[0];

            foreach ($this->infoPortatil as $key => $value) {
                $this->infoPortatil[$key] = trim($value);
            }

        } else {
            $this->informacionEstandarPortatil();
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

        $contenidoPagina .= "<p>El suscrito beneficiario del Proyecto Conexiones Digitales II, cuyos datos se presentan a continuación:</p>
                            <table width:100%;>
                                <tr>
                                    <td style='width:20%;'><b>Nombres y Apellidos</b></td>
                                    <td style='width:35%;'><b>" . $beneficiario['nombre_contrato'] . " " . $beneficiario['primer_apellido_contrato'] . " " . $beneficiario['segundo_apellido_contrato'] . "</b></td>
                                    <td style='width:15%;'><b>Fecha Entrega</b></td>
                                    <td colspan='2' style='width:30%;'>" . $beneficiario['fecha_entrega'] . "</td>
                                </tr>

                                <tr>
                                    <td style='width:20%;'><b>Dirección</b></td>
                                    <td style='width:35%;'>" . $beneficiario['direccion_domicilio'] . " " . $anexo_dir . "</td>
                                    <td style='width:15%;'><b>Cedula</b></td>
                                    <td colspan='2' style='width:30%;'><b>" . number_format($beneficiario['numero_identificacion_contrato'], 0, '', '.') . "</b></td>
                                </tr>
                                <tr>
                                    <td rowspan='2' style='width:20%;'><b>Urbanización</b></td>
                                    <td rowspan='2' style='width:35%;'>" . $this->limpiar_caracteres_especiales($beneficiario['nombre_urbanizacion']) . "</td>
                                    <td style='width:15%;'><b>Municipio</b></td>
                                    <td colspan='2' style='width:30%;'>" . $beneficiario['nombre_municipio'] . "</td>
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
conformidad y satisfacción del bien que recibo en la fecha, y me obligo a realizar su correcto uso, custodia y conservación, autorizando al prestador del servicio Corporación Politécnica a, para que ejerza el seguimiento y control sobre el adecuado y correcto uso, custodia y conservación del mismo.<br>A la terminación del plazo de ejecución de este contrato de comodato, tendré la opción de adquirir el bien antes descrito entregado en comodato. Para constancia de lo anterior, firmo con copia de mi documento de identidad hoy día " . $fecha_letra . ". En el municipio de <b>" . $beneficiario['municipio'] . "</b>, Departamento <b>" . $beneficiario['departamento'] . "</b>.</p>

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
                                <td align='center'  style='width:35%;border:none;font-size:6px'><b>" . $beneficiario['nombre_contrato'] . " " . $beneficiario['primer_apellido_contrato'] . " " . $beneficiario['segundo_apellido_contrato'] . "</b></td>
                                <td align='center'  style='width:10%;border:none;'> </td>
                                <td align='center'  style='width:10%;border:none;'>Nombre</td>
                                <td align='center'  style='width:35%;border:none;'>_________________________</td>
                             </tr>
                             <tr>
                                <td align='center'  style='width:10%;border:none;'>Cedula</td>
                                <td align='center'  style='width:35%;border:none;font-size:6px'><b>" . $beneficiario['numero_identificacion_contrato'] . "</b></td>
                                <td align='center'  style='width:10%;border:none;'> </td>
                                <td align='center'  style='width:10%;border:none;'>Cedula</td>
                                <td align='center'  style='width:35%;border:none;'>_________________________</td>
                             </tr>
                            </table>


                    ";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;

        unset($beneficiario);
        $beneficiario = null;

        unset($contenidoPagina);
        $contenidoPagina = null;

    }

    public function estruturaDocumentoActaServicios($beneficiario)
    {

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
$miDocumento = new GenerarDocumento($this->miSql, $this->proceso, $this->rutaAbsoluta_archivos);
