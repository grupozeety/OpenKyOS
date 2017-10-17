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
        $this->miSesionSso    = \SesionSso::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql         = $sql;
        $this->rutaURL       = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->miProceso     = $proceso;
        $this->ruta_archivos = $ruta_archivos;

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $esteBloque    = $this->miConfigurador->configuracion['esteBloque'];

        if (!isset($esteBloque["grupo"]) || $esteBloque["grupo"] == "") {

            $this->rutaURL .= "/blocks/" . $esteBloque["nombre"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $esteBloque["grupo"] . "/" . $esteBloque["nombre"] . "/";
        }

        //Conexion a Base de Datos
        $conexion              = "interoperacion";
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
                $value                = null;

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

                $this->crearPDF('P', 'MargenesMinimas');

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

    public function crearPDF($orientacion = '', $margenes = '')
    {

        ob_start();
        $html2pdf = new \HTML2PDF($orientacion, 'LETTER', 'es', true, 'UTF-8', $this->parametrizarMargenesContenido($margenes));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output($this->ruta_archivos . "/" . $this->nombre_archivo, 'F');

    }

    public function parametrizarMargenesContenido($parametro = '')
    {
        switch ($parametro) {
            case 'MargenesMinimas':

                $arreglo = array(
                    1,
                    1,
                    1,
                    1,
                );

                break;

            default:
                $arreglo = array(
                    2,
                    2,
                    2,
                    10,
                );
                break;
        }

        return $arreglo;

    }

    public function estruturaDocumentoCartel($beneficiario)
    {

        {

            $direccion_beneficiario = $this->esctruturarDireccionBeneficiario($beneficiario);

            $nombre_beneficiario = $this->estruturarNombreBeneficiario($beneficiario);
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
            $tipo_vip           = ($beneficiario['tipo_beneficiario_contrato'] == "1") ? "<b>VIP</b>" : "";
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
                                                <b>SUBPROYECTO: </b>" . $this->limpiar_caracteres_especiales($beneficiario['nombre_urbanizacion']) . "<br><br>
                                                <b>BENEFICIARIO: </b>" . $nombre_beneficiario . "<br><br>
                                                <b>DIRECCIÓN: </b>" . $direccion_beneficiario . "<br><br>
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

            $fecha       = explode("-", $beneficiario['fecha_entrega']);
            $dia         = $fecha[2];
            $mes         = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            $mes         = $mes[$fecha[1] + 0];
            $anno        = $fecha[0];
            $fecha_letra = $dia . " del mes de " . $mes . " del Año " . $anno;

            $beneficiario['fecha_entrega'] = $beneficiario['fecha_entrega'];

        } else {

            $fecha_letra = "_________ del mes de _________ del año _________";

            $beneficiario['fecha_entrega'] = '';

        }

        {

            $tipo_vip           = ($beneficiario['tipo_beneficiario_contrato'] == "1") ? "<b>X</b>" : "";
            $tipo_residencial_1 = ($beneficiario['tipo_beneficiario_contrato'] == "2") ? (($beneficiario['estrato_socioeconomico_contrato'] == "1") ? "<b>X</b>" : "") : "";
            $tipo_residencial_2 = ($beneficiario['tipo_beneficiario_contrato'] == "2") ? (($beneficiario['estrato_socioeconomico_contrato'] == "2") ? "<b>X</b>" : "") : "";
        }

        setlocale(LC_ALL, "es_CO.UTF-8");

        {

            $direccion_beneficiario = $this->esctruturarDireccionBeneficiario($beneficiario);

        }

        // Caraterizacioón Codigo Departamento

        if ($beneficiario['codigo_departamento'] == '23') {

            $departamento_cordoba = 'X';
            $departamento_sucre   = '';

        } elseif ($beneficiario['codigo_departamento'] == '70') {

            $departamento_cordoba = ' ';
            $departamento_sucre   = 'X';

        } else {
            $departamento_cordoba = ' ';
            $departamento_sucre   = ' ';

        }

        if (!is_null($beneficiario['serial']) && $beneficiario['serial'] != '') {

            $cadenaSql          = $this->miSql->getCadenaSql('consultarInformacionEquipoSerial', $beneficiario['serial']);
            $this->infoPortatil = $this->esteRecursoDBPR->ejecutarAcceso($cadenaSql, "busqueda")[0];

            foreach ($this->infoPortatil as $key => $value) {
                $this->infoPortatil[$key] = trim($value);
            }

        } else {
            $this->informacionEstandarPortatil();
        }

        {
            if ($this->infoPortatil['marca'] == 'Hewlett Packard' && $beneficiario['web_soporte'] == '' && $beneficiario['telefono_soporte'] == '') {
                $this->infoPortatil['web_soporte']      = "http://www.hp.com/latam/co/soporte/cas/";
                $this->infoPortatil['telefono_soporte'] = "0180005147468368 - 018000961016";
            } else {
                $this->infoPortatil['web_soporte']      = $beneficiario['web_soporte'];
                $this->infoPortatil['telefono_soporte'] = $beneficiario['telefono_soporte'];
            }

        }
        {
            // Nombre Beneficiario

            $nombre_beneficiario = $this->estruturarNombreBeneficiario($beneficiario);

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
                                    font-size:13px;

                                }
                            </style>";

        $encabezado = "<page backtop='25mm' backbottom='5mm' backleft='20mm'          backright='20mm' footer='page'>
                            <page_header>
                                 <table  style='width:100%;' >
                                          <tr>
                                                <td align='center' style='width:100%;border=none;' >
                                                <img src='" . $this->rutaURL . "frontera/css/imagen/logos_contrato.png'  width='500' height='45'>
                                                </td>
                                                <tr>
                                                <td> </td>
                                                </tr>
                                                <tr>
                                                <td style='width:100%;border:none;text-align:center;'><br><br><b>008 - ACTA DE ENTREGA DE COMPUTADOR PORTÁTIL</b></td>
                                                </tr>

                                        </tr>
                                    </table>

                        </page_header>
                       ";

        $contenidoPagina .= $encabezado;

        $informacion_beneficiario = "<p>El suscrito beneficiario del Proyecto Conexiones Digitales II, cuyos datos se presentan a continuación:</p>

                            <table width:100%;>
                                <tr>
                                    <td style='width:21%;background-color:#efefef;'>Contrato de Servicios</td>
                                    <td colspan='3' align='center' style='width:80%;'><b>" . $beneficiario['numero_contrato'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:21%;background-color:#efefef;'>Beneficiario</td>
                                    <td colspan='3' style='width:80%;'><b>" . $nombre_beneficiario . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:21%;background-color:#efefef;'>No. de Identificación</td>
                                    <td colspan='3' style='width:80%;'><b>" . number_format($beneficiario['numero_identificacion_contrato'], 0, '', '.') . "</b></td>
                                </tr>
                                <tr>
                                    <td colspan='4' style='width:100%;'><b>Datos de Vivienda</b></td>
                                </tr>
                                <tr>
                                    <td align='center' style='width:20%;'>Tipo</td>
                                    <td align='center' style='width:26.6%;'>Estrato 2 (<b>" . $tipo_residencial_2 . "</b> )</td>
                                    <td align='center' style='width:26.6%;'>Estrato 1 (<b>" . $tipo_residencial_1 . "</b>)</td>
                                    <td align='center' style='width:26.6%;'>VIP (<b>" . $tipo_vip . "</b>)</td>
                                </tr>
                                <tr>
                                    <td style='width:21%;background-color:#efefef;'>Dirección</td>
                                    <td colspan='3' style='width:80%;'>" . $direccion_beneficiario . "</td>
                                </tr>
                                <tr>
                                    <td style='width:21%;background-color:#efefef;'>Departamento</td>
                                    <td align='center' style='width:26.6%;'>" . $beneficiario['nombre_departamento'] . "</td>
                                    <td align='center' style='width:26.6%;background-color:#efefef;'>Municipio</td>
                                    <td align='center' style='width:26.6%;'>" . $beneficiario['nombre_municipio'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:21%;background-color:#efefef;'>Urbanización</td>
                                    <td colspan='3' style='width:80%;'>" . $this->limpiar_caracteres_especiales($beneficiario['nombre_urbanizacion']) . "</td>
                                </tr>
                            </table>";

        $contenidoPagina .= $informacion_beneficiario;

        $contenidoPagina .= "<br>
                            <br>
                            <div align='center'><b>MANIFIESTO QUE:</b></div>
                            <br>
                            <p style='text-align:justify'>
                               1. El contratista entregó un computador portátil marca HP 245 G4 Notebook PC nuevo, a titulo de uso y goce hasta la terminación del contrato de aporte suscrito entre el Fondo TIC y la Corporación Politécnica. En consecuencia, el computador no puede ser vendido, arrendado,transferido, dado en prenda, servir de garantía, so pena de perder el beneficio.<br><br>
                               2. Respecto al computador descrito en la hoja 2 de este documento, dejo constancia que no se me cobro ningún tipo de cargo como usuario beneficiado del Proyecto de Conexiones Digitales y que el equipo fue entregado embalado, garantizando la integridad del mismo.<br><br>
                               3. Además certifico que se realizaron las siguientes pruebas de funcionalidad:
                            </p>
                             <br>
                             <div align='center'>
                                <table align='center' style='width:50%'>
                                    <tr>
                                        <td style='width:80%;background-color:#efefef;'>Correcto encendido/apagado</td>
                                        <td align='center' style='width:20%;'>SI</td>
                                    </tr>
                                    <tr>
                                        <td style='width:80%;background-color:#efefef;'>Equipo funcionando y navegando</td>
                                        <td align='center' style='width:20%;'>SI</td>
                                    </tr>
                                    <tr>
                                        <td style='width:80%;background-color:#efefef;'>Funciona el teclado, parlante y touchpad</td>
                                        <td align='center' style='width:20%;'>SI</td>
                                    </tr>
                                </table>
                             </div>
                             <br>
                            <p style='text-align:justify'>
                            4. La garantía del equipo es un año a partir de la fecha de entrega en la que se firma este documento.<br><br>
                            5. El Contacto de Garantía es la Corporación Politécnica, y me puedo comunicar con la línea gratuita las 24 horas del día de los 7 días de la semana. ((018000 961016)).<br><br>
                            6. Que con el fin de no perder la garantía del fabricante en la eventualidad de presentarse fallas, el beneficiario ni un tercero no autorizado por el fabricante, pueden manipular el equipo tratando de resolver el problema presentado.<br><br>
                            7. En caso de daño, hurto, el usuario debe hacer el reporte a la mesa de ayuda con numero 018000961016, lo cual debe quedar consignado en un ticket para la gestión y seguimiento del mismo.<br><br>
                            8. En caso de pérdida o hurto no habrá reposición del equipo.<br><br>
                            9. Que manifiesto mi entera conformidad y satisfacción del bien que recibo en la fecha y me obligo a realizar su correcto uso, custodia y conservación, autorizando al prestador del servicio (Corporación Politécnica) para que ejerza seguimiento y control sobre el mismo.<br><br>
                            10. Que a la terminación del plazo de ejecución de este contrato de comodato, tendré la opción de adquirir el bien antes descrito.
                            </p>";

        $contenidoPagina .= "</page>";

        $contenidoPagina .= $encabezado;

        $contenidoPagina .= $informacion_beneficiario;

        $contenidoPagina .= "<br>
                            <div align='center'><b>CERTIFICA BAJO GRAVEDAD DE JURAMENTO:</b></div>
                            <p style='text-align:justify'>
                              1. Que recibe un computador portátil NUEVO, sin uso, original de fábrica y en perfecto estado de funcionamiento, con las siguientes características:
                            </p>
                            <table>
                                <tr>
                                    <td style='width:20%;background-color:#efefef;'><b>Modelo</b></td>
                                    <td style='width:30%'>" . $this->infoPortatil['modelo'] . "</td>
                                    <td style='width:18%;background-color:#efefef;'><b>Marca</b></td>
                                    <td style='width:32%'>" . $this->infoPortatil['marca'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:20%;background-color:#efefef;'><b>Procesador</b></td>
                                    <td style='width:30%'>" . $this->infoPortatil['procesador'] . "</td>
                                    <td style='width:18%;background-color:#efefef;'><b>Serial</b></td>
                                    <td style='width:32%'>" . $beneficiario['serial'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:20%;background-color:#efefef;'><b>Disco Duro</b></td>
                                    <td style='width:30%'>" . $this->infoPortatil['disco_duro'] . "</td>
                                    <td style='width:18%;background-color:#efefef;'><b>Memoria RAM</b></td>
                                    <td style='width:32%'>" . $this->infoPortatil['memoria_ram'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:20%;background-color:#efefef;'><b>Cámara</b></td>
                                    <td style='width:30%'>" . $this->infoPortatil['camara'] . "</td>
                                    <td style='width:18%;background-color:#efefef;'><b>Sistema Operativo</b></td>
                                    <td style='width:32%'>" . $this->infoPortatil['sistema_operativo'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:20%;background-color:#efefef;'><b>Batería</b></td>
                                    <td style='width:30%'>" . $this->infoPortatil['bateria'] . "</td>
                                    <td style='width:18%;background-color:#efefef;'><b>Audio</b></td>
                                    <td style='width:32%'>" . $this->infoPortatil['audio'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:20%;background-color:#efefef;'><b>Tarjeta de Red<br>(Inalámbrica)</b></td>
                                    <td style='width:30%'>" . $this->infoPortatil['targeta_red_inalambrica'] . "</td>
                                    <td style='width:18%;background-color:#efefef;'><b>Tarjeta de Red<br>(Alámbrica)</b></td>
                                    <td style='width:32%'>" . $this->infoPortatil['targeta_red_alambrica'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:20%;background-color:#efefef;'><b>Pantalla</b></td>
                                    <td style='width:30%'>" . $this->infoPortatil['pantalla'] . "</td>
                                    <td style='width:18%;background-color:#efefef;'><b>Cargador</b></td>
                                    <td style='width:32%'>" . $this->infoPortatil['cargador'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:20%;background-color:#efefef;'><b>Sitio Web de Soporte</b></td>
                                    <td colspan='3' style='width:80%'>" . $this->infoPortatil['web_soporte'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:20%;background-color:#efefef;'><b>Teléfono de Soporte</b></td>
                                    <td colspan='3' style='width:80%'>" . $this->infoPortatil['telefono_soporte'] . "</td>
                                </tr>
                            </table>
                            <p style='text-align:justify'>
                            2. Que el computador recibido no presenta rayones, roturas, hendiduras o elementos sueltos.<br><br>
                            3. Que entiende que el computador recibido no tiene costo adicional y se encuentra incorporado al contrato de servicio suscrito con la Corporación Politécnica Nacional de Colombia.<br><br>
                            4. Que se compromete a velar por la seguridad del equipo y a cuidarlo para mantener su capacidad de uso y goce en el marco del contrato de servicio suscrito con la Corporación Politécnica Nacional de Colombia.<br><br>
                            <br><br>
                            </p>";

        $contenidoPagina .= "<p style='text-align:justify'>
                            Para constancia de lo anterior, firma en el municipio de " . $beneficiario['nombre_municipio'] . ", departamento de " . $beneficiario['nombre_departamento'] . ",
                            <br>el día " . $fecha_letra . " .
                            </p><br>";

        $contenidoPagina .= "<table style='width:100%;border-color:#999999;>
                                <tr>
                                    <td style='width:50%;'>Nombre Beneficiario:<br><br><b>" . $nombre_beneficiario . "</b></td>
                                    <td rowspan='2' style='width:50%;color:#999999'><b>Firma<br><br><br><br><br></b><br></td>
                                </tr>
                                <tr>
                                    <td style='width:50%;'>No. de Identificación:<br><br><b>" . number_format($beneficiario['numero_identificacion_contrato'], 0, '', '.') . "</b></td>
                                </tr>
                            </table>";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;

        unset($beneficiario);
        $beneficiario = null;

        unset($contenidoPagina);
        $contenidoPagina = null;

    }

    public function estruturaDocumentoActaServicios($beneficiario)
    {

        $direccion_beneficiario = $this->esctruturarDireccionBeneficiario($beneficiario);
        $nombre_beneficiario    = $this->estruturarNombreBeneficiario($beneficiario);

        {

            $tipo_vip           = ($beneficiario['estrato'] == 1) ? "<b>X</b>" : "";
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
                                                <td></td>
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
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . $nombre_beneficiario . "</b></td>
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
                                    <td colspan='3' style='width:75%;text-align:center;'>" . $direccion_beneficiario . "</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Departamento</td>
                                    <td style='width:25%;text-align:center;'>" . $beneficiario['departamento'] . "</td>
                                    <td style='width:25%;'>Municipio</td>
                                    <td style='width:25%;text-align:center;'>" . $beneficiario['municipio'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Urbanización</td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . $this->limpiar_caracteres_especiales($beneficiario['urbanizacion']) . "</b></td>
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
                            <table width:100%;>
                                <tr>
                                    <td rowspan='2' align='rigth' style='vertical-align:top;width:50%;'>Firma: <br>&nbsp;
                                    <br>&nbsp;
                                    <br>&nbsp;

                                    </td>
                                    <td style='width:50%;text-align:center;'><b>" . $nombre_beneficiario . "</b></td>
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

            'camara'                     => 'Integrada 720 px HD Grabación, Video y Fotografía',

            'mouse_tipo'                 => 'Touchpad con capacidad multi-touch',
            'sistema_operativo'          => 'Ubuntu',

            'targeta_audio_video'        => 'Incorporados',

            'disco_duro'                 => '500 GB velocidad de 5.400 rpm',

            'autonomia'                  => 'Mín. Cuatro horas – 6 celdas',

            'puerto_usb'                 => '(2)Usb 2.0 y (3) Ubs 3.0',

            'voltaje'                    => '100 v a 120 v - 50 Hz a 60 Hz',

            'targeta_memoria'            => 'multi-format digital media reader(soporta SD, SDHC, SDXC)',

            'salida_video'               => 'VGA 1 y HMDI 1',

            'cargador'                   => 'Adaptador Smart AC 100 v a 120 v',

            'bateria_tipo'               => 'Recargable Lithium Ion',

            'teclado'                    => 'Español(Internacional)',

            'marca'                      => 'Hewlett Packard',

            'modelo'                     => 'HP 245 G4 Notebook PC',

            'procesador'                 => 'AMD A8-7410 2200 MHz cores 2.2 GHz',

            'arquitectura'               => '64 Bits',

            'memoria_ram'                => 'DDR3 4096 MB',

            'compatibilidad_memoria_ram' => 'PAE, NX, y SSE 4.x',

            'tecnologia_memoria_ram'     => 'DDR3',

            'antivirus'                  => 'Clamav Antivirus',

            'disco_anti_impacto'         => 'N/A',

            'serial'                     => '',

            'audio'                      => 'Integrado Mono/Estereo',

            'bateria'                    => '41610 mWh',

            'targeta_red_alambrica'      => 'Integrada',

            'targeta_red_inalambrica'    => 'Integrada',

            'pantalla'                   => 'HD SVA anti-brillo LED14"',

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

    public function estruturarNombreBeneficiario($beneficiario)
    {

        // Nombre Beneficiario

        $nombre_beneficiario = $beneficiario['nombre_contrato'] . " " . $beneficiario['primer_apellido_contrato'] . " " . $beneficiario['segundo_apellido_contrato'];

        $nombre_beneficiario = strtoupper(trim($nombre_beneficiario));

        return $nombre_beneficiario;
    }

    public function esctruturarDireccionBeneficiario($beneficiario)
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

        $direccion_beneficiario = strtoupper(trim($beneficiario['direccion_domicilio'] . " " . $anexo_dir));

        return $direccion_beneficiario;

    }
}
$miDocumento = new GenerarDocumento($this->miSql, $this->proceso, $this->rutaAbsoluta_archivos);
