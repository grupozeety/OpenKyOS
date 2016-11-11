<?php
namespace gestionBeneficiarios\generacionContrato\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

use gestionBeneficiarios\generacionContrato\entidad\Redireccionador;

include_once 'Redireccionador.php';
class FormProcessor {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $archivos_datos;
    public $esteRecursoDB;
    public $datos_contrato;
    public $rutaURL;
    public $rutaAbsoluta;
    public $clausulas;
    public $registro_info_contrato;
    public function __construct($lenguaje, $sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }
        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        //$this->cargarClausula();

        /**
         *  1. CargarArchivos en el Directorio
         **/

        $this->cargarArchivos();

        /**
         *  2. Procesar Informacion Contrato
         **/

        $this->procesarInformacion();

        if ($_REQUEST['firmaBeneficiario'] != '') {

            include_once "guardarDocumentoPDF.php";

        }

        if ($this->registro_info_contrato) {
            Redireccionador::redireccionar("InsertoInformacionContrato");
        } else {
            Redireccionador::redireccionar("NoInsertoInformacionContrato");
        }

    }

    public function procesarInformacion() {

        if ($this->archivos_datos === '') {
            $soporte = '';

        } else {
            $soporte = $this->archivos_datos[0]['ruta_archivo'];

        }

        $url_firma_beneficiario = $_REQUEST['firmaBeneficiario'];

        //$url_firma_contratista = $_REQUEST['firmaInstalador'];

        $clausulas = $this->clausulas;

        switch ($_REQUEST['tipo']) {

            case '1':
                $valor_tarificacion = '6500';
                break;

            case '2':

                $valor_tarificacion = '0';

                if ($_REQUEST['estrato_economico'] == '1') {
                    $valor_tarificacion = '12600';
                } elseif ($_REQUEST['estrato_economico'] == '2') {
                    $valor_tarificacion = '17600';
                }

                break;

            case '3':
                $valor_tarificacion = $_REQUEST['valor_tarificacion'];
                break;

        }

        $arreglo = array(
            'nombres' => $_REQUEST['nombres'],
            'primer_apellido' => $_REQUEST['primer_apellido'],
            'segundo_apellido' => $_REQUEST['segundo_apellido'],
            'tipo_documento' => $_REQUEST['tipo_documento'],
            'numero_identificacion' => $_REQUEST['numero_identificacion'],
            'fecha_expedicion' => $_REQUEST['fecha_expedicion'],
            'direccion_domicilio' => $_REQUEST['direccion_domicilio'],
            'direccion_instalacion' => '',
            'departamento' => $_REQUEST['departamento'],
            'municipio' => $_REQUEST['municipio'],
            'urbanizacion' => $_REQUEST['urbanizacion'],
            'estrato' => $_REQUEST['tipo'],
            'barrio' => $_REQUEST['barrio'],
            'telefono' => $_REQUEST['telefono'],
            'celular' => $_REQUEST['celular'],
            'correo' => $_REQUEST['correo'],
            'cuenta_suscriptor' => ' ',
            'velocidad_internet' => $_REQUEST['velocidad_internet'],
            'fecha_inicio_vigencia_servicio' => '',
            'fecha_fin_vigencia_servicio' => '',
            'valor_mensual' => $valor_tarificacion,
            'marca' => ' ',
            'modelo' => ' ',
            'serial' => ' ',
            'tecnologia' => ' ',
            'estado' => ' ',
            'clausulas' => '',
            'url_firma_contratista' => '',
            'url_firma_beneficiario' => $url_firma_beneficiario,
            'manzana' => $_REQUEST['num_manzana'],
            'bloque' => $_REQUEST['num_bloque'],
            'torre' => $_REQUEST['num_torre'],
            'casa_apartamento' => $_REQUEST['tipo_tecnologia'],
            'tipo_tecnologia' => $_REQUEST['tipo_tecnologia'],
            'valor_tarificacion' => $valor_tarificacion,
            'medio_pago' => $_REQUEST['medio_pago'],
            'tipo_pago' => $_REQUEST['tipo_pago'],
            'soporte' => $soporte,
        );

        $cadenaSql = $this->miSql->getCadenaSql('registrarInformacionContrato', $arreglo);
        $this->registro_info_contrato = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

    }

    public function cargarArchivos() {

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
                    echo "error";exit;
                    Redireccionador::redireccionar("ErrorCargarFicheroDirectorio");
                }

                $archivo_datos[] = array(
                    'ruta_archivo' => $ruta_relativa,
                    'nombre_archivo' => $archivo['name'],
                    'campo' => $key,
                );

            }

        }

        $this->archivos_datos = $archivo_datos;

    }

    public function cargarClausula() {

        $this->clausulas = 'CONTRATO DE PRESTACIÓN DE SERVICIOS DE COMUNICACIONES CONEXIONES DIGITALES II


Entre las siguientes partes a saber: LA CORPORACIÓN POLITÉCNICA NACIONAL DE COLOMBIA, en adelante POLITÉCNICA, entidad sin ánimo de lucro, domiciliada en la ciudad de Bogotá D.C, por una parte, y por la otra, la persona identificada como USUARIO, cuyos datos son los que aparecen registrados en el formato de solicitud de servicios N° _____ suscrito por él mismo, quien ha leído y aceptado en todos sus términos el presente documento, hemos convenido celebrar el presente CONTRATO DE PRESTACIÓN DE SERVICIOS DE COMUNICACIONES el cual se regirá por lo dispuesto en la ley 1341 de 2009, en la Resolución 3066 de 2011 expedida por la Comisión de Regulación de Comunicaciones, y en las normas que la adicionen, modifiquen o deroguen; y en especial, por las siguientes cláusulas: El USUARIO al realizar la acción de iniciar el (los) procedimiento (s) de suscripción para el (los) plan (es) del Servicio de Comunicaciones en la modalidad postpago y prepago, conforme aplique en el contrato suscrito, (en lo sucesivo el Servicio) a través del medio que POLITÉCNICA ponga a disposición del USUARIO; y al suministrar sus datos personales o de empresa, según sea persona natural o jurídica, se entiende que el USUARIO acuerda suscribirse a uno de los planes ofrecidos por POLITÉCNICA del Servicio y expresa su entera e incondicional aceptación, de ser aprobada su solicitud, a los términos y condiciones contenidos en el presente contrato y en los anexos que lo integran (en lo sucesivo denominado, el Contrato) para disponer del Servicio CLÁUSULA PRIMERA. OBJETO DEL CONTRATO. Este contrato tiene por objeto establecer las condiciones técnicas, jurídicas y económicas que regirán la prestación al USUARIO de servicios de comunicaciones, específicamente Internet fijo, en condiciones de calidad y eficiencia, a cambio de un precio en dinero, pactado de acuerdo con lo previsto en el Anexo Técnico del Contrato de Aporte N° 681 de 2015 y al documento de recomendaciones de la CRC “CONEXIONES DIGITALES Esquema para la implementación de subsidios e Incentivos para el acceso a Internet de Última milla”, la regulación y reglamentación que expidan la Comisión de Regulación de Comunicaciones, el Ministerio de Tecnologías de la Información y las Comunicaciones y la Superintendencia de Industria y Comercio, cada uno de ellos en la órbita de su competencia y las cláusulas del presente contrato, en el marco del Contrato de Aporte N° 681 de 2015. PARÁGRAFO PRIMERO: El USUARIO tiene derecho a elegir el medio a través del cual desea recibir copia del presente Contrato, es decir, de manera física o electrónica, de acuerdo con lo estipulado en el artículo 11.1 de la Resolución CRC 3066 de 2011. En caso que EL USUARIO elija que sea por medio electrónico, de todas formas tendrá el derecho a solicitar en cualquier momento, la entrega de la copia impresa, por una sola vez durante la vigencia del contrato. CLÁUSULA SEGUNDA. SERVICIOS CONTRATADOS, PRECIO Y FORMA DE PAGO: Los servicios que prestará POLITÉCNICA al USUARIO, serán definidos en el Formato de Solicitud de Servicios suscrito(s) por el USUARIO en el (los) cual (es) acepta los términos y condiciones de prestación de los mismos. El valor de los servicios contratados se establecerá de conformidad con las tarifas contenidas en el Anexo Técnico del Contrato de Aporte N° 681 de 2015 y al documento de recomendaciones de la CRC “CONEXIONES DIGITALES Esquema para la implementación de subsidios e Incentivos para el acceso a Internet de Última milla”. A dicha suma se le agregará el IVA en el porcentaje establecido por el Gobierno Nacional, según sea el caso. POLITÉCNICA realizará, por mensualidades vencidas, los cobros derivados de la prestación del servicio. Dichos valores se incorporarán en la factura de servicios que expida POLITÉCNICA. Los pagos se realizarán de acuerdo con las condiciones, plazos y sitios establecidos en las facturas expedidas por POLITÉCNICA. El no recibo de la factura no exime al USUARIO del pago de Servicio ni del recargo por mora que se genere. CLÁUSULA TERCERA. VIGENCIA Y DURACIÓN DEL CONTRATO: La duración del contrato será la establecida en el Formato de Solicitud de Servicios. El término del presente contrato se contará a partir del momento en que se instalen los servicios solicitados, fecha que se indicará en el ACTA DE INSTALACIÓN suscrita por un funcionario de POLITÉCNICA. PARÁGRAFO PRIMERO: De acuerdo con lo establecido en el Anexo Técnico del Contrato de Aporte N° 681 de 2015, el presente Contrato no establece cláusula de permanencia. CLÁUSULA CUARTA. DERECHOS DEL USUARIO EN RELACIÓN CON LOS SERVICIOS PRESTADOS: De acuerdo con el artículo 10 de la resolución 3066 de 2011, son derechos de los usuarios: 1. Contar con la medición apropiada de sus consumos reales, mediante los instrumentos tecnológicos apropiados para efectuar dicha medición, dentro de los plazos fijados por la Comisión de Regulación de Comunicaciones CRC 2. Ejercer los derechos contenidos en el Régimen de Protección al USUARIO de los Servicios de Comunicaciones, establecido en la Resolución CRC 3066 de 2011 y demás normas que lo modifiquen, complementen, adicionen o deroguen; así como todos aquellos derechos que se deriven de las disposiciones contenidas en la regulación expedida por el Ministerio de Tecnologías de la Información y las Comunicaciones, la Comisión de Regulación de Comunicaciones y la Superintendencia de Industria y Comercio. 3. Tener fácil acceso de manera veraz, oportuna, clara, transparente, precisa, completa y gratuita de toda la información que necesite en relación con los servicios prestados. 4. Ser atendido por parte de POLITÉCNICA ágilmente y con calidad, cuando así lo requiera, a través de las oficinas físicas de Atención al Cliente, oficinas virtuales (página Web y red social) y la línea gratuita de atención al USUARIO. 5. A presentar fácilmente y sin requisitos innecesarios peticiones, quejas o recursos en los Centros de Atención al Cliente, oficinas virtuales (página Web y red social) y la línea gratuita de atención al USUARIO y, además, a recibir atención integral y respuesta oportuna ante cualquier clase de solicitud que presente a POLITÉCNICA  6. Recibir los servicios contratados de manera continua e ininterrumpida, salvo por circunstancias de fuerza mayor, caso fortuito o hecho de un tercero que impidan la prestación del servicio en condiciones normales; en caso de que el (los) servicios prestados por parte de POLITÉCNICA sean suspendidos por motivos de algún daño que impida la prestación del servicio por causas diferentes a fuerza mayor, caso fortuito o hecho de un tercero, POLITÉCNICA reparará el daño en la mayor brevedad posible, de tal forma que la prestación del servicio no se vea gravemente afectada. 7. Poder consultar en línea, a través de la página Web del Proyecto o la página Web de la Superintendencia de Industria y Comercio, el estado del trámite asociado a su petición, queja o recurso, y el tiempo exacto para obtener respuesta al mismo, según el caso. 8. Mantener las mismas condiciones acordadas en el contrato, sin que estas puedan ser modificadas de ninguna manera por parte de POLITÉCNICA sin su aceptación previa. 9.  Recibir un trato respetuoso por parte de POLITÉCNICA y el personal a su cargo. 10. Ser compensado cuando se presente y verifique la falta de disponibilidad de los servicios contratados, de acuerdo con lo dispuesto en la resolución 3066 de 2011, y en las normas que la adicionen, modifiquen o deroguen.  11. Tener acceso gratuito a la información de sus consumos, a través de la página Web del Proyecto y a través de la línea gratuita de atención al USUARIO. 12. Ser avisado en el plazo que determinen las normas vigentes, sobre los posibles reportes de información ante entidades de riesgos financieros, para poder aceptar o defenderse del eventual reporte 13. Gozar de una protección especial en cuanto al manejo confidencial y privado de los datos Personales que ha suministrado a POLITÉCNICA, así como al derecho a que dichos datos no sean utilizados por el proveedor para fines distintos a los autorizados por el USUARIO. 14. Disponer de su contrato por el medio de su elección, esto es, papel o medio electrónico, para poderlo consultar en cualquier momento. 15. Recibir información clara, oportuna, veraz, transparente, precisa, cierta, completa y gratuita que no induzca a error, para que pueda tomar decisiones informadas, respecto del servicio .';

    }

}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);
?>

