<?php
namespace reportes\certificadoNoInternet\entidad;

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
        $this->rutaURL .= '/archivos/certificados/';
        $this->rutaAbsoluta .= '/archivos/certificados/';
        $this->asosicarCodigoDocumento();

        $this->crearPDF();

        if ($_REQUEST['opcion'] != 'edicionInformacion') {

            $arreglo = array(
                'nombre_contrato' => $this->nombreDocumento,
                'ruta_contrato' => $this->rutaURL . $this->nombreDocumento);

            $cadenaSql = $this->miSql->getCadenaSql('registrarDocumentoCertificado', $arreglo);

            $this->registro_certificado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
            $arreglo = array(
                'id_beneficiario' => $_REQUEST['id_beneficiario'],
                'tipologia' => "124",
                'nombre_documento' => $this->nombreDocumento,
                'ruta_relativa' => $this->rutaURL . $this->nombreDocumento,
            );

            $cadenaSql = $this->miSql->getCadenaSql('registrarRequisito', $arreglo);
            $this->registroRequisito = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
        }

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
        $nombre_archivo = str_replace(" ", "_", $descripcion_documento);
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

        //var_dump($_REQUEST);exit;
        //var_dump($_REQUEST);exit;
        $firma_beneficiario = (isset($archivo_datos['ruta_archivo'])) ? "<img src='" . $archivo_datos['ruta_archivo'] . "'  width='125' height='40'>" : " ";

        {

            {
                $firmaBeneficiario = base64_decode($_REQUEST['firmaBeneficiario']);
                $firmaBeneficiario = str_replace("image/svg+xml,", '', $firmaBeneficiario);
                $firmaBeneficiario = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">', '', $firmaBeneficiario);
                $firmaBeneficiario = str_replace("svg", 'draw', $firmaBeneficiario);

                $firmaBeneficiario = str_replace("height", 'height="40" pasos2', $firmaBeneficiario);
                $firmaBeneficiario = str_replace("width", 'width="125" pasos1', $firmaBeneficiario);
            }

            $cadena = $_SERVER['HTTP_USER_AGENT'];
            $resultado = stristr($cadena, "Android");

            if ($resultado) {
                $firmaBeneficiario = str_replace("<path", '<g viewBox="0 0 50 50" transform="scale(0.2,0.2)"><path', $firmaBeneficiario);
                $firmaBeneficiario = str_replace("/>", ' /></g>', $firmaBeneficiario);
            } else {

                $firmaBeneficiario = str_replace("<path", '<g viewBox="0 0 50 50" transform="scale(0.09,0.09)"><path', $firmaBeneficiario);
                $firmaBeneficiario = str_replace("/>", ' /></g>', $firmaBeneficiario);

            }

            ini_set('xdebug.var_display_max_depth', 20000);
            ini_set('xdebug.var_display_max_children', 20000);
            ini_set('xdebug.var_display_max_data', 20000);

        }

        setlocale(LC_ALL, "es_CO.UTF-8");
        $contenidoPagina = "
                            <style type=\"text/css\">
                                table {

                                    font-family:Helvetica, Arial, sans-serif; /* Nicer font */

                                    border-collapse:collapse; border-spacing: 3px;
                                }
                                td, th {
                                    border: 1px solid #CCC;
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
                                            <td rowspan='3' style='width:33.3%;text-align=center;'><b>DECLARACIÓN DE NO ACCESO A SERVICIO DE  INTERNET</b></td>
                                            <td align='center' style='width:33.3%;'>CODIGO: CPN-FO-CDII-63</td>
                                        </tr>

                                        <tr>
                                             <td align='center' style='width:33.3%;'>VERSIÓN: 01</td>
                                        </tr>
                                        <tr>
                                             <td align='center' style='width:33.3%;'>FECHA: 2016-11-01</td>
                                        </tr>
                                    </table>

                        </page_header>";

        $contenidoPagina .= "
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <b>Fecha " . strftime("%d de %B del %Y") . "<br>
                    Ciudad " . $_REQUEST['ciudad_firma'] . ",
                    </b>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>


                    <table  style='width:100%;' >
                            <tr>

                                <td style='border:none;text-align:justify;' >

                    Yo  <b>" . $_REQUEST['nombres'] . " " . $_REQUEST['primer_apellido'] . " " . $_REQUEST['segundo_apellido'] . "</b> identificado(a) con cédula de ciudadanía <b>N°." . $_REQUEST['numero_identificacion'] . " de " . $_REQUEST['ciudad'] . " </b> en mi calidad de beneficiario(a) del Proyecto Conexiones Digitales II Redes de Acceso última milla para la masificación de accesos de banda ancha en viviendas de interés prioritario y hogares en estratos 1 y 2 - Ministerio de las Tecnologías de la Información y las Comunicaciones, por medio de la presente declaro inequívocamente que no he contratado los servicios de internet en los últimos seis (6) meses.
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    Como constancia se firma a los  <b>" . date('d') . "</b> días del mes <b>" . strftime("%B") . "</b> del año <b>" . date('Y') . "</b> en la ciudad de " . $_REQUEST['ciudad_firma'] . ".

                                </td>
                            </tr>
                        </table>

        <br>
        <br>
        <br>
";

        $contenidoPagina .= "<nobreak>
                    <b>Acepto,
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>" . $firmaBeneficiario . "<br>
                                    ____________________________<br>
                                    Firma Propietario<br>
                                    <table style='width:100%;border:none'>
                                        <tr>
                                            <td style='width:25%;text-align:left;border:none'>NOMBRE :</td>
                                            <td style='width:25%;text-align:left;border:none'>" . $_REQUEST['nombres'] . " " . $_REQUEST['primer_apellido'] . " " . $_REQUEST['segundo_apellido'] . "</td>
                                            <td style='width:50%;text-align:center;border:none'> </td>
                                        </tr>
                                        <tr>
                                            <td style='width:25%;text-align:left;border:none'>C.C :</td>
                                            <td style='width:25%;text-align:left;border:none'>" . $_REQUEST['numero_identificacion'] . "</td>
                                            <td style='width:50%;text-align:center;border:none'> </td>
                                        </tr>
                                      ";
        if ($_REQUEST['celular'] != '') {
            $contenidoPagina .= "  <tr>
                                            <td style='width:25%;text-align:left;border:none'>No .Celular :</td>
                                            <td style='width:25%;text-align:left;border:none'>" . $_REQUEST['celular'] . "</td>
                                            <td style='width:50%;text-align:center;border:none'> </td>
                                        </tr>";
        }
        $contenidoPagina .= "
                                    </table>
                                    </b>
                                    </nobreak>";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;
    }
}
$miDocumento = new GenerarDocumento($this->miSql);

?>
