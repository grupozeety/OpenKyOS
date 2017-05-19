<?php
namespace reportes\certificadoNoInternet\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

use reportes\certificadoNoInternet\entidad\Redireccionador;

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

        /**
         *  1. Procesar Informacion Contrato
         **/

        $this->procesarInformacion();

        if ($_REQUEST['firmaBeneficiario'] != '') {

            include_once "guardarDocumentoCertificacion.php";

        }

        if ($this->actualizacionCertificado) {
            Redireccionador::redireccionar("ActualizoInformacionCertificado");
        } else {
            Redireccionador::redireccionar("NoInsertoInformacionCertificado");
        }

    }

    public function procesarInformacion() {

        if ($_REQUEST['firmaBeneficiario'] === '') {
            $url_firma_beneficiario = '';

        } else {

            $url_firma_beneficiario = $_REQUEST['firmaBeneficiario'];

            //$url_firma_contratista = $this->archivos_datos[0]['ruta_archivo'];

        }

        $arreglo = array(
            'id_beneficiario' => $_REQUEST['id_beneficiario'],
            'nombres' => $_REQUEST['nombres'],
            'primer_apellido' => $_REQUEST['primer_apellido'],
            'segundo_apellido' => $_REQUEST['segundo_apellido'],
            'identificacion' => $_REQUEST['numero_identificacion'],
            'celular' => $_REQUEST['celular'],
            'ciudad_expedicion_identificacion' => $_REQUEST['ciudad'],
            'ciudad_firma' => $_REQUEST['ciudad_firma'],
            'ruta_firma' => $url_firma_beneficiario,
        );

        $cadenaSql = $this->miSql->getCadenaSql('actualizarCertificacion', $arreglo);

        $this->actualizacionCertificado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

    }

}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);
?>

