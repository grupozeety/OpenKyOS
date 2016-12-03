<?php
namespace gestionBeneficiarios\generarContratosMasivos\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

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

        switch ($_REQUEST['tipo_beneficiario']) {

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
            'fecha_expedicion' => " ",
            'direccion_domicilio' => $_REQUEST['direccion_domicilio'],
            'direccion_instalacion' => '',
            'departamento' => $_REQUEST['departamento'],
            'municipio' => $_REQUEST['municipio'],
            'urbanizacion' => $_REQUEST['urbanizacion'],
            'estrato' => $_REQUEST['tipo_beneficiario'],
            'estrato_socioeconomico' => $_REQUEST['estrato_economico'],
            'barrio' => "",
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
            'casa_apartamento' => $_REQUEST['num_apto_casa'],
            'interior' => $_REQUEST['interior'],
            'lote' => $_REQUEST['lote'],
            'tipo_tecnologia' => $_REQUEST['tipo_tecnologia'],
            'valor_tarificacion' => $valor_tarificacion,
            'medio_pago' => $_REQUEST['medio_pago'],
            'tipo_pago' => $_REQUEST['tipo_pago'],
            'soporte' => $soporte,
            'nombre_comisionador' => $_REQUEST['nombre_comisionador'],
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

