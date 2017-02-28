<?php
namespace facturacion\impresionFactura\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

include_once 'Redireccionador.php';

class FormProcessor
{

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
    public function __construct($lenguaje, $sql)
    {

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

        /**
         * 1.Procesar Información Beneficiarios
         **/

        $this->procesarInformacionBeneficiario();

        $_REQUEST['tiempo'] = time();

        exit;

        if (!is_null($this->id_beneficiario_acta_portatil) && !is_null($this->id_beneficiario_acta_servicio)) {

            Redireccionador::redireccionar("ExitoActualizacionActas");
        } else {
            Redireccionador::redireccionar("ErrorCreacion");
        }

    }

    public function registroProceso()
    {

        $this->urbanizaciones = array_unique($this->urbanizaciones);
        $arreglo_registro = array(
            'nombre' => $this->arreglo_nombre,
            'inicio' => $this->id_beneficiario_acta_portatil[0],
            'final' => end($this->id_beneficiario_acta_portatil),
            'datos_adicionales' => implode(";", $this->id_beneficiario_acta_portatil),
            'urbanizaciones' => implode("<br>", $this->urbanizaciones),
        );

        $cadenaSql = $this->miSql->getCadenaSql('registrarProceso', $arreglo_registro);

        $this->proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['id_proceso'];

    }

    public function procesarInformacionBeneficiario()
    {

        $arreglo = array(
            'departamento' => $_REQUEST['departamento'],
            'municipio' => $_REQUEST['municipio'],
            'urbanizacion' => $_REQUEST['urbanizacion'],
            'estado_beneficiario' => $_REQUEST['estado_beneficiario'],
            'beneficiario' => $_REQUEST['beneficiario'],
            'estado_documento' => $_REQUEST['estado_documento'],
        );

        $cadenaSql = $this->miSql->getCadenaSql('consultaGeneralInformacion');
        $this->Informacion = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        if ($this->Informacion == false) {

            Redireccionador::redireccionar('SinResultado');
        }

        $descripcion = '';
        foreach ($this->Informacion as $key => $value) {

            $descripcion .= ($key + 1) . ". " . $value['departamento'] . ", " . $value['municipio'] . ", " . trim(str_replace("URBANIZACION", "", $value['urbanizacion'])) . "<br>";

        }

        $arreglo = array(
            'parametros' => base64_encode(json_encode($arreglo)),
            'descripcion' => $descripcion,
        );

        $cadenaSql = $this->miSql->getCadenaSql('crearProceso', $arreglo);

        $proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0][0];

        if ($proceso) {

            Redireccionador::redireccionar('exitoProceso', $proceso);
        } else {

            Redireccionador::redireccionar('errorProceso');
        }

    }
    public function procesarInformacionBeneficiaasdadrio()
    {

        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionBeneficiario', $value['identificacion_beneficiario']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            $this->urbanizaciones[] = $consulta['urbanizacion'];

            $this->informacion_registrar_portatil[] = array(
                'id_beneficiario' => $consulta['id_beneficiario'],
                'fecha_entrega' => $value['fecha_entrega_portatil'],
                'serial' => $value['serial_portatil'],
            );

            $this->informacion_registrar_acta_servicios[] = array(
                'id_beneficiario' => $consulta['id_beneficiario'],
                'mac_esc' => $value['mac_1'],
                'serial_esc' => $value['serial_esclavo'],
                'marca_esc' => $value['marca_esclavo'],
                'cant_esc' => $value['cantidad_esclavo'],
                'ip_esc' => $value['ip'],
                'mac_esc2' => $value['mac_2'],
            );

        }

    }

}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);
