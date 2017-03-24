<?php
namespace gestionBeneficiarios\generarContratosMasivos\entidad;

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
    public $urbanizaciones = null;
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
         *  3. Validar Duplicidad Plantilla
         **/

        $this->validarDuplicidad();

        /**
         *  4. Validar Existencia Contratos Beneficiarios
         **/

        $this->validarContratosExistentes();

        /**
         *  5. Validar Existencia Beneficiarios
         **/

        $this->validarBeneficiariosExistentes();

        /**
         *  6. Validar otros Datos
         **/

        $this->validarOtrosDatos();

        /**
         *  6. Procesar Información Beneficiarios
         **/

        $this->procesarInformacionBeneficiario();

        /**
         *  7. Crear Contrato
         **/

        $this->crearContrato();

        /**
         *  8. Parametrizacion Nombre Contrato
         **/

        $this->parametrizarNombreContratos();

        /**
         *  9. Registrar Tarea o Proceso de Generación Pdf Contratos
         **/

        $this->registroProceso();

        if (isset($this->proceso) && $this->proceso != null) {
            Redireccionador::redireccionar("ExitoRegistroProceso", $this->proceso);
        } else {
            Redireccionador::redireccionar("ErrorRegistroProceso");
        }

    }

    public function registroProceso()
    {

        $this->urbanizaciones = array_unique($this->urbanizaciones);

        $arreglo_registro = array(
            'nombre_contrato' => $this->arreglo_nombre,
            'contrato_inicio' => $this->contrato[0],
            'contrato_final' => end($this->contrato),
            'urbanizaciones' => implode("<br>", $this->urbanizaciones),
        );

        $cadenaSql = $this->miSql->getCadenaSql('registrarProceso', $arreglo_registro);

        $this->proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['id_proceso'];

    }

    public function parametrizarNombreContratos()
    {
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

                    case 'Nombre Comisionador':
                        $arreglo_nombre[] = 'nombre_comisionador';
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

    public function crearContrato()
    {

        foreach ($this->informacion_registrar as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('registrarContrato', $value);

            $cadenaSql = str_replace(",)", ")", $cadenaSql);

            $this->contrato[] = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['numero_contrato'];

        }

    }

    public function procesarInformacionBeneficiario()
    {

        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionBeneficiario', $value['identificacion_beneficiario']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            $this->urbanizaciones[] = $consulta['proyecto'];

            switch ($consulta['tipo_beneficiario']) {

                case '1':
                    $valor_tarificacion = '6500';
                    break;

                case '2':

                    $valor_tarificacion = '0';

                    if ($value['estrato_socioeconomico'] == '1') {
                        $valor_tarificacion = '12600';
                    } elseif ($value['estrato_socioeconomico'] == '2') {
                        $valor_tarificacion = '17600';
                    }

                    break;

                case '3':
                    $valor_tarificacion = $value['valor_tarificacion'];
                    break;

            }

            $this->informacion_registrar[] = array(
                'id_beneficiario' => $consulta['id_beneficiario'],
                'estado_contrato' => "82",
                'nombre' => $consulta['nombre'],
                'primer_apellido' => $consulta['primer_apellido'],
                'segundo_apellido' => $consulta['segundo_apellido'],
                'tipo_documento' => "1",
                'identificacion' => $consulta['identificacion'],
                'direccion_domicilio' => $value['direccion'],
                'direccion_instalacion' => $value['direccion'],
                'departamento' => $consulta['nombre_departamento'],
                'municipio' => $consulta['nombre_municipio'],
                'urbanizacion' => $consulta['proyecto'],
                'estrato' => $consulta['tipo_beneficiario'],
                'telefono' => $value['telefono'],
                'celular' => $value['celular'],
                'correo' => $value['correo'],
                'velocidad_internet' => "4",
                'valor_mensual' => "6500",
                'tecnologia' => $value['tipo_tecnologia'],
                'estado' => "TRUE",
                'usuario' => "administrador",
                'manzana' => $value['manzana'],
                'bloque' => $value['bloque'],
                'torre' => $value['torre'],
                'casa_apartamento' => $value['casa_apartamento'],
                'tipo_tecnologia' => $value['tipo_tecnologia'],
                'valor_tarificacion' => $valor_tarificacion,
                'interior' => $value['interior'],
                'lote' => $value['lote'],
                'piso' => $value['piso'],
                'nombre_comisionador' => $value['nombre_comisionador'],
                'fecha_contrato' => $value['fecha_contrato'],
                'estrato_socioeconomico' => $value['estrato_socioeconomico'],
                'barrio' => $value['barrio'],

            );

        }

    }

    public function validarDuplicidad()
    {

        $conteo_identificaciones = array_count_values($this->identificaciones);

        foreach ($conteo_identificaciones as $key => $value) {

            if ($value > 1) {

                Redireccionador::redireccionar("ErrorCreacionContratos");

            }

        }

    }

    public function validarOtrosDatos()
    {

        foreach ($this->datos_beneficiario as $key => $value) {

            //Fecha Valida

            if ($value['fecha_contrato']) {

                $date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
                $hiredate = $value['fecha_contrato'];

                if (!preg_match($date_regex, $hiredate)) {
                    Redireccionador::redireccionar("ErrorCreacionContratos");

                }
            }

            //Tipo de Tecnologia

            if ($value['tipo_tecnologia']) {

                if (!is_numeric($value['tipo_tecnologia'])) {
                    Redireccionador::redireccionar("ErrorCreacionContratos");

                }
                if ($value['tipo_tecnologia'] != '94' && $value['tipo_tecnologia'] != '95' && $value['tipo_tecnologia'] != '96') {
                    Redireccionador::redireccionar("ErrorCreacionContratos");

                }

            }

            if ($value['estrato_socioeconomico']) {

                if (!is_numeric($value['estrato_socioeconomico']) && $value['estrato_socioeconomico'] != 'Estrato No Clasificado') {
                    Redireccionador::redireccionar("ErrorCreacionContratos");
                }

                if ($value['estrato_socioeconomico'] != '1' && $value['estrato_socioeconomico'] != '2' && $value['estrato_socioeconomico'] != 'Estrato No Clasificado') {
                    Redireccionador::redireccionar("ErrorCreacionContratos");
                }

            }

            //Validar Barrio

            if ($value['barrio'] != 'Sin Barrio') {

                if (is_null($value['barrio']) || $value['barrio'] == '') {

                    Redireccionador::redireccionar("ErrorCreacionContratos");
                }

            }

            $mensaje = null;
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

            if ($consulta) {

                Redireccionador::redireccionar("ErrorCreacionContratos");

            }

        }

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

                $datos_beneficiario[$i]['identificacion_beneficiario'] = trim($informacion->setActiveSheetIndex()->getCell('A' . $i)->getCalculatedValue());

                $this->identificaciones[] = trim($informacion->setActiveSheetIndex()->getCell('A' . $i)->getCalculatedValue());

                $datos_beneficiario[$i]['telefono'] = $informacion->setActiveSheetIndex()->getCell('B' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['celular'] = $informacion->setActiveSheetIndex()->getCell('C' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['correo'] = $informacion->setActiveSheetIndex()->getCell('D' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['direccion'] = $informacion->setActiveSheetIndex()->getCell('E' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['manzana'] = $informacion->setActiveSheetIndex()->getCell('F' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['bloque'] = $informacion->setActiveSheetIndex()->getCell('G' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['torre'] = $informacion->setActiveSheetIndex()->getCell('H' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['casa_apartamento'] = $informacion->setActiveSheetIndex()->getCell('I' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['interior'] = $informacion->setActiveSheetIndex()->getCell('J' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['lote'] = $informacion->setActiveSheetIndex()->getCell('K' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['piso'] = $informacion->setActiveSheetIndex()->getCell('L' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['nombre_comisionador'] = $informacion->setActiveSheetIndex()->getCell('M' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['fecha_contrato'] = $informacion->setActiveSheetIndex()->getCell('N' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['tipo_tecnologia'] = $informacion->setActiveSheetIndex()->getCell('O' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['estrato_socioeconomico'] = $informacion->setActiveSheetIndex()->getCell('P' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['barrio'] = $informacion->setActiveSheetIndex()->getCell('Q' . $i)->getCalculatedValue();

            }

            $this->datos_beneficiario = $datos_beneficiario;

            if (isset($informacion_general[1]) && $informacion_general['1']['worksheetName'] == 'Parametrización Nombre Contrato') {
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

    public function cargarArchivos()
    {

        $archivo_datos = '';
        $archivo = $_FILES['archivo_contratos'];

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
