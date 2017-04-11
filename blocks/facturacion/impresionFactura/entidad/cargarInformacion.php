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
    public $esteRecursoDB;
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

        // Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        /**
         * 1.Procesar Información Beneficiarios
         */

        $this->procesarInformacionBeneficiario();

        /**
         * 2.Validar Numeros de Factura Actuales
         */

        $this->validarNumerosFacturasActuales();

        /**
         * 3.Registrar Proceso
         */

        $this->registroProceso();

        if (!is_null($this->proceso)) {
            Redireccionador::redireccionar("ExitoRegistroProceso", $this->proceso);
        } else {
            Redireccionador::redireccionar("ErrorRegistroProceso");
        }
    }
    public function registroProceso()
    {

        $fechaOportuna = '&fecha_oportuna_pago=' . $_REQUEST['fecha_oportuna_pago'];

        if ($_REQUEST['correo'] == '1') {
            $datos_adicionales = implode(";", $this->Beneficiarios) . $fechaOportuna . '&correo';
        } elseif ($_REQUEST['correo'] == '0') {
            $datos_adicionales = implode(";", $this->Beneficiarios) . $fechaOportuna;
        }

        $arreglo_registro = array(
            'inicio' => $this->Beneficiarios[0],
            'final' => end($this->Beneficiarios),
            'datos_adicionales' => $datos_adicionales,
            'urbanizaciones' => implode("<br>", $this->Urbanizaciones),
        );

        $cadenaSql = $this->miSql->getCadenaSql('registrarProceso', $arreglo_registro);

        $this->proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['id_proceso'];
    }

    public function validarNumerosFacturasActuales()
    {

        $departamento_validar = ['FSU', 'FCO'];

        foreach ($departamento_validar as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarNumeracionFactura', $value);

            $numeracion_actual = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['numeracion'];

            if (!is_null($numeracion_actual) && $numeracion_actual < 500000) {
                switch ($value) {
                    case 'FSU':

                        $numero_beneficiarios_facturar = $this->contarBeneficiarioPorDepartamento('70');

                        break;

                    case 'FCO':
                        $numero_beneficiarios_facturar = $this->contarBeneficiarioPorDepartamento('23');
                        break;

                }

                if ((500000 - $numeracion_actual) < $numero_beneficiarios_facturar) {

                    Redireccionador::redireccionar("ErrorNumeroBeneficiariosFacturar");
                }

            } else if (!($numeracion_actual < 500000)) {

                echo "error_numero_fac";

                Redireccionador::redireccionar("ErrorNumeracionFacturacion");

            }

        }

    }

    public function contarBeneficiarioPorDepartamento($departamento)
    {
        $i = 0;
        foreach ($this->BeneficiariosValidar as $key => $value) {

            if ($value['departamento'] == $departamento) {

                $i++;

            }

        }

        return $i;

    }

    public function procesarInformacionBeneficiario()
    {
        $arreglo = array(
            'departamento' => $_REQUEST['departamento'],
            'municipio' => $_REQUEST['municipio'],
            'urbanizacion' => $_REQUEST['urbanizacion'],
            'beneficiario' => $_REQUEST['beneficiario'],
        )
        ;

        $cadenaSql = $this->miSql->getCadenaSql('consultaGeneralInformacion');
        $Beneficiarios = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $cadenaSql = $this->miSql->getCadenaSql('consultaGeneralInformacionUrbanizaciones');
        $Urbanizaciones = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        if ($Beneficiarios == false) {

            Redireccionador::redireccionar('SinResultado');
        }

        foreach ($Beneficiarios as $key => $value) {
            $this->Beneficiarios[] = trim($value['id_beneficiario']);
        }

        foreach ($Beneficiarios as $key => $value) {
            $this->BeneficiariosValidar[] = array(
                'id_beneficiario' => trim($value['id_beneficiario']),
                'departamento' => $value['departamento'],

            );
        }

        foreach ($Urbanizaciones as $key => $value) {
            $this->Urbanizaciones[] = trim($value['urbanizacion']);
        }
    }
}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);
