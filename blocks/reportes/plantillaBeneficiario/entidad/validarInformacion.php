<?php

namespace reportes\plantillaBeneficiario\entidad;

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
         * Validar Duplicidad
         */

        $this->validarDuplicidad();

        /**
         * 5.
         * Validar que no hayan nulos
         */

        $this->validarNulo();

        /**
         * 6.
         * Validar que no  Valores Númericos
         */

        $this->validarNumeros();

        /**
         * 7.
         * Validar Existencia Beneficiarios
         */

        if ($_REQUEST['funcionalidad'] == 2) {
            $this->validarBeneficiariosExistentesRegistro();
        } else {
            $this->validarBeneficiariosExistentes();
        }

        /**
         * 8.
         * Cerrar Log
         */

        $this->cerrar_log();

        if (isset($this->error)) {
            Redireccionador::redireccionar("ErrorInformacionCargar", base64_encode($this->ruta_relativa_log));
        } else {
            Redireccionador::redireccionar("ExitoInformacion");
        }
    }

    public function validarDuplicidad()
    {

        $conteo_identificaciones = array_count_values($this->identificaciones);

        foreach ($conteo_identificaciones as $key => $value) {

            if ($value > 1) {

                $mensaje = " La identificación '" . $key . "' esta duplicada en la plantilla.";
                $this->escribir_log($mensaje);
                $this->error = true;

            }

        }

    }

    public function validarNumeros()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            if ($value['longitud'] != 'Sin Longitud') {

                if (!is_numeric($value['longitud']) && $value['longitud'] != 'NULL') {

                    $mensaje = " La longitud con respecto a la ubicacion asociada a la identificación del beneficiario " . $value['identificacion_beneficiario'] . " no es valida dado que  la longitud debe ser númerica con decimales separados por coma.";
                    $this->escribir_log($mensaje);

                    $this->error = true;
                } elseif ($value['longitud'] < -77 || $value['longitud'] > -73) {
                    $mensaje = " La longitud con respecto a la ubicación de la identificación del beneficiario " . $value['identificacion_beneficiario'] . " no es valida dado que la longitud debe estar en un rango  de -77 y -73 ";
                    $this->escribir_log($mensaje);

                    $this->error = true;
                }

            }
            if ($value['longitud'] != 'Sin Latitud' && $value['latitud'] != 'NULL') {
                if (!is_numeric($value['latitud'])) {
                    $mensaje = " La latitud con respecto a la ubicación asociada a la identificación del beneficiario " . $value['identificacion_beneficiario'] . " no es valida dado que la latitud debe ser númerica  con decimales separados por coma.";
                    $this->escribir_log($mensaje);

                    $this->error = true;
                } elseif ($value['latitud'] > 10 || $value['latitud'] < 6) {
                    $mensaje = " La latitud con respecto a la ubicación de  la  identificación del beneficiario " . $value['identificacion_beneficiario'] . " no es valida dado que la latitud debe estar en un rango de 10 y 6 ";

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
    public function validarBeneficiariosExistentesRegistro()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaBeneficiario', $value['identificacion_beneficiario']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (!is_null($consulta)) {
                $mensaje = "La identificación " . $value['identificacion_beneficiario'] . ", ya se encuentra registrada en el sistema.";
                $this->escribir_log($mensaje);

                $this->error = true;
            }
        }
    }
    public function validarNulo()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            if ($value['estrato'] === 0) {
                $mensaje = " La identificación " . $value['identificacion_beneficiario'] . ", posee un estrato vacío.";
                $this->escribir_log($mensaje);
                $this->error = true;
            }

            if (is_null($value['identificacion_beneficiario']) || $value['identificacion_beneficiario'] == 'NULL') {
                $mensaje = "Existe un campo de identificación vacío o nulo. La identificación del beneficiario es obligatoria.";
                $this->escribir_log($mensaje);
                $this->error = true;
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

            if ($total_filas > 1001) {
                Redireccionador::redireccionar("ErrorNoCargaInformacionHojaCalculo");
            }

            for ($i = 2; $i <= $total_filas; $i++) {

                $datos_beneficiario[$i]['departamento'] = $informacion->setActiveSheetIndex()->getCell('A' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['municipio'] = $informacion->setActiveSheetIndex()->getCell('B' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['id_proyecto'] = $informacion->setActiveSheetIndex()->getCell('C' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['proyecto'] = $informacion->setActiveSheetIndex()->getCell('D' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['tipo_beneficiario'] = $informacion->setActiveSheetIndex()->getCell('E' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['tipo_documento'] = $informacion->setActiveSheetIndex()->getCell('F' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['identificacion_beneficiario'] = trim($informacion->setActiveSheetIndex()->getCell('G' . $i)->getCalculatedValue());

                $this->identificaciones[] = trim($informacion->setActiveSheetIndex()->getCell('G' . $i)->getCalculatedValue());

                $datos_beneficiario[$i]['nombre'] = $informacion->setActiveSheetIndex()->getCell('H' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['primer_apellido'] = $informacion->setActiveSheetIndex()->getCell('I' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['segundo_apellido'] = $informacion->setActiveSheetIndex()->getCell('J' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['genero'] = (!is_null($informacion->setActiveSheetIndex()->getCell('K' . $i)->getCalculatedValue())) ? $informacion->setActiveSheetIndex()->getCell('K' . $i)->getCalculatedValue() : 0;

                $datos_beneficiario[$i]['edad'] = (!is_null($informacion->setActiveSheetIndex()->getCell('L' . $i)->getCalculatedValue())) ? $informacion->setActiveSheetIndex()->getCell('L' . $i)->getCalculatedValue() : 0;

                $datos_beneficiario[$i]['nivel_estudio'] = (!is_null($informacion->setActiveSheetIndex()->getCell('M' . $i)->getCalculatedValue())) ? $informacion->setActiveSheetIndex()->getCell('M' . $i)->getCalculatedValue() : 0;

                $datos_beneficiario[$i]['correo'] = $informacion->setActiveSheetIndex()->getCell('N' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['telefono'] = (!is_null($informacion->setActiveSheetIndex()->getCell('O' . $i)->getCalculatedValue())) ? $informacion->setActiveSheetIndex()->getCell('O' . $i)->getCalculatedValue() : 0;

                $datos_beneficiario[$i]['direccion'] = $informacion->setActiveSheetIndex()->getCell('P' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['manzana'] = $informacion->setActiveSheetIndex()->getCell('Q' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['bloque'] = $informacion->setActiveSheetIndex()->getCell('R' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['torre'] = $informacion->setActiveSheetIndex()->getCell('S' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['casa_apto'] = $informacion->setActiveSheetIndex()->getCell('T' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['interior'] = $informacion->setActiveSheetIndex()->getCell('U' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['lote'] = $informacion->setActiveSheetIndex()->getCell('V' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['piso'] = (!is_null($informacion->setActiveSheetIndex()->getCell('W' . $i)->getCalculatedValue())) ? $informacion->setActiveSheetIndex()->getCell('W' . $i)->getCalculatedValue() : 0;

                $datos_beneficiario[$i]['minvivienda'] = (!is_null($informacion->setActiveSheetIndex()->getCell('X' . $i)->getCalculatedValue())) ? $informacion->setActiveSheetIndex()->getCell('X' . $i)->getCalculatedValue() : 'FALSE';

                $datos_beneficiario[$i]['barrio'] = $informacion->setActiveSheetIndex()->getCell('Y' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['estrato'] = (!is_null($informacion->setActiveSheetIndex()->getCell('Z' . $i)->getCalculatedValue())) ? $informacion->setActiveSheetIndex()->getCell('Z' . $i)->getCalculatedValue() : 0;

                $datos_beneficiario[$i]['longitud'] = str_replace(',', '.', $informacion->setActiveSheetIndex()->getCell('AA' . $i)->getCalculatedValue());
                $datos_beneficiario[$i]['latitud'] = str_replace(',', '.', $informacion->setActiveSheetIndex()->getCell('AB' . $i)->getCalculatedValue());
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
