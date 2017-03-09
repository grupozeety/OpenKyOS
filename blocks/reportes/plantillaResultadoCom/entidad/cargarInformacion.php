<?php

namespace reportes\plantillaResultadoCom\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

require_once $ruta . "/plugin/PHPExcel/Classes/PHPExcel.php";

// require_once $ruta . "/plugin/PHPExcel/Classes/PHPExcel/Reader/Excel2007.php";

require_once $ruta . "/plugin/PHPExcel/Classes/PHPExcel/IOFactory.php";

include_once 'Redireccionador.php';
class FormProcessor
{
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $esteRecursoDB;
    public $rutaURL;
    public $rutaAbsoluta;
    public function __construct($lenguaje, $sql)
    {
        date_default_timezone_set('America/Bogota');

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

        $_REQUEST['tiempo'] = time();

        /**
         * 1.
         * Cargar Archivo en el Directorio
         */

        $this->cargarArchivos();

        /**
         * 2.
         * Cargar Informacion Hoja de Calculo
         */

        $this->cargarInformacionHojaCalculo();

        /**
         * 4.
         * Validar Existencia Contratos Beneficiarios
         */

        $this->validarContratosExistentes();

        /**
         * 5.
         * Validar Existencia Beneficiarios
         */

        $this->validarBeneficiariosExistentes();

        /**
         * 6.
         * Validar Existencia Acta de Servicio
         */

        $this->validarServiciosExistentes();

        /**
         * 6.
         * Procesar Información Beneficiarios
         */

        $this->procesarInformacionBeneficiario();

        /**
         * 7.
         * Crear Contrato
         */

        $this->informacionServicio();

        /**
         * 9.
         * Registrar Tarea o Proceso Masivo
         */

        $this->registroProceso();

        if (isset($this->proceso) && $this->proceso != null) {
            Redireccionador::redireccionar("ExitoRegistroProceso", $this->proceso);
        } else {
            Redireccionador::redireccionar("ErrorRegistroProceso");
        }
    }

    /**
     * Funcionalidades Específicas
     */
    public function registroProceso()
    {
        $arreglo_registro = array(
            'nombre_archivo' => $this->archivo['ruta_archivo'],
        );

        $cadenaSql = $this->miSql->getCadenaSql('registrarProceso', $arreglo_registro);
        $this->proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['id_proceso'];
    }
    public function informacionServicio()
    {
        foreach ($this->informacion_registrar as $key => $value) {
            $cadenaSql = $this->miSql->getCadenaSql('actualizarServicio', $value);
            $resultado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "registro");

            if ($resultado != true) {
                Redireccionador::redireccionar("ErrorActualizacion");
            }
        }

    }
    public function procesarInformacionBeneficiario()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionBeneficiario', $value['identificacion_beneficiario']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            $this->informacion_registrar[] = array(
                'id_beneficiario' => $consulta['id_beneficiario'],
                'resultado_vs' => $value['subida'],
                'resultado_vb' => $value['bajada'],
                'resultado_p1' => $value['latencia'],
                'observaciones_p1' => $value['pagina'],
                'resultado_tr2' => $value['tracert'],
                'resultado_tr1' => $value['estado'],
                'reporte_fallos' => $value['observaciones'],
                'acceso_reportando' => $value['tracert'],
                'paginas_visitadas' => $value['pagina'],
                'fecha' => $value['fecha_comisionamiento'],
            );
        }
    }

    public function validarBeneficiariosExistentes()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaBeneficiario', $value['identificacion_beneficiario']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta)) {

                Redireccionador::redireccionar("ErrorCreacionContratos");
            }
        }
    }
    public function validarContratosExistentes()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaContrato', $value['identificacion_beneficiario']);
            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta)) {
                Redireccionador::redireccionar("ErrorCreacionContratos");
            }
        }
    }
    public function validarServiciosExistentes()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExistenciaServicio', $value['identificacion_beneficiario']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta)) {
                Redireccionador::redireccionar("ErrorCreacionContratos");
            }

            if (isset($value['fecha_comisionamiento'])) {

                $date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
                $hiredate = $value['fecha_comisionamiento'];

                if (!preg_match($date_regex, $hiredate) && $value['fecha_comisionamiento'] != 'Sin Fecha') {

                    Redireccionador::redireccionar("ErrorCreacionContratos");

                }
            }

        }
    }

    public function validarNumeros()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            if (!is_numeric($value['subida'])) {

                Redireccionador::redireccionar("ErrorCreacionContratos");
            } elseif ($value['subida'] > 10 || $value['subida'] < 0.1) {

                Redireccionador::redireccionar("ErrorCreacionContratos");
            }

            if (!is_numeric($value['bajada'])) {

                Redireccionador::redireccionar("ErrorCreacionContratos");
            } elseif ($value['bajada'] > 10 || $value['bajada'] < 0.1) {
                Redireccionador::redireccionar("ErrorCreacionContratos");
            }

            if (!is_numeric($value['latencia'])) {

                Redireccionador::redireccionar("ErrorCreacionContratos");
            }

        }

    }

    public function cargarInformacionHojaCalculo()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);

        if (file_exists($this->archivo['ruta_archivo'])) {

            $hojaCalculo = \PHPExcel_IOFactory::createReader($this->tipo_archivo);
            $informacion = $hojaCalculo->load($this->archivo['ruta_archivo']);

            $informacion_general = $hojaCalculo->listWorksheetInfo($this->archivo['ruta_archivo']);

            {
                $total_filas = $informacion_general[0]['totalRows'];
            }

            if ($total_filas > 501) {
                Redireccionador::redireccionar("ErrorNoCargaInformacionHojaCalculo");
            }

            for ($i = 2; $i <= $total_filas; $i++) {

                $datos_beneficiario[$i]['identificacion_beneficiario'] = $informacion->setActiveSheetIndex()->getCell('A' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['fecha_comisionamiento'] = $informacion->setActiveSheetIndex()->getCell('B' . $i)->getFormattedValue();

                $datos_beneficiario[$i]['latencia'] = str_replace(',', '.', $informacion->setActiveSheetIndex()->getCell('C' . $i)->getCalculatedValue());

                $datos_beneficiario[$i]['tracert'] = $informacion->setActiveSheetIndex()->getCell('D' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['observaciones'] = $informacion->setActiveSheetIndex()->getCell('E' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['estado'] = $informacion->setActiveSheetIndex()->getCell('F' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['pagina'] = $informacion->setActiveSheetIndex()->getCell('G' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['subida'] = str_replace(',', '.', $informacion->setActiveSheetIndex()->getCell('H' . $i)->getCalculatedValue());

                $datos_beneficiario[$i]['bajada'] = str_replace(',', '.', $informacion->setActiveSheetIndex()->getCell('I' . $i)->getCalculatedValue());

            }

            $this->datos_beneficiario = $datos_beneficiario;

            unlink($this->archivo['ruta_archivo']);

        } else {
            Redireccionador::redireccionar("ErrorNoCargaInformacionHojaCalculo");
        }
    }
    public function cargarArchivos()
    {
        $archivo_datos = '';
        $archivo = $_FILES['archivo_informacion'];

        if ($archivo['error'] == 0) {

            switch ($archivo['type']) {
                case 'application/vnd.oasis.opendocument.spreadsheet':
                    $this->tipo_archivo = 'OOCalc';
                    break;

                case 'application/vnd.ms-excel':
                    $this->tipo_archivo = 'Excel5';
                    break;

                case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                    $this->tipo_archivo = 'Excel2007';
                    break;

                default:
                    Redireccionador::redireccionar("ErrorFormatoArchivo");
                    break;
            }

            $this->prefijo = substr(md5(uniqid(time())), 0, 6);
            /*
             * obtenemos los datos del Fichero
             */
            $tamano = $archivo['size'];
            $tipo = $archivo['type'];
            $nombre_archivo = str_replace(" ", "_", $archivo['name']);
            /*
             * guardamos el fichero en el Directorio
             */
            $ruta_absoluta = $this->rutaAbsoluta . "/entidad/archivos_validar/" . $this->prefijo . "_" . $nombre_archivo;

            $ruta_relativa = $this->rutaURL . " /entidad/archivos_validar/" . $this->prefijo . "_" . $nombre_archivo;

            $archivo['rutaDirectorio'] = $ruta_absoluta;

            if (!copy($archivo['tmp_name'], $ruta_absoluta)) {

                Redireccionador::redireccionar("ErrorCargarArchivo");
            }

            $this->archivo = array(
                'ruta_archivo' => str_replace("//", "/", $ruta_absoluta),
                'nombre_archivo' => $archivo['name'],
            );
        } else {
            Redireccionador::redireccionar("ErrorArchivoNoValido");
        }
    }
}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);
