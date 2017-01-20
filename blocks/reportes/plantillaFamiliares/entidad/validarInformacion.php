<?php

namespace reportes\plantillaFamiliares\entidad;

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

        switch ($_REQUEST['funcionalidad']) {
            case '2':
            /**
             * 5.
             * Validar Existencia Beneficiarios
             */

                $this->validarDuplicidadFamiliares();
                break;

            case '3':

            /**
             * 5.
             * Validar Existencia Familiares
             */

                $this->validarExistenciaFamiliares();

                break;

        }

        /**
         * 6.
         * Validar Otros Datos
         */

        $this->validarOtrosDatos();

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

    public function validarOtrosDatos() {

        foreach ($this->datos_beneficiario as $key => $value) {

            //Nombre ,Primer Apellido y Segundo Apellido

            if (isset($value['nombre_fm']) && isset($value['primer_apellido_fm']) && isset($value['segundo_apellido_fm'])) {

                if (is_null($value['nombre_fm'])) {

                    $mensaje = " No exite nombre del familiar asociada al beneficiario " . $value['identificacion_beneficiario'] . ". Se Verifique el Nombre del Familiar.";
                    $this->escribir_log($mensaje);
                    $this->error = true;

                }

                if (is_null($value['primer_apellido_fm'])) {

                    $mensaje = " No exite primer apellido del familiar asociada al beneficiario " . $value['identificacion_beneficiario'] . ". Se Verifique el Primer Apellido del Familiar.";
                    $this->escribir_log($mensaje);
                    $this->error = true;

                }

                if (is_null($value['segundo_apellido_fm'])) {

                    $mensaje = " Segundo apellido del familiar asociada al beneficiario " . $value['identificacion_beneficiario'] . " no es valido. Se Verifique el Segundo Apellido del Familiar.";
                    $this->escribir_log($mensaje);
                    $this->error = true;

                }
            }

            // Correo

            if (isset($value['correo_fm'])) {

                if (is_null($value['correo_fm'])) {

                    $mensaje = "Correo del familiar no  valido asociado al beneficiario con identificación " . $value['identificacion_beneficiario'] . ". Se Verifique el Correo del Familiar.";
                    $this->escribir_log($mensaje);
                    $this->error = true;

                }

            }

            //Parentesco Familiar
            if (isset($value['parentesco_fm']) && is_numeric($value['parentesco_fm'])) {

                if ($value['parentesco_fm'] != 0) {

                    if ($value['parentesco_fm'] < 1 || $value['parentesco_fm'] > 12) {

                        $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario " . $value['identificacion_beneficiario'] . ", no es valido el parentesco familiar. Se sugiere  revisar el parentesco familiar concuerde con los parametros  en la hoja  \"Parametros\" de la Plantilla.";
                        $this->escribir_log($mensaje);
                        $this->error = true;

                    }

                }

            } else {

                $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario " . $value['identificacion_beneficiario'] . ", no es valido el parentesco familiar. Se sugiere   revisar el parentesco familiar concuerde con los parametros en la hoja  \"Parametros\" de la Plantilla y es un campo númerico.";
                $this->escribir_log($mensaje);
                $this->error = true;

            }

            //Genero Familiar
            if (isset($value['genero_fm']) && is_numeric($value['genero_fm'])) {

                if ($value['genero_fm'] != 0) {

                    if ($value['genero_fm'] < 1 || $value['genero_fm'] > 2) {

                        $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario " . $value['identificacion_beneficiario'] . ", no es valido el genero familiar. Se sugiere   revisar el genero familiar concuerde con los parametros en la hoja  \"Parametros\" de la Plantilla.";
                        $this->escribir_log($mensaje);
                        $this->error = true;

                    }

                }

            } else {

                $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario " . $value['identificacion_beneficiario'] . ", no es valido el genero familiar. Se sugiere  revisar el genero familiar concuerde con los parametros en la hoja  \"Parametros\" de la Plantilla y es un campo númerico.";
                $this->escribir_log($mensaje);
                $this->error = true;

            }

            //Edad Familiar
            if (isset($value['edad_fm']) && is_numeric($value['edad_fm'])) {

                if ($value['edad_fm'] != 0) {

                    if ($value['edad_fm'] < 1 || $value['edad_fm'] > 100) {

                        $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario " . $value['identificacion_beneficiario'] . ", no es valida la edad del familiar. Se sugiere  revisar la edad del familiar.";
                        $this->escribir_log($mensaje);
                        $this->error = true;

                    }

                }

            } else {

                $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario " . $value['identificacion_beneficiario'] . ", no es valido la edad del familiar. Se sugiere   revisar la edad familiar ya que es un campo númerico.";
                $this->escribir_log($mensaje);
                $this->error = true;

            }

            //Nivel Estudio del Familiar
            if (isset($value['nivel_estudio_fm']) && is_numeric($value['nivel_estudio_fm'])) {

                if ($value['nivel_estudio_fm'] != 0) {

                    if ($value['nivel_estudio_fm'] < 1 || $value['nivel_estudio_fm'] > 9) {

                        $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario " . $value['identificacion_beneficiario'] . ", no es valido el nivel de estudio del  familiar. Se sugiere revisar el nivel del estudio del familiar concuerde con los parametros  en la hoja  \"Parametros\" de la Plantilla.";
                        $this->escribir_log($mensaje);
                        $this->error = true;

                    }

                }

            } else {

                $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario " . $value['identificacion_beneficiario'] . ", no es valido el nivel de estudio del  familiar. Se sugiere que  revisar el nivel de estudio del  familiar concuerde con los parametros en la hoja  \"Parametros\" de la Plantilla y es un campo númerico.";
                $this->escribir_log($mensaje);
                $this->error = true;

            }

            //Pertencia Etnica
            if (isset($value['pertencia_fm']) && is_numeric($value['pertencia_fm'])) {

                if ($value['pertencia_fm'] != 0) {

                    if ($value['nivel_estudio_fm'] < 1 || $value['nivel_estudio_fm'] > 9) {

                        $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario " . $value['identificacion_beneficiario'] . ", no es valido la pertencia étnica. Se sugiere revisarla pertencia étnica concuerde con los parametros  en la hoja  \"Parametros\" de la Plantilla.";
                        $this->escribir_log($mensaje);
                        $this->error = true;

                    }

                }

            } else {

                $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario " . $value['identificacion_beneficiario'] . ", no es valido la pertencia étnica. Se sugiere revisar la pertencia étnica concuerde con los parametros en la hoja  \"Parametros\" de la Plantilla y es un campo númerico.";
                $this->escribir_log($mensaje);
                $this->error = true;

                $mensaje = null;
            }

            //Institucion del Familiar

            if (isset($value['institucion_edu_fm'])) {

                if (is_null($value['institucion_edu_fm'])) {

                    $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario " . $value['identificacion_beneficiario'] . ", no es validad la institución de educación del familiar. Sugerencia verifique el nombre de la intitución de educación del familiar.";
                    $this->escribir_log($mensaje);
                    $this->error = true;

                }

            }

            //Ocupación del Familiar
            if (isset($value['ocupacion_fm']) && is_numeric($value['ocupacion_fm'])) {

                if ($value['ocupacion_fm'] != 0) {

                    if ($value['ocupacion_fm'] < 1 || $value['ocupacion_fm'] > 30) {

                        $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario " . $value['identificacion_beneficiario'] . ", no es valida la ocupación del familiar. Se sugiere revisarla que la ocupación del familiar concuerde con los parametros  en la hoja  \"Parametros\" de la Plantilla.";
                        $this->escribir_log($mensaje);
                        $this->error = true;

                        var_dump($mensaje);

                    }

                }

            } else {

                $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario " . $value['identificacion_beneficiario'] . ", no es valida la ocupación del familiar. Se sugiere revisar la ocupación del familiar concuerde con los parametros en la hoja  \"Parametros\" de la Plantilla y es un campo númerico.";
                $this->escribir_log($mensaje);
                $this->error = true;

                $mensaje = null;
            }

        }
    }

    public function validarDuplicidadFamiliares() {
        foreach ($this->datos_beneficiario as $key => $value) {

            if (is_null($value['identificacion_fm'])) {
                $mensaje = " No exite relacionada identificacion del familiar asociada al beneficiario " . $value['identificacion_beneficiario'] . ". Se Verifique la Identificación del Familiar.";
                $this->escribir_log($mensaje);
                $this->error = true;

            } else {

                $cadenaSql = $this->miSql->getCadenaSql('consultarDuplicidadFamiliar', $value['identificacion_fm']);

                $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

                if ($consulta) {
                    $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario con identificación  " . $value['identificacion_beneficiario'] . ", esta asociada a otro beneficiario " . $consulta['identificacion_beneficiario'] . ". Se sugiere corregir la identificación del familiar y/o corregir el familiar asociado a esa identificación.";
                    $this->escribir_log($mensaje);

                    $this->error = true;

                }
            }
        }

    }

    public function validarExistenciaFamiliares() {
        foreach ($this->datos_beneficiario as $key => $value) {

            if (is_null($value['identificacion_fm'])) {
                $mensaje = " No exite relacionada identificacion del familiar asociada al beneficiario " . $value['identificacion_beneficiario'] . ". Se Verifique la Identificación del Familiar.";
                $this->escribir_log($mensaje);
                $this->error = true;

            } else {

                $arreglo = array(
                    'identificacion_familiar' => $value['identificacion_fm'],
                    'identificacion_beneficiario' => $value['identificacion_beneficiario'],
                );
            }

            $cadenaSql = $this->miSql->getCadenaSql('consultarExistenciaFamiliar', $arreglo);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta)) {
                $mensaje = " La identificación  del familiar " . $value['identificacion_fm'] . " asociada al beneficiario con identificación " . $value['identificacion_beneficiario'] . ", no es validad para actualizar. Se sugiere verificar que existe registrado la identificación del familiar asociado a ese Beneficiario";
                $this->escribir_log($mensaje);

                $this->error = true;

            }
        }

    }

    public function validarBeneficiariosExistentes() {
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
    public function validarBeneficiariosExistentesRegistro() {
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
    public function validarNulo() {
        foreach ($this->datos_beneficiario as $key => $value) {

            if ($value['estrato'] == 0) {
                $mensaje = " La identificación " . $value['identificacion_beneficiario'] . ", posee un estrato vacío.";
                $this->escribir_log($mensaje);
                $this->error = true;
            }

            if (is_null($value['identificacion_beneficiario'])) {
                $mensaje = "Existe un campo de identificación vacío. La identificación del beneficiario es obligatoria.";
                $this->escribir_log($mensaje);
                $this->error = true;
            }
        }
    }
    public function escribir_log($mensaje) {
        fwrite($this->log, $mensaje . PHP_EOL);
    }
    public function cerrar_log() {
        fclose($this->log);
    }
    public function creacion_log() {
        $prefijo = substr(md5(uniqid(time())), 0, 6);

        $this->ruta_absoluta_log = $this->rutaAbsoluta . "/entidad/logs/Log_documento_validacion_" . $prefijo . ".log";

        $this->ruta_relativa_log = $this->rutaURL . "/entidad/logs/Log_documento_validacion_" . $prefijo . ".log";

        $this->log = fopen($this->ruta_absoluta_log, "w");
    }
    public function cargarInformacionHojaCalculo() {
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

                $datos_beneficiario[$i]['tipo_identificacion_fm'] = $informacion->setActiveSheetIndex()->getCell('B' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['identificacion_fm'] = $informacion->setActiveSheetIndex()->getCell('C' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['nombre_fm'] = $informacion->setActiveSheetIndex()->getCell('D' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['primer_apellido_fm'] = $informacion->setActiveSheetIndex()->getCell('E' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['segundo_apellido_fm'] = $informacion->setActiveSheetIndex()->getCell('F' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['parentesco_fm'] = $informacion->setActiveSheetIndex()->getCell('G' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['genero_fm'] = $informacion->setActiveSheetIndex()->getCell('H' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['edad_fm'] = $informacion->setActiveSheetIndex()->getCell('I' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['celular_fm'] = $informacion->setActiveSheetIndex()->getCell('J' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['nivel_estudio_fm'] = $informacion->setActiveSheetIndex()->getCell('K' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['correo_fm'] = $informacion->setActiveSheetIndex()->getCell('L' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['pertencia_fm'] = $informacion->setActiveSheetIndex()->getCell('M' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['institucion_edu_fm'] = $informacion->setActiveSheetIndex()->getCell('N' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['ocupacion_fm'] = $informacion->setActiveSheetIndex()->getCell('O' . $i)->getCalculatedValue();

            }

            unlink($this->archivo['ruta_archivo']);

            $this->datos_beneficiario = $datos_beneficiario;
        } else {
            Redireccionador::redireccionar("ErrorNoCargaInformacionHojaCalculo");
        }

    }
    public function cargarArchivos() {
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
?>

