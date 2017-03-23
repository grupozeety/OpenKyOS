<?php

namespace reportes\plantillaInfoTecnica\entidad;

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
         * 4. Duplicidad en la Plantilla
         *
         */

        $this->validarDuplicidad();

        /**
         * 4.
         * Validar que no hayan nulos
         */

        $this->validarNulo();

        /**
         * 5.
         * Validar Numeros
         */

        $this->validarNumeros();

        /**
         * 6.
         * Validar Existencia Beneficiarios
         */

        if ($_REQUEST['funcionalidad'] == 2) {
            $this->validarInfoExistentesRegistro();
        } else {
            $this->validarInfoExistentes();
        }

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

    public function validarDuplicidad()
    {

        $conteo = array_count_values($this->mac_esclavo);

        foreach ($conteo as $key => $value) {

            if ($value > 1) {

                $mensaje = " El mac esclavo '" . $key . "' esta duplicado en la plantilla.";
                $this->escribir_log($mensaje);
                $this->error = true;

            }

        }

    }

    public function validarNumeros()
    {
        foreach ($this->datos_infotecnica as $key => $value) {

            if ($value['longitud'] != 'Sin Longitud') {

                if (!is_numeric($value['longitud']) && $value['longitud'] != 'NULL') {

                    $mensaje = " La longitud  " . $value['longitud'] . " no es valida dado que  la longitud debe ser númerica con decimales separados por coma.";
                    $this->escribir_log($mensaje);

                    $this->error = true;
                } elseif ($value['longitud'] < -77 || $value['longitud'] > -73) {
                    $mensaje = " La longitud   " . $value['longitud'] . " con respecto a la ubicación del nodo  no es valida dado que la longitud debe estar en un rango  de -77 y -73 ";
                    $this->escribir_log($mensaje);

                    $this->error = true;
                }

            }
            if ($value['latitud'] != 'Sin Latitud' && $value['latitud'] != 'NULL') {
                if (!is_numeric($value['latitud'])) {
                    $mensaje = " La latitud  " . $value['latitud'] . " no es valida dado que la latitud debe ser númerica  con decimales separados por coma.";
                    $this->escribir_log($mensaje);

                    $this->error = true;
                } elseif ($value['latitud'] > 10 || $value['latitud'] < 6) {
                    $mensaje = " La latitud  " . $value['latitud'] . " con respecto a la ubicación del nodo  no es valida dado que la latitud debe estar en un rango de 10 y 6 ";

                    $this->escribir_log($mensaje);

                    $this->error = true;
                }

            }

        }

    }

    public function validarInfoExistentes()
    {
        foreach ($this->datos_infotecnica as $key => $value) {

            if ($_REQUEST['proceso'] == 1) {
                $cadenaSql = $this->miSql->getCadenaSql('consultarExistenciaInfoHFC', $value);
                $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

                if (is_null($consulta)) {
                    $mensaje = $key . ". El registro no se encuentra en el sistema. No se puede actualizar.";
                    $this->escribir_log($mensaje);

                    $this->error = true;
                }
            } elseif ($_REQUEST['proceso'] == 2) {
                $cadenaSql = $this->miSql->getCadenaSql('consultarExistenciaInfoWMAN', $value);
                $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
                if (is_null($consulta)) {
                    $mensaje = $key . ". El registro no se encuentra en el sistema. No se puede actualizar.";
                    $this->escribir_log($mensaje);

                    $this->error = true;
                }
            } else {
                $mensaje = $key . ". Tipo de tecnologia Inválido.";
                $this->escribir_log($mensaje);
                $this->error = true;
            }
        }
    }
    public function validarInfoExistentesRegistro()
    {
        foreach ($this->datos_infotecnica as $key => $value) {
            if ($_REQUEST['tecnologia'] == 1) {
                $cadenaSql = $this->miSql->getCadenaSql('consultarExistenciaInfoHFC', $value);
                $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

                if (!is_null($consulta)) {
                    $mensaje = $key . ". El registro se encuentra en el sistema. No se puede duplicar.";
                    $this->escribir_log($mensaje);

                    $this->error = true;
                }
            } elseif ($_REQUEST['tecnologia'] == 2) {
                $cadenaSql = $this->miSql->getCadenaSql('consultarExistenciaInfoWMAN', $value);
                $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
                if (!is_null($consulta)) {
                    $mensaje = $key . ". El registro se encuentra en el sistema. No se puede duplicar.";
                    $this->escribir_log($mensaje);

                    $this->error = true;
                }
            } else {
                $mensaje = "Tipo de tecnologia Inválido.";
                $this->escribir_log($mensaje);
                $this->error = true;
            }
        }
    }
    public function validarNulo()
    {

        foreach ($this->datos_infotecnica as $key => $value) {
            // wMAN

            if (is_null($value['id_proyecto']) || is_null($value['proyecto'])) {
                $mensaje = $key . "Datos de Urbanización no pueden ser nulos.";
                $this->escribir_log($mensaje);
                $this->error = true;
            }

            if (is_null($value['codigo_cabecera'])) {
                $mensaje = $key . "El campo Código Nodo/Celda no puede ser vacío";
                $this->escribir_log($mensaje);
                $this->error = true;
            }

            if (is_null($value['codigo_nodo'])) {
                $mensaje = $key . ". El campo Código Nodo/Celda no puede ser vacío";
                $this->escribir_log($mensaje);
                $this->error = true;
            }

            if ($_REQUEST['tecnologia'] == 1) {

                if (isset($value['hfc_macmaster'])) {
                    if (is_null($value['hfc_macmaster'])) {
                        $mensaje = $key . ". Para HFC, MAC Master es Obligatorio.";
                        $this->escribir_log($mensaje);
                        $this->error = true;
                    }
                    if (is_null($value['hfc_ipmaster'])) {
                        $mensaje = $key . ". Para HFC, IP Master es Obligatorio.";
                        $this->escribir_log($mensaje);
                        $this->error = true;
                    }

                    if (is_null($value['hfc_maconu'])) {
                        $mensaje = $key . ". Para HFC, MAC ONU es Obligatorio.";
                        $this->escribir_log($mensaje);
                        $this->error = true;
                    }

                    if (is_null($value['hfc_iponu'])) {
                        $mensaje = $key . ". Para HFC, IP ONU es Obligatorio.";
                        $this->escribir_log($mensaje);
                        $this->error = true;
                    }
                    if ($value['macesclavo1'] === 0) {
                        $mensaje = $key . ". Para HFC, MAC Esclavo es Obligatorio.";
                        $this->escribir_log($mensaje);
                        $this->error = true;
                    }
                } else {
                    Redireccionador::redireccionar("ErrorTecnologia");
                }
            } elseif ($_REQUEST['tecnologia'] == 2) {

                if (isset($value['wman_maccelda'])) {
                    if (is_null($value['wman_maccelda'])) {
                        $mensaje = $key . ". Para wMan, MAC Celda es Obligatorio.";
                        $this->escribir_log($mensaje);
                        $this->error = true;
                    }

                    if (is_null($value['wman_ipcelda'])) {
                        $mensaje = $key . ". Para wMan, IP Celda es Obligatorio.";
                        $this->escribir_log($mensaje);
                        $this->error = true;
                    }

                    if (is_null($value['wman_nombresectorial'])) {
                        $mensaje = $key . ". Para wMan, Nombre Sectorial es Obligatorio.";
                        $this->escribir_log($mensaje);
                        $this->error = true;
                    }

                    if (is_null($value['wman_ipsmcelda'])) {
                        $mensaje = $key . ". Para wMan, IPSM Celda es Obligatorio.";
                        $this->escribir_log($mensaje);
                        $this->error = true;
                    }

                    if (is_null($value['wman_maccpecelda'])) {
                        $mensaje = $key . ". Para wMan, MAC CPE Celda es Obligatorio.";
                        $this->escribir_log($mensaje);
                        $this->error = true;
                    }
                    if ($value['macesclavo1'] === 0) {
                        $mensaje = $key . ". Para wMan, MAC Esclavo es Obligatorio.";
                        $this->escribir_log($mensaje);
                        $this->error = true;
                    }
                } else {
                    Redireccionador::redireccionar("ErrorTecnologia");
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

                $datos_infotecnica[$i]['codigo_nodo'] = $informacion->setActiveSheetIndex()->getCell('A' . $i)->getCalculatedValue();

                $datos_infotecnica[$i]['codigo_cabecera'] = $informacion->setActiveSheetIndex()->getCell('B' . $i)->getCalculatedValue();

                $datos_infotecnica[$i]['departamento'] = $informacion->setActiveSheetIndex()->getCell('C' . $i)->getCalculatedValue();

                $datos_infotecnica[$i]['municipio'] = $informacion->setActiveSheetIndex()->getCell('D' . $i)->getCalculatedValue();

                $datos_infotecnica[$i]['proyecto'] = $informacion->setActiveSheetIndex()->getCell('E' . $i)->getCalculatedValue();

                $datos_infotecnica[$i]['id_proyecto'] = $informacion->setActiveSheetIndex()->getCell('F' . $i)->getCalculatedValue();

                $datos_infotecnica[$i]['latitud'] = str_replace(',', '.', $informacion->setActiveSheetIndex()->getCell('G' . $i)->getCalculatedValue());

                $datos_infotecnica[$i]['longitud'] = str_replace(',', '.', $informacion->setActiveSheetIndex()->getCell('H' . $i)->getCalculatedValue());

                $datos_infotecnica[$i]['macesclavo1'] = (is_null($informacion->setActiveSheetIndex()->getCell('I' . $i)->getCalculatedValue())) ? 0 : strtolower(str_replace(':', '', $informacion->setActiveSheetIndex()->getCell('I' . $i)->getCalculatedValue()));

                $this->mac_esclavo[] = (is_null($informacion->setActiveSheetIndex()->getCell('I' . $i)->getCalculatedValue())) ? 0 : strtolower(str_replace(':', '', $informacion->setActiveSheetIndex()->getCell('I' . $i)->getCalculatedValue()));

                $datos_infotecnica[$i]['port'] = (!is_null($informacion->setActiveSheetIndex()->getCell('J' . $i)->getCalculatedValue())) ? $informacion->setActiveSheetIndex()->getCell('J' . $i)->getCalculatedValue() : 0;

                if ($_REQUEST['tecnologia'] == 1) {

                    $datos_infotecnica[$i]['tipo_tecnologia'] = '95';

                    $datos_infotecnica[$i]['hfc_macmaster'] = str_replace(':', '', $informacion->setActiveSheetIndex()->getCell('K' . $i)->getCalculatedValue());

                    $datos_infotecnica[$i]['hfc_ipmaster'] = $informacion->setActiveSheetIndex()->getCell('L' . $i)->getCalculatedValue();

                    $datos_infotecnica[$i]['hfc_maconu'] = str_replace(':', '', $informacion->setActiveSheetIndex()->getCell('M' . $i)->getCalculatedValue());

                    $datos_infotecnica[$i]['hfc_iponu'] = (!is_null($informacion->setActiveSheetIndex()->getCell('N' . $i)->getCalculatedValue())) ? $informacion->setActiveSheetIndex()->getCell('N' . $i)->getCalculatedValue() : 0;

                    $datos_infotecnica[$i]['hfc_machub'] = (!is_null($informacion->setActiveSheetIndex()->getCell('O' . $i)->getCalculatedValue())) ? str_replace(':', '', $informacion->setActiveSheetIndex()->getCell('O' . $i)->getCalculatedValue()) : 0;

                    $datos_infotecnica[$i]['hfc_iphub'] = (!is_null($informacion->setActiveSheetIndex()->getCell('P' . $i)->getCalculatedValue())) ? $informacion->setActiveSheetIndex()->getCell('P' . $i)->getCalculatedValue() : 0;

                    $datos_infotecnica[$i]['hfc_maccpe'] = str_replace(':', '', $informacion->setActiveSheetIndex()->getCell('Q' . $i)->getCalculatedValue());
                } elseif ($_REQUEST['tecnologia'] == 2) {

                    $datos_infotecnica[$i]['tipo_tecnologia'] = '96';

                    $datos_infotecnica[$i]['wman_maccelda'] = (!is_null($informacion->setActiveSheetIndex()->getCell('K' . $i)->getCalculatedValue())) ? str_replace(':', '', $informacion->setActiveSheetIndex()->getCell('K' . $i)->getCalculatedValue()) : 0;

                    $datos_infotecnica[$i]['wman_ipcelda'] = $informacion->setActiveSheetIndex()->getCell('L' . $i)->getCalculatedValue();

                    $datos_infotecnica[$i]['wman_nombrenodo'] = $informacion->setActiveSheetIndex()->getCell('M' . $i)->getCalculatedValue();

                    $datos_infotecnica[$i]['wman_nombresectorial'] = $informacion->setActiveSheetIndex()->getCell('N' . $i)->getCalculatedValue();

                    $datos_infotecnica[$i]['wman_ipswitchcelda'] = $informacion->setActiveSheetIndex()->getCell('O' . $i)->getCalculatedValue();

                    $datos_infotecnica[$i]['wman_macsmcelda'] = str_replace(':', '', $informacion->setActiveSheetIndex()->getCell('P' . $i)->getCalculatedValue());

                    $datos_infotecnica[$i]['wman_ipsmcelda'] = $informacion->setActiveSheetIndex()->getCell('Q' . $i)->getCalculatedValue();

                    $datos_infotecnica[$i]['wman_maccpecelda'] = str_replace(':', '', $informacion->setActiveSheetIndex()->getCell('R' . $i)->getCalculatedValue());
                } else {
                    Redireccionador::redireccionar("ErrorNoCargaInformacionHojaCalculo");
                }
            }

            unlink($this->archivo['ruta_archivo']);

            $this->datos_infotecnica = $datos_infotecnica;
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
