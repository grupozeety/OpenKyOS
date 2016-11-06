<?php
namespace gestionBeneficiarios\generacionContrato\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

include $ruta . "/plugin/html2pdf/html2pdf.class.php";

include_once "core/auth/SesionSso.class.php";

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
    public $miSesionSso;
    public $info_usuario;
    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miSesionSso = \SesionSso::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->info_usuario = $this->miSesionSso->getParametrosSesionAbierta();

        foreach ($this->info_usuario['description'] as $key => $rol) {

            $this->info_usuario['rol'][] = $rol;
        }

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $conexion = "openproject";
        $this->esteRecursoOP = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {

            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }

        /**
         *  2. Información de Beneficiario
         **/

        $this->obtenerInformacionBeneficiario();

        /**
         *  3. Estruturar Documento
         **/

        $this->estruturaDocumento();

        /**
         *  4. Crear PDF
         **/

        $this->crearPDF();

    }
    public function obtenerInformacionBeneficiario() {

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionContrato');

        $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $this->beneficiario = $beneficiario[0];

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
        $html2pdf->Output('BorradorContrato_N_' . $this->beneficiario['numero_contrato'] . '_' . date('Y-m-d') . '.pdf', 'D');

    }
    public function estruturaDocumento() {
        unset($requisitos);
        $arreglo = array(
            'perfil_beneficiario' => $_REQUEST['tipo_beneficiario'],
            'id_beneficiario' => $this->beneficiario['id_beneficiario'],

        );
        $cadenaSql = $this->miSql->getCadenaSql('consultarValidacionRequisitos', $arreglo);

        $requisitos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $cadenaSql = $this->miSql->getCadenaSql('consultaNombreProyecto', $this->beneficiario['urbanizacion']);
        $urbanizacion = $this->esteRecursoOP->ejecutarAcceso($cadenaSql, "busqueda");
        $urbanizacion = $urbanizacion[0];

        $cadenaSql = $this->miSql->getCadenaSql('consultarTipoDocumento', "Cédula de Ciudadanía");
        $CodigoCedula = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $CodigoCedula = $CodigoCedula[0];

        $cadenaSql = $this->miSql->getCadenaSql('consultarTipoDocumento', "Tarjeta de Identidad");
        $CodigoTargeta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $CodigoTargeta = $CodigoTargeta[0];

        $anexo_dir = '';

        if ($this->beneficiario['manzana'] != 0) {
            $anexo_dir .= " Número Manzana  #" . $this->beneficiario['manzana'] . " - ";
        }

        if ($this->beneficiario['bloque'] != 0) {
            $anexo_dir .= " Número Bloque #" . $this->beneficiario['bloque'] . " - ";
        }

        if ($this->beneficiario['torre'] != 0) {
            $anexo_dir .= " Número Torre #" . $this->beneficiario['torre'] . " - ";
        }

        if ($this->beneficiario['casa_apartamento'] != 0) {
            $anexo_dir .= " Número de Casa/Apartamento #" . $this->beneficiario['casa_apartamento'];
        }

        $cedula = ($this->beneficiario['tipo_documento'] == $CodigoCedula['codigo']) ? '<b>(X)</b>' : '';
        $targeta = ($this->beneficiario['tipo_documento'] == $CodigoTargeta['codigo']) ? '<b>(X)</b>' : '';

        $firma_contratista = ($this->beneficiario['url_firma_contratista'] != '' && $_REQUEST['botonGenerarPdfNoFirmas'] != 'true') ? "<img src='" . $this->beneficiario['url_firma_contratista'] . "'  width='125' height='40'>" : "___________________________";

        $firma_beneficiario = ($this->beneficiario['url_firma_beneficiarios'] != '' && $_REQUEST['botonGenerarPdfNoFirmas'] != 'true') ? "<img src='" . $this->beneficiario['url_firma_beneficiarios'] . "'  width='125' height='40'>" : "___________________________";

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
                                               <td align='center' style='width:30%;border=none;' >
                                                 <img src='" . $this->rutaURL . "frontera/css/imagen/vivedigital.png'  width='125' height='40'>
                                                </td>
                                               <td align='center' style='width:70%;border=none;' >
                                                <font size='40px'><b>CONTRATO DE PRESTACIÓN DE SERVICIOS DE COMUNICACIONES</b></font>

                                            </td>
                                        </tr>
                                    </table>

                        </page_header>";

        $contenidoPagina .= " <table style='width:100%;'>
        <tr>
        <td style='width:35%;text-align=center;border:none'> </td>
        <td style='width:30%;text-align=center;border:none'>
        <table style='width:100%;'>
        <tr>
        <td style='width:100%;border:none;text-align:center'><b>COMPRAVENTA DE SERVICIOS</b></td>
        </tr>
        </table>
        </td>
        <td style='width:5%;text-align=center;border:none'> </td>
        <td style='width:30%;text-align=center;border:none'></td>
        </tr>
        </table>
        <br>
        <table style='width:100%;'>
        <tr>
        <td style='width:35%;text-align=center;border:none'> </td>
        <td style='width:30%;text-align=center;border:none'>
        <table style='width:100%;'>
        <tr>
        <td style='width:25%;text-align=center;'><b>Fecha</b></td>
        <td style='width:25%;text-align=center;color:#c5c5c5;'>DD</td>
        <td style='width:25%;text-align=center;color:#c5c5c5;'>MM</td>
        <td style='width:25%;text-align=center;color:#c5c5c5;'>AAAA</td>
        </tr>
        </table>
        </td>
        <td style='width:5%;text-align=center;border:none'> </td>
        <td style='width:30%;text-align=center;border:none'>
        <table style='width:100%;'>
        <tr>
        <td style='width:50%;text-align=center;'><b>N° Contrato</b></td>
        <td style='width:50%;text-align=center;'>" . $this->beneficiario['numero_contrato'] . "</td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        <br>
        <br>
        <br>";
        $contenidoPagina .= "<P style='text-align:justify'>
        <b>CONTRATO DE PRESTACIÓN DE SERVICIOS DE COMUNICACIONES CONEXIONES DIGITALES II</b> Entre las
        siguientes partes a saber: <b>LA CORPORACIÓN POLITÉCNICA NACIONAL DE COLOMBIA</b>, en adelante <b>POLITÉCNICA</b>, entidad sin ánimo de lucro, domiciliada en la ciudad de Bogotá D.C, por una parte, y por la otra, la persona con nombre  <b>" . $this->beneficiario['nombres'] . " " . $this->beneficiario['primer_apellido'] . $this->beneficiario['segundo_apellido'] . "</b> identificado(a) con cédula de ciudadanía N°. <b>" . $this->beneficiario['numero_identificacion'] . "</b>, con domicilio   <b>" . $this->beneficiario['direccion_domicilio'] . " " . $anexo_dir . "</b>, en el departamento de <b>" . $this->beneficiario['nombre_departamento'] . "</b> , municipio <b>" . $this->beneficiario['nombre_municipio'] . "</b> y urbanización <b>" . $urbanizacion['nombre'] . "</b> como USUARIO, cuyos datos son los que aparecen registrados en el formato de solicitud de servicios <b>N° " . $_REQUEST['numero_contrato'] . "</b> suscrito por él mismo, quien ha leído y aceptado en todos sus términos el presente documento, hemos convenido celebrar el presente CONTRATO DE PRESTACIÓN DE SERVICIOS DE COMUNICACIONES el cual se regirá por lo dispuesto en la ley 1341 de 2009, en la Resolución 3066 de 2011 expedida por la Comisión de Regulación de Comunicaciones, y en las normas que la adicionen, modifiquen o deroguen; y en especial, por las siguientes cláusulas: El USUARIO al realizar la acción de iniciar el (los) procedimiento (s) de suscripción para el (los) plan (es) del Servicio de Comunicaciones en la modalidad postpago y prepago, conforme aplique en el contrato suscrito, (en lo sucesivo el Servicio) a través del medio que POLITÉCNICA ponga a disposición del USUARIO; y al suministrar sus datos personales o de empresa, según sea persona natural o jurídica, se entiende que el USUARIO acuerda suscribirse a uno de los planes ofrecidos por POLITÉCNICA del Servicio y expresa su entera e incondicional aceptación, de ser aprobada su solicitud, a los términos y condiciones contenidos en el presente contrato y en los anexos que lo integran (en lo sucesivo denominado, el Contrato)para disponer del Servicio.</P>";

        //$contenidoPagina .= "<P style='text-align:justify;font-size:9px'>" . ($this->beneficiario['clausulas']) . "</P>";

        /*  foreach ($this->clausulas as $key => $value) {

        $contenidoPagina .= "<br><b>" . $value['descripcion'] . "</b><br>";

        foreach ($value['clausulas'] as $key => $contenido) {
        $contenidoPagina .= "<P style='text-align:justify'><b>CLÁUSULA " . trim($contenido['orden_general']) . ".</b>" . $contenido['contenido'] . "</P><br>";
        }

        }*/

        $contenidoPagina .= "<nobreak>
     <table style='width:100%;border:none'>
                            <tr>
                                <td style='width:0%;border:none'> </td>
                                <td style='width:95%;border:none'>

                    <b>COMO CONSTANCIA DE ACEPTACIÓN SUSCRIBE EL PRESENTE CONTRATO EL USUARIO:
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>

                    <table style='width:100%;border:none'>
                                        <tr>
                                            <td style='width:25%;text-align:left;border:none'>FIRMA :</td>
                                            <td style='width:25%;text-align:left;border:none'>" . $firma_beneficiario . "</td>
                                            <td style='width:50%;text-align:center;border:none'> </td>
                                        </tr>
                                        <tr>
                                            <td style='width:25%;text-align:left;border:none'>NOMBRE :</td>
                                            <td style='width:25%;text-align:left;border:none'>" . $this->beneficiario['nombres'] . " " . $this->beneficiario['primer_apellido'] . " " . $this->beneficiario['segundo_apellido'] . "</td>
                                            <td style='width:50%;text-align:center;border:none'> </td>
                                        </tr>
                                        <tr>
                                            <td style='width:25%;text-align:left;border:none'>C.C :</td>
                                            <td style='width:25%;text-align:left;border:none'>" . $this->beneficiario['numero_identificacion'] . "</td>
                                            <td style='width:50%;text-align:center;border:none'> </td>
                                        </tr>
                                        <tr>
                                            <td style='width:25%;text-align:left;border:none'>Correo :</td>
                                            <td style='width:25%;text-align:left;border:none'>" . $this->beneficiario['correo'] . "</td>
                                            <td style='width:50%;text-align:center;border:none'> </td>
                                        </tr>
                                      ";
        if ($this->beneficiario['celular'] != '') {
            $contenidoPagina .= "  <tr>
                                            <td style='width:25%;text-align:left;border:none'>Celular :</td>
                                            <td style='width:25%;text-align:left;border:none'>" . $this->beneficiario['celular'] . "</td>
                                            <td style='width:50%;text-align:center;border:none'> </td>
                                        </tr>";
        }
        $contenidoPagina .= "
                                    </table>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    </b>
                                    </td>
                            </tr>
                    </table>
                   </nobreak>";

        /* FIRMA CONTRATISTA:
        <br>
        <br>
        <br>
        <table style='width:100%;border:none'>
        <tr>
        <td style='width:25%;text-align:left;border:none'>FIRMA :</td>
        <td style='width:25%;text-align:left;border:none'>" . $firma_contratista . "</td>
        <td style='width:50%;text-align:center;border:none'> </td>
        </tr>
        <tr>
        <td style='width:25%;text-align:left;border:none'>NOMBRE :</td>
        <td style='width:25%;text-align:left;border:none'>Corporación Politécnica Nacional de Colombia</td>
        <td style='width:50%;text-align:center;border:none'> </td>
        </tr>
        <tr>
        <td style='width:25%;text-align:left;border:none'>NIT :</td>
        <td style='width:25%;text-align:left;border:none'>830115993</td>
        <td style='width:50%;text-align:center;border:none'> </td>
        </tr>
        </table>*/

        if ($requisitos) {

            $contenidoDocumentos = "<br> <div style='page-break-after:always; clear:both'></div>
                                    <P style='text-align:center'><b>Documentos Faltantes para el Contrato</b></P><br><br>";
            foreach ($requisitos as $key => $value) {
                if ($value['obligatoriedad'] = '1' && is_null($value['nombre_documento'])) {
                    $requisitosFaltantesObligatorios = true;

                    $contenidoPagina .= $contenidoDocumentos . "<P style='text-align:left'>" . $value['nombre_requisitos'] . "</P><br>";
                    $contenidoDocumentos = '';

                }

            }
        }

        $contenidoPagina .= "

            <page_footer  backtop='35mm' backbottom='30mm' backleft='10mm' backright='10mm' >
     Elaborado por : " . $this->info_usuario['uid'][0] . "
            </page_footer>
                    ";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;

    }
}
$miDocumento = new GenerarDocumento($this->sql);

?>
