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

        $this->crearPDF();

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
        $html2pdf->Output('CertificadoNOInternet_CC_' . $this->infoCertificado['identificacion'] . '_' . date('Y-m-d') . '.pdf', 'D');

    }
    public function estruturaDocumento() {

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionCertificado');
        $infoCertificado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
        $this->infoCertificado = $infoCertificado;

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
                    Ciudad " . $infoCertificado['ciudad_firma'] . ",
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

                    Yo  <b>" . $infoCertificado['nombre'] . " " . $infoCertificado['primer_apellido'] . " " . $infoCertificado['segundo_apellido'] . "</b> identificado(a) con cédula de ciudadanía <b>N°." . $infoCertificado['identificacion'] . " de " . $infoCertificado['ciudad_expedicion_identificacion'] . " </b> en mi calidad de beneficiario(a) del Proyecto Conexiones Digitales II Redes de Acceso última milla para la masificación de accesos de banda ancha en viviendas de interés prioritario y hogares en estratos 1 y 2 - Ministerio de las Tecnologías de la Información y las Comunicaciones, por medio de la presente declaro inequívocamente que no he contratado los servicios de internet en los últimos seis (6) meses.
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    Como constancia se firma a los  <b>" . date('d') . "</b> días del mes <b>" . strftime("%B") . "</b> del año <b>" . date('Y') . "</b> en la ciudad de " . $infoCertificado['ciudad_firma'] . ".

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
                                    <br><br>
                                    ____________________________<br>
                                    Firma Propietario<br>
                                    <table style='width:100%;border:none'>
                                        <tr>
                                            <td style='width:25%;text-align:left;border:none'>NOMBRE :</td>
                                            <td style='width:25%;text-align:left;border:none'>" . $infoCertificado['nombre'] . " " . $infoCertificado['primer_apellido'] . " " . $infoCertificado['segundo_apellido'] . "</td>
                                            <td style='width:50%;text-align:center;border:none'> </td>
                                        </tr>
                                        <tr>
                                            <td style='width:25%;text-align:left;border:none'>C.C :</td>
                                            <td style='width:25%;text-align:left;border:none'>" . $infoCertificado['identificacion'] . "</td>
                                            <td style='width:50%;text-align:center;border:none'> </td>
                                        </tr>
                                      ";
        if ($infoCertificado['celular'] != '') {
            $contenidoPagina .= "  <tr>
                                            <td style='width:25%;text-align:left;border:none'>No .Celular :</td>
                                            <td style='width:25%;text-align:left;border:none'>" . $infoCertificado['celular'] . "</td>
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
$miDocumento = new GenerarDocumento($this->sql);

?>
