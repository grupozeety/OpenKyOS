<?php
namespace reportes\generarActasMasivos\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

require_once $ruta . "/plugin/PHPExcel/Classes/PHPExcel.php";

//require_once $ruta . "/plugin/PHPExcel/Classes/PHPExcel/Reader/Excel2007.php";

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
         *  1. Cargar Archivo en el Directorio
         **/

        $this->cargarArchivos();

        /**
         *  2. Cargar Informacion Hoja de Calculo
         **/

        $this->cargarInformacionHojaCalculo();

        /**
         *  3. Creación Log
         **/

        $this->creacion_log();

        /**
         * 4. Duplicidad en la Plantilla
         *
         */

        $this->validarDuplicidad();

        /**
         *  5. Validar Existencia Beneficiarios
         **/

        $this->validarBeneficiariosExistentes();

        /**
         *  6. Validar Existencia Contratos Beneficiarios
         **/

        $this->validarContratosExistentes();

        switch ($_REQUEST['funcionalidad']) {
            case '3':

                /**
                 *  6.1. Validar que no exitan actas a Actualizar
                 **/
                $this->validarExistenciaActas();

                /**
                 *  6.2. Validar existencia serial portatil
                 **/
                $this->validarExistenciaSerialPortatil();

                /**
                 *  6.3. Validar que no exitan registradas actas con lo seriales a registrar
                 **/

                $this->validarDuplicidadPortatil();

                /**
                 *  6.4. Validar duplicidad IP y MAC Esclavos
                 **/
                $this->validarIPyMAC();

                break;

            default:

                /**
                 *  6.1. Validar que no exitan registradas actas con lo seriales a registrar
                 **/
                $this->validarDuplicidadPortatil();

                /**
                 *  6.2. Validar que no exitan registradas actas con las identificaciones de los Beneficiaciarios
                 **/
                $this->validarDuplicidadActa();

                /**
                 *  6.3. Validar existencia serial portatil
                 **/
                $this->validarExistenciaSerialPortatil();

                /**
                 *  6.4. Validar duplicidad IP y MAC Esclavos
                 **/
                $this->validarIPyMAC();

                break;

        }

        /**
         *  7. Validar otros Datos
         **/

        $this->validarOtrosDatos();

        /**
         *  8. Cerrar Log
         **/

        $this->cerrar_log();

        if (isset($this->error)) {
            Redireccionador::redireccionar("ErrorInformacionCargar", base64_encode($this->ruta_relativa_log));
        } else {
            Redireccionador::redireccionar("ExitoInformacion");
        }

    }

    public function validarDuplicidad()
    {
        /**
         *Validación Seriales Portatiles
         **/
        foreach ($this->serial_portatil as $key => $value) {

            if ($value == 'Sin Serial Portatil') {

                unset($this->serial_portatil[$key]);

            }

            if ($value == 'NULL') {

                unset($this->serial_portatil[$key]);

            }

        }

        if (!empty($this->serial_portatil)) {

            $conteo_serial_portatil = array_count_values($this->serial_portatil);

            foreach ($conteo_serial_portatil as $key => $value) {

                if ($value > 1) {

                    $mensaje = " El serial portatil '" . $key . "' esta duplicado en la plantilla.";
                    $this->escribir_log($mensaje);
                    $this->error = true;

                }

            }

        }

        /**
         *Validación Mac Esclavo 1
         **/

        foreach ($this->mac_esclavo as $key => $value) {

            if ($value == 'Sin MAC 1') {

                unset($this->mac_esclavo[$key]);

            }

            if ($value == 'NULL' || $value == 'null') {

                unset($this->mac_esclavo[$key]);

            }

        }

        if (!empty($this->mac_esclavo)) {

            $conteo_mac = array_count_values($this->mac_esclavo);

            foreach ($conteo_mac as $key => $value) {

                if ($value > 1) {

                    $mensaje = " El MAC 1  '" . $key . "' esta duplicado en la plantilla.";
                    $this->escribir_log($mensaje);
                    $this->error = true;

                }

            }

        }

    }

    public function validarExistenciaActas()
    {

        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaActaPortatil', $value['identificacion_beneficiario']);

            $consulta_acta_portatil = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta_acta_portatil)) {

                $mensaje = " La identificación " . $value['identificacion_beneficiario'] . " no tiene asociada una acta de entrega de portatil. Sugerencia registre un acta de entrega de portatil para el beneficiario.";
                $this->escribir_log($mensaje);

                $this->error = true;

            }

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaActaServicios', $value['identificacion_beneficiario']);

            $consulta_acta_servicios = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta_acta_servicios)) {

                $mensaje = " La identificación " . $value['identificacion_beneficiario'] . " no tiene asociada una acta de servicios. Sugerencia registre un acta de servicios para el beneficiario.";
                $this->escribir_log($mensaje);

                $this->error = true;

            }

        }

    }

    public function validarOtrosDatos()
    {

        foreach ($this->datos_beneficiario as $key => $value) {

            //Fecha Valida

            if (isset($value['fecha_entrega_portatil'])) {

                $date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
                $hiredate = $value['fecha_entrega_portatil'];

                if (!preg_match($date_regex, $hiredate) && $value['fecha_entrega_portatil'] != 'Sin Fecha' && $value['fecha_entrega_portatil'] != 'NULL') {

                    $mensaje = " La fecha de entrega de portatil  asosicado al beneficiario con identificación " . $value['identificacion_beneficiario'] . ", no es valida.Sugerencia verifique que la columna Fecha de entrega de portatil este en formato texto y con esl formato 'yyyy-mm-dd'.";
                    $this->escribir_log($mensaje);
                    $this->error = true;

                }
            }

            //Fecha Valida

            if (isset($value['fecha_instalacion'])) {

                $date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
                $hiredate = $value['fecha_instalacion'];

                if (!preg_match($date_regex, $hiredate) && $value['fecha_instalacion'] != 'Sin Fecha' && $value['fecha_instalacion'] != 'NULL') {

                    $mensaje = " La fecha de instalación asosicada al beneficiario con identificación " . $value['identificacion_beneficiario'] . ", no es valida.Sugerencia verifique que la columna Fecha de entrega de portatil este en formato texto y con esl formato 'yyyy-mm-dd'.";
                    $this->escribir_log($mensaje);
                    $this->error = true;

                }
            }

            if (isset($value['cantidad_esclavo'])) {

                if (is_numeric($value['cantidad_esclavo'])) {

                    if ($value['cantidad_esclavo'] < 1) {

                        $mensaje = " La cantidad del esclavo   asosicado al beneficiario con identificación " . $value['identificacion_beneficiario'] . ", no es valida.Sugerencia verifique que sea mayor o igual  a 1 .";
                        $this->escribir_log($mensaje);
                        $this->error = true;

                    }

                } elseif ($value['cantidad_esclavo'] != 'Sin Cantidad' && $value['cantidad_esclavo'] != 'NULL') {

                    $mensaje = " La cantidad del esclavo  asosicado al beneficiario con identificación " . $value['identificacion_beneficiario'] . ", no es valida.Sugerencia verifique que sea un campo númerico.";
                    $this->escribir_log($mensaje);
                    $this->error = true;

                }
            }

            if (isset($value['marca_portatil']) && isset($value['modelo_portatil'])) {

                if ($value['serial_portatil'] == 'Sin Serial Portatil' && $value['serial_portatil'] != 'NULL' && $value['marca_portatil'] != 'Hewlett Packard' && $value['marca_portatil'] != 'NULL') {

                    $mensaje = " La marca y el modelo del portatil asosicado al beneficiario con identificación " . $value['identificacion_beneficiario'] . ", no es valido.Sugerencia verifique marca y modelo portatil .";
                    $this->escribir_log($mensaje);
                    $this->error = true;

                }

                if ($value['serial_portatil'] == 'Sin Serial Portatil' && $value['serial_portatil'] != 'NULL' && $value['modelo_portatil'] != 'HP 245 G4 Notebook PC' && $value['marca_portatil'] != 'NULL') {

                    $mensaje = " La marca y el modelo del portatil asosicado al beneficiario con identificación " . $value['identificacion_beneficiario'] . ", no es valido.Sugerencia verifique marca y modelo portatil .";
                    $this->escribir_log($mensaje);
                    $this->error = true;

                }

            }

            $mensaje = null;
        }

    }

    public function validarIPyMAC()
    {

        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaIP', $value['ip']);

            $ip_beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (!is_null($ip_beneficiario) && $value['ip'] != 'Sin IP' && $value['ip'] != 'NULL' && $value['identificacion_beneficiario'] != $ip_beneficiario['numero_identificacion']) {

                $mensaje = " La IP del esclavo " . $value['ip'] . " que esta relacionado con la identificación  " . $value['identificacion_beneficiario'] . " ya existe relacionada a otro beneficiario. Sugerencia verifique y corriga la IP del Esclavo .";

                $this->escribir_log($mensaje);

                $this->error = true;

            }

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaMac1', $value['mac_1']);

            $mac_1_beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (!is_null($mac_1_beneficiario) && $value['mac_1'] != 'Sin MAC 1' && $value['mac_1'] != 'NULL' && $value['identificacion_beneficiario'] != $ip_beneficiario['numero_identificacion']) {

                $mensaje = " La Mac del esclavo 1 \"" . $value['mac_1'] . "\" que esta relacionado con la identificación  " . $value['identificacion_beneficiario'] . " ya existe relacionada a otro beneficiario con identificación " . $mac_1_beneficiario['numero_identificacion'] . " perteneciente al proyecto " . $mac_1_beneficiario['urbanizacion'] . ". Sugerencia verifique y corriga la Mac del Esclavo 1 .";

                $this->escribir_log($mensaje);

                $this->error = true;

            }

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaMac2', $value['mac_2']);

            $mac_2_beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (!is_null($mac_2_beneficiario) && $value['mac_2'] != 'Sin MAC 2' && $value['mac_2'] != 'NULL' && $value['identificacion_beneficiario'] != $ip_beneficiario['numero_identificacion']) {

                $mensaje = " La Mac del esclavo 2  \"" . $value['mac_2'] . "\" que esta relacionado con la identificación  " . $value['identificacion_beneficiario'] . " ya existe relacionada a otro beneficiario con identificación " . $mac_2_beneficiario['numero_identificacion'] . " perteneciente al proyecto " . $mac_2_beneficiario['urbanizacion'] . ". Sugerencia verifique y corriga la Mac del Esclavo 2 .";

                $this->escribir_log($mensaje);

                $this->error = true;

            }

        }

    }

    public function validarExistenciaSerialPortatil()
    {

        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaSerialRegistrado', $value['serial_portatil']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta) && $value['serial_portatil'] != 'Sin Serial Portatil' && $value['serial_portatil'] != 'NULL') {

                $mensaje = " El serial  del portatil " . $value['serial_portatil'] . " no existe en la base de datos  el cual esta relacionado con la identificación  " . $value['identificacion_beneficiario'] . " . Sugerencia verifique serial de portatil o crear la referencia del mismo con el serial inexistente .";

                $this->escribir_log($mensaje);

                $this->error = true;

            }

        }

    }

    public function validarDuplicidadActa()
    {

        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaActa', $value['identificacion_beneficiario']);
            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if ($consulta) {

                $mensaje = " La identificación " . $value['identificacion_beneficiario'] . " ya tiene asociada un acta . Sugerencia verifique el beneficiario o actualize el beneficiario en la Opcion de Funcionalidad  \"Actualización Registros Actas\".";
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

            if ($value['serial_portatil'] != 'Sin Serial Portatil' && $value['serial_portatil'] != 'NULL') {
                $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaSerialPortatil', $arreglo);
                $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

                if ($consulta && $value['identificacion_beneficiario'] != $consulta['numero_identificacion']) {

                    $mensaje = " La identificación " . $value['identificacion_beneficiario'] . " asociada con el serial " . $value['serial_portatil'] . " no es validad dado que este serial ya esta asociado a un acta con el beneficiario de identificación " . $consulta['numero_identificacion'] . ". Sugerencia relacione otro serial de portatil o corrija el acta registrada.";
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

                $mensaje = " La identificación " . $value['identificacion_beneficiario'] . ", no tiene asociado ningun beneficiario. Sugerencia registrarlo en el Sistema.";
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

            //$documento = \PHPExcel_IOFactory::load($this->archivo['ruta_archivo']);

            //$this->informacion = $documento->getActiveSheet()->toArray(null, true, true, true);

            //unset($this->informacion[1]);

            $hojaCalculo = \PHPExcel_IOFactory::createReader($this->tipo_archivo);
            $informacion = $hojaCalculo->load($this->archivo['ruta_archivo']);
            //var_dump($informacion);die;

            //$hoja_1 = $informacion->getActiveSheet();
            //var_dump($hoja_1);

            $informacion_general = $hojaCalculo->listWorksheetInfo($this->archivo['ruta_archivo']);

            {

                $total_filas = $informacion_general[0]['totalRows'];

            }

            if ($total_filas > 500) {

                Redireccionador::redireccionar("ErrorNoCargaInformacionHojaCalculo");
            }

            for ($i = 2; $i <= $total_filas; $i++) {

                $datos_beneficiario[$i]['identificacion_beneficiario'] = $informacion->setActiveSheetIndex()->getCell('A' . $i)->getCalculatedValue();

                $serial_portatil = $informacion->setActiveSheetIndex()->getCell('B' . $i)->getCalculatedValue();

                $serial_portatil = strtoupper($serial_portatil);

                $datos_beneficiario[$i]['serial_portatil'] = $serial_portatil;

                $this->serial_portatil[] = $serial_portatil;

                $datos_beneficiario[$i]['fecha_entrega_portatil'] = $informacion->setActiveSheetIndex()->getCell('C' . $i)->getCalculatedValue();

                $mac_1 = $informacion->setActiveSheetIndex()->getCell('D' . $i)->getCalculatedValue();

                $mac_1 = ($mac_1 != 'Sin MAC 1') ? strtolower(str_replace(":", "", $mac_1)) : $mac_1;

                $datos_beneficiario[$i]['mac_1'] = $mac_1;

                $this->mac_esclavo[] = $mac_1;

                $mac_2 = $informacion->setActiveSheetIndex()->getCell('E' . $i)->getCalculatedValue();

                $mac_2 = ($mac_2 != 'Sin MAC 2') ? strtolower(str_replace(":", "", $mac_2)) : $mac_2;

                $datos_beneficiario[$i]['mac_2'] = $mac_2;

                $datos_beneficiario[$i]['serial_esclavo'] = $informacion->setActiveSheetIndex()->getCell('F' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['marca_esclavo'] = $informacion->setActiveSheetIndex()->getCell('G' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['cantidad_esclavo'] = $informacion->setActiveSheetIndex()->getCell('H' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['ip'] = $informacion->setActiveSheetIndex()->getCell('I' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['marca_portatil'] = $informacion->setActiveSheetIndex()->getCell('J' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['modelo_portatil'] = $informacion->setActiveSheetIndex()->getCell('K' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['fecha_instalacion'] = $informacion->setActiveSheetIndex()->getCell('L' . $i)->getCalculatedValue();

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
                    exit;
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
