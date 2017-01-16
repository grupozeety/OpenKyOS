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
         *  1. Cargar Archivo en el Directorio
         **/

        $this->cargarArchivos();

        /**
         *  2. Cargar Informacion Hoja de Calculo
         **/

        $this->cargarInformacionHojaCalculo();

        /**
         *  4. Validar Existencia Contratos Beneficiarios
         **/

        $this->validarContratosExistentes();

        /**
         *  5. Validar Existencia Beneficiarios
         **/

        $this->validarBeneficiariosExistentes();

        switch ($_REQUEST['funcionalidad']) {
            case '3':

            /**
             *  5.1. Validar que no exitan actas a Actualizar
             **/
                $this->validarExistenciaActas();

            /**
             *  5.3. Validar existencia serial portatil
             **/
                $this->validarExistenciaSerialPortatil();

            /**
             *  5.1. Validar que no exitan registradas actas con lo seriales a registrar
             **/

                $this->validarDuplicidadPortatil();

            /**
             *  5.4. Validar duplicidad IP y MAC Esclavos
             **/
                $this->validarIPyMAC();

                break;

            default:

            /**
             *  5.1. Validar que no exitan registradas actas con lo seriales a registrar
             **/
                $this->validarDuplicidadPortatil();

            /**
             *  5.2. Validar que no exitan registradas actas con las identificaciones de los Beneficiaciarios
             **/
                $this->validarDuplicidadActa();

            /**
             *  5.3. Validar existencia serial portatil
             **/
                $this->validarExistenciaSerialPortatil();

            /**
             *  5.4. Validar duplicidad IP y MAC Esclavos
             **/
                $this->validarIPyMAC();

                break;

        }

        /**
         *  6. Validar otros Datos
         **/

        $this->validarOtrosDatos();

        /**
         *  6. Procesar Información Beneficiarios
         **/

        $this->procesarInformacionBeneficiario();

        /**
         *  7. Crear Actas
         **/

        $this->crearActas();

        switch ($_REQUEST['funcionalidad']) {
            case '1':

                if (!is_null($this->id_beneficiario_acta_portatil) && !is_null($this->id_beneficiario_acta_servicio)) {

                    Redireccionador::redireccionar("ExitoRegistroActas");

                } else {
                    Redireccionador::redireccionar("ErrorCreacion");
                }

                break;

            case '2':

            /**
             *  8. Parametrizacion Nombre Contrato
             **/

                $this->parametrizarNombre();

            /**
             *  9. Registrar Tarea o Proceso de Generación Pdf Contratos
             **/

                $this->registroProceso();

                if (isset($this->proceso) && $this->proceso != null) {
                    Redireccionador::redireccionar("ExitoRegistroProceso", $this->proceso);
                } else {
                    Redireccionador::redireccionar("ErrorRegistroProceso");
                }
                break;

            case '3':
                if (!is_null($this->id_beneficiario_acta_portatil) && !is_null($this->id_beneficiario_acta_servicio)) {

                    Redireccionador::redireccionar("ExitoActualizacionActas");
                } else {
                    Redireccionador::redireccionar("ErrorCreacion");
                }

                break;

        }

    }

    public function registroProceso() {
        $arreglo_registro = array(
            'nombre' => $this->arreglo_nombre,
            'inicio' => $this->id_beneficiario_acta_portatil[0],
            'final' => end($this->id_beneficiario_acta_portatil),
            'datos_adicionales' => implode(";", $this->id_beneficiario_acta_portatil),
        );

        $cadenaSql = $this->miSql->getCadenaSql('registrarProceso', $arreglo_registro);

        $this->proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['id_proceso'];

    }

    public function parametrizarNombre() {
        if (isset($this->datos_nombre_documento)) {

            foreach ($this->datos_nombre_documento as $key => $value) {

                switch ($value) {
                    case 'Numero Contrato':
                        $arreglo_nombre[] = 'numero_contrato';
                        break;

                    case 'Indentificación':
                        $arreglo_nombre[] = 'numero_identificacion';
                        break;

                    case 'Nombre Beneficiario':
                        $arreglo_nombre[] = 'nombres-primer_apellido-segundo_apellido';
                        break;

                    case 'Dirección':
                        $arreglo_nombre[] = 'direccion_domicilio';
                        break;

                    case 'Manzana':
                        $arreglo_nombre[] = 'manzana';
                        break;

                    case 'Bloque':
                        $arreglo_nombre[] = 'bloque';
                        break;

                    case 'Torre':
                        $arreglo_nombre[] = 'torre';
                        break;

                    case 'Casa/Apartamento':
                        $arreglo_nombre[] = 'casa_apartamento';
                        break;

                    case 'Interior':
                        $arreglo_nombre[] = 'interior';
                        break;

                    case 'Lote':
                        $arreglo_nombre[] = 'lote';
                        break;

                    case 'Piso':
                        $arreglo_nombre[] = 'piso';
                        break;

                }

            }

        } else {

            $arreglo_nombre[] = 'numero_contrato';
            $arreglo_nombre[] = 'numero_identificacion';
            $arreglo_nombre[] = 'nombres-primer_apellido-segundo_apellido';

        }

        $this->arreglo_nombre = base64_encode(implode("-", $arreglo_nombre));

    }

    public function crearActas() {

        foreach ($this->informacion_registrar_portatil as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('registrarActaPortatil', $value);

            $cadenaSql = str_replace(",)", ")", $cadenaSql);

            $this->id_beneficiario_acta_portatil[] = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['id_beneficiario'];

        }

        foreach ($this->informacion_registrar_acta_servicios as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('registrarActaServicios', $value);

            $cadenaSql = str_replace(",)", ")", $cadenaSql);

            $this->id_beneficiario_acta_servicio[] = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['id_beneficiario'];

        }

    }

    public function procesarInformacionBeneficiario() {

        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionBeneficiario', $value['identificacion_beneficiario']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

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

    public function validarExistenciaActas() {

        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaActaPortatil', $value['identificacion_beneficiario']);
            $consulta_acta_portatil = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta_acta_portatil)) {
                Redireccionador::redireccionar("ErrorCreacion");

            }

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaActaServicios', $value['identificacion_beneficiario']);
            $consulta_acta_servicios = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta_acta_servicios)) {

                Redireccionador::redireccionar("ErrorCreacion");

            }

        }

    }

    public function validarOtrosDatos() {

        foreach ($this->datos_beneficiario as $key => $value) {

            //Fecha Valida

            if (isset($value['fecha_entrega_portatil'])) {

                $date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
                $hiredate = $value['fecha_entrega_portatil'];

                if (!preg_match($date_regex, $hiredate) && $value['fecha_entrega_portatil'] != 'Sin Fecha') {
                    Redireccionador::redireccionar("ErrorCreacion");
                }
            }

            if (isset($value['cantidad_esclavo'])) {

                if (is_numeric($value['cantidad_esclavo'])) {

                    if ($value['cantidad_esclavo'] < 1) {
                        Redireccionador::redireccionar("ErrorCreacion");
                    }

                } elseif ($value['cantidad_esclavo'] != 'Sin Cantidad') {

                    Redireccionador::redireccionar("ErrorCreacion");
                }
            }

            $mensaje = null;
        }

    }

    public function validarIPyMAC() {

        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaIP', $value['ip']);

            $ip_beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (!is_null($ip_beneficiario) && $value['ip'] != 'Sin IP' && $value['identificacion_beneficiario'] != $ip_beneficiario['numero_identificacion']) {

                Redireccionador::redireccionar("ErrorCreacion");

            }

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaMac1', $value['mac_1']);

            $mac_1_beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (!is_null($mac_1_beneficiario) && $value['ip'] != 'Sin MAC 1' && $value['identificacion_beneficiario'] != $ip_beneficiario['numero_identificacion']) {

                Redireccionador::redireccionar("ErrorCreacion");

            }

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaMac2', $value['mac_2']);

            $mac_2_beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (!is_null($mac_2_beneficiario) && $value['ip'] != 'Sin MAC 2' && $value['identificacion_beneficiario'] != $ip_beneficiario['numero_identificacion']) {
                Redireccionador::redireccionar("ErrorCreacion");

            }

        }

    }

    public function validarDuplicidadActa() {

        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaActa', $value['identificacion_beneficiario']);
            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if ($consulta) {

                Redireccionador::redireccionar("ErrorCreacion");
            }

        }

    }

    public function validarExistenciaSerialPortatil() {

        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaSerialRegistrado', $value['serial_portatil']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta) && $value['serial_portatil'] != 'Sin Serial Portatil') {
                Redireccionador::redireccionar("ErrorCreacion");

            }

        }

    }

    public function validarDuplicidadPortatil() {

        foreach ($this->datos_beneficiario as $key => $value) {

            $arreglo = array(
                'identificacion' => $value['identificacion_beneficiario'],
                'serial_portatil' => $value['serial_portatil'],
            );

            if ($value['serial_portatil'] != 'Sin Serial Portatil') {
                $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaSerialPortatil', $arreglo);
                $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

                if ($consulta && $value['identificacion_beneficiario'] != $consulta['numero_identificacion']) {

                    Redireccionador::redireccionar("ErrorCreacion");
                }

            }

        }

    }

    public function validarBeneficiariosExistentes() {

        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaBeneficiario', $value['identificacion_beneficiario']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta)) {

                Redireccionador::redireccionar("ErrorCreacion");

            }

        }

    }

    public function validarContratosExistentes() {

        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaContrato', $value['identificacion_beneficiario']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta)) {

                Redireccionador::redireccionar("ErrorCreacion");

            }

        }

    }

    public function cargarInformacionHojaCalculo() {

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

                $datos_beneficiario[$i]['serial_portatil'] = $informacion->setActiveSheetIndex()->getCell('B' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['fecha_entrega_portatil'] = $informacion->setActiveSheetIndex()->getCell('C' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['mac_1'] = $informacion->setActiveSheetIndex()->getCell('D' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['mac_2'] = $informacion->setActiveSheetIndex()->getCell('E' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['serial_esclavo'] = $informacion->setActiveSheetIndex()->getCell('F' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['marca_esclavo'] = $informacion->setActiveSheetIndex()->getCell('G' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['cantidad_esclavo'] = $informacion->setActiveSheetIndex()->getCell('H' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['ip'] = $informacion->setActiveSheetIndex()->getCell('I' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['modelo_portatil'] = $informacion->setActiveSheetIndex()->getCell('J' . $i)->getCalculatedValue();

            }

            $this->datos_beneficiario = $datos_beneficiario;

            if (isset($informacion_general[1]) && $informacion_general['1']['worksheetName'] == 'Parametrización Nombre Actas') {
                {
                    //var_dump($informacion_general);exit;

                    $total_filas = $informacion_general[1]['totalRows'];

                }

                for ($i = 2; $i <= $total_filas; $i++) {

                    $datos_nombre_documento[$i] = $informacion->setActiveSheetIndex(1)->getCell('A' . $i)->getCalculatedValue();

                }
                $this->datos_nombre_documento = $datos_nombre_documento;
            }

            unlink($this->archivo['ruta_archivo']);

        } else {
            Redireccionador::redireccionar("ErrorNoCargaInformacionHojaCalculo");

        }

    }

    public function cargarArchivos() {

        $archivo_datos = '';
        $archivo = $_FILES['archivo_actas'];

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
?>

