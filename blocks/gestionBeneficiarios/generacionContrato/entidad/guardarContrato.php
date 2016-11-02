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

        /**
         *  1. CargarArchivos en el Directorio
         **/

        $this->cargarArchivos();

        /**
         *  2. Procesar Informacion Contrato
         **/

        $this->procesarInformacion();

        if ($this->registro_info_contrato) {
            Redireccionador::redireccionar("InsertoInformacionContrato");
        } else {
            Redireccionador::redireccionar("NoInsertoInformacionContrato");
        }

    }

    public function procesarInformacion() {

        if ($this->archivos_datos === '') {
            $url_firma_beneficiario = '';
            $url_firma_contratista = '';

        } else {

            $url_firma_beneficiario = $this->archivos_datos[1]['ruta_archivo'];
            $url_firma_contratista = $this->archivos_datos[0]['ruta_archivo'];

        }
        $clausulas = '
                Entre las siguientes partes a saber: LA CORPORACIÓN POLITÉCNICA NACIONAL DE COLOMBIA, en adelante POLITÉCNICA, entidad sin ánimo de lucro, domiciliada en la ciudad de Bogotá D.C, por una parte, y por la otra, la persona identificada como USUARIO, cuyos datos son los que aparecen registrados en el formato de solicitud de servicios N° _____ suscrito por él mismo, quien ha leído y aceptado en todos sus términos el presente documento, hemos convenido celebrar el presente CONTRATO DE PRESTACIÓN DE SERVICIOS DE COMUNICACIONES el cual se regirá por lo dispuesto en la ley 1341 de 2009, en la Resolución 3066 de 2011 expedida por la Comisión de Regulación de Comunicaciones, y en las normas que la adicionen, modifiquen o deroguen; y en especial, por las siguientes cláusulas: El USUARIO al realizar la acción de iniciar el (los) procedimiento (s) de suscripción para el (los) plan (es) del Servicio de Comunicaciones en la modalidad postpago y prepago, conforme aplique en el contrato suscrito, (en lo sucesivo el Servicio) a través del medio que POLITÉCNICA ponga a disposición del USUARIO; y al suministrar sus datos personales o de empresa, según sea persona natural o jurídica, se entiende que el USUARIO acuerda suscribirse a uno de los planes ofrecidos por POLITÉCNICA del Servicio y expresa su entera e incondicional aceptación, de ser aprobada su solicitud, a los términos y condiciones contenidos en el presente contrato y en los anexos que lo integran (en lo sucesivo denominado, el Contrato) para disponer del Servicio CLÁUSULA PRIMERA. OBJETO DEL CONTRATO. Este contrato tiene por objeto establecer las condiciones técnicas, jurídicas y económicas que regirán la prestación al USUARIO de servicios de comunicaciones, específicamente Internet fijo, en condiciones de calidad y eficiencia, a cambio de un precio en dinero, pactado de acuerdo con lo previsto en el Anexo Técnico del Contrato de Aporte N° 681 de 2015 y al documento de recomendaciones de la CRC “CONEXIONES DIGITALES Esquema para la implementación de subsidios e Incentivos para el acceso a Internet de Última milla”, la regulación y reglamentación que expidan la Comisión de Regulación de Comunicaciones, el Ministerio de Tecnologías de la Información y las Comunicaciones y la Superintendencia de Industria y Comercio, cada uno de ellos en la órbita de su competencia y las cláusulas del presente contrato, en el marco del Contrato de Aporte N° 681 de 2015. PARÁGRAFO PRIMERO: El USUARIO tiene derecho a elegir el medio a través del cual desea recibir copia del presente Contrato, es decir, de manera física o electrónica, de acuerdo con lo estipulado en el artículo 11.1 de la Resolución CRC 3066 de 2011. En caso que EL USUARIO elija que sea por medio electrónico, de todas formas tendrá el derecho a solicitar en cualquier momento, la entrega de la copia impresa, por una sola vez durante la vigencia del contrato. CLÁUSULA SEGUNDA. SERVICIOS CONTRATADOS, PRECIO Y FORMA DE PAGO: Los servicios que prestará POLITÉCNICA al USUARIO, serán definidos en el Formato de Solicitud de Servicios suscrito(s) por el USUARIO en el (los) cual (es) acepta los términos y condiciones de prestación de los mismos. El valor de los servicios contratados se establecerá de conformidad con las tarifas contenidas en el Anexo Técnico del Contrato de Aporte N° 681 de 2015 y al documento de recomendaciones de la CRC “CONEXIONES DIGITALES Esquema para la implementación de subsidios e Incentivos para el acceso a Internet de Última milla”. A dicha suma se le agregará el IVA en el porcentaje establecido por el Gobierno Nacional, según sea el caso. POLITÉCNICA realizará, por mensualidades vencidas, los cobros derivados de la prestación del servicio. Dichos valores se incorporarán en la factura de servicios que expida POLITÉCNICA. Los pagos se realizarán de acuerdo con las condiciones, plazos y sitios establecidos en las facturas expedidas por POLITÉCNICA. El no recibo de la factura no exime al USUARIO del pago de Servicio ni del recargo por mora que se genere.';

        $clausulas = str_replace("'", "", $clausulas);

        $arreglo = array(
            'nombres' => 'Stiv',
            'primer_apellido' => 'Verdugo',
            'segundo_apellido' => 'Carmona',
            'tipo_documento' => '1',
            'numero_identificacion' => '1032418288',
            'fecha_expedicion' => '2016-10-07',
            'direccion_domicilio' => 'calle con carrera',
            'direccion_instalacion' => 'Kr 80 73 F 72 Sur',
            'departamento' => '23',
            'municipio' => '23090',
            'urbanizacion' => '18',
            'estrato' => '3',
            'barrio' => '12123123',
            'telefono' => '3165306782',
            'celular' => '3118827465',
            'correo' => 'tabordaemmanuel@gmail.com',
            'cuenta_suscriptor' => '123123123123',
            'velocidad_internet' => '123123123',
            'fecha_inicio_vigencia_servicio' => '2016-10-13',
            'fecha_fin_vigencia_servicio' => '2016-10-20',
            'valor_mensual' => '123123123',
            'marca' => '123123',
            'modelo' => '12123',
            'serial' => '12123213',
            'tecnologia' => '12123123',
            'estado' => 'Excelente',
            'clausulas' => $clausulas,
            'url_firma_contratista' => $url_firma_contratista,
            'url_firma_beneficiario' => $url_firma_beneficiario,

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

}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);
?>

