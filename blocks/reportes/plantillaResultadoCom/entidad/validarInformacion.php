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
    public $archivos_datos;
    public $esteRecursoDB;
    public $datos_contrato;
    public $rutaURL;
    public $rutaAbsoluta;
    public $clausulas;
    public $registro_info_contrato;
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
         * 3.
         * Creación Log
         */

        $this->creacion_log();

        /**
         * 4.
         * Validar Existencia Beneficiarios
         */

        $this->validarBeneficiariosExistentes();

        /**
         * 5.
         * Validar Existencia Contratos Beneficiarios
         */

        $this->validarContratosExistentes();

        /**
         * 6.
         * Validar Existencia Acta de Servicio
         */

        $this->validarServiciosExistentes();

        /**
         * 7.
         * Cerrar Log
         */

        $this->cerrar_log();

        if (isset($this->error)) {
            Redireccionador::redireccionar("ErrorInformacionCargar", base64_encode($this->ruta_relativa_log));
        } else {
            Redireccionador::redireccionar("ExitoInformacion");
        }
    }
    public function validarOtrosDatos()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            $mensaje = null;
        }
    }
    public function validarDuplicidadActa()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaActa', $value['identificacion_beneficiario']);
            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if ($consulta) {

                $mensaje = " La identificación " . $value['identificacion_beneficiario'] . " asociada con el serial " . $value['serial_portatil'] . " no es validad dado que este serial ya esta asociado a un acta con el beneficiario de identifiación " . $consulta['numero_identificacion'] . ". Sugerencia relacione otro serial de portatil o corrija el acta registrada.";
                $this->escribir_log($mensaje);

                $this->error = true;
            }
        }
    }
    public function validarDuplicidadPortatil()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            $arreglo = array(
                'identificacion' => $value['identificacion_beneficiario'],
                'serial_portatil' => $value['serial_portatil'],
            );

            if ($value['serial_portatil'] != 'Sin Serial Portatil') {
                $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaSerialPortatil', $arreglo);
                $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

                if ($consulta) {

                    $mensaje = " La identificación " . $value['identificacion_beneficiario'] . " asociada con el serial " . $value['serial_portatil'] . " no es validad dado que este serial ya esta asociado a un acta con el beneficiario de identifiación " . $consulta['numero_identificacion'] . ". Sugerencia relacione otro serial de portatil o corrija el acta registrada.";
                    $this->escribir_log($mensaje);

                    $this->error = true;
                }
            }
        }
    }
    public function validarBeneficiariosExistentes()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaBeneficiario', $value['identificacion_beneficiario']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta)) {

                $mensaje = " La identificación " . $value['identificacion_beneficiario'] . ", no tiene asociado un beneficiario. Se sugiere registrarlo en el Sistema.";
                $this->escribir_log($mensaje);

                $this->error = true;
            }
        }
    }
    public function validarContratosExistentes()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaContrato', $value['identificacion_beneficiario']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta)) {

                $mensaje = " El beneficiario con identificación " . $value['identificacion_beneficiario'] . " no tiene un contrato asociado.Sugerencia registrar un contrato con la identificación asociada.";
                $this->escribir_log($mensaje);

                $this->error = true;
            }
        }
    }

    public function validarServiciosExistentes()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExistenciaServicio', $value['identificacion_beneficiario']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta)) {
                $mensaje = " El beneficiario con identificación " . $value['identificacion_beneficiario'] . " no tiene un equipo instalado. Sugerencia registrar el kit correspondiente.";
                $this->escribir_log($mensaje);

                $this->error = true;
            }

            if (isset($value['fecha_comisionamiento'])) {

                $date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
                $hiredate = $value['fecha_comisionamiento'];

                if (!preg_match($date_regex, $hiredate) && $value['fecha_entrega_portatil'] != 'Sin Fecha') {

                    $mensaje = " La fecha de comisionamiento  asosicado al beneficiario con identificación " . $value['identificacion_beneficiario'] . ", no es valida.Sugerencia verifique que la columna Fecha de entrega de portatil este en formato texto y con esl formato 'yyyy-mm-dd'.";
                    $this->escribir_log($mensaje);
                    $this->error = true;

                }
            }

        }
    }
    public function escribir_log($mensaje)
    {
        fwrite($this->log, $mensaje . PHP_EOL);
    }
    public function cerrar_log()
    {
        fclose($this->log);
    }
    public function creacion_log()
    {
        $prefijo = substr(md5(uniqid(time())), 0, 6);

        $this->ruta_absoluta_log = $this->rutaAbsoluta . "/entidad/logs/Log_documento_validacion_" . $prefijo . ".log";

        $this->ruta_relativa_log = $this->rutaURL . "/entidad/logs/Log_documento_validacion_" . $prefijo . ".log";

        $this->log = fopen($this->ruta_absoluta_log, "w");
    }
    public function cargarInformacionHojaCalculo()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);

        if (file_exists($this->archivo['ruta_archivo'])) {

            $hojaCalculo = \PHPExcel_IOFactory::createReader($this->tipo_archivo);
            $informacion = $hojaCalculo->load($this->archivo['ruta_archivo']);

            $informacion_general = $hojaCalculo->listWorksheetInfo($this->archivo['ruta_archivo']);

            $total_filas = $informacion_general[0]['totalRows'];

            if ($total_filas > 501) {
                Redireccionador::redireccionar("ErrorNoCargaInformacionHojaCalculo");
            }

            for ($i = 2; $i <= $total_filas; $i++) {

                $datos_beneficiario[$i]['identificacion_beneficiario'] = $informacion->setActiveSheetIndex()->getCell('A' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['fecha_comisionamiento'] = $informacion->setActiveSheetIndex()->getCell('B' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['latencia'] = $informacion->setActiveSheetIndex()->getCell('C' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['tracert'] = $informacion->setActiveSheetIndex()->getCell('D' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['estado'] = $informacion->setActiveSheetIndex()->getCell('E' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['pagina'] = $informacion->setActiveSheetIndex()->getCell('F' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['subida'] = $informacion->setActiveSheetIndex()->getCell('G' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['bajada'] = $informacion->setActiveSheetIndex()->getCell('H' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['observaciones'] = $informacion->setActiveSheetIndex()->getCell('I' . $i)->getCalculatedValue();
            }

            unlink($this->archivo['ruta_archivo']);

            $this->datos_beneficiario = $datos_beneficiario;
        } else {
            Redireccionador::redireccionar("ErrorNoCargaInformacionHojaCalculo");
        }
    }
    public function cargarArchivos()
    {
        $archivo_datos = '';
        $archivo = $_FILES['archivo_validacion'];

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
                    exit();
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
            $ruta_absoluta = $this->rutaAbsoluta . "entidad/archivos_validar/" . $this->prefijo . "_" . $nombre_archivo;
            $ruta_relativa = $this->rutaURL . "entidad/archivos_validar/" . $this->prefijo . "_" . $nombre_archivo;

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
