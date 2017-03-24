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
         * 6.
         * Procesar Información Beneficiarios
         */

        $this->procesarInformacionBeneficiario();

        /**
         * 7.
         * Actualizar o Registrar beneficiarios
         */

        $this->informacionBeneficiario();

        switch ($_REQUEST['funcionalidad']) {

            case '2':

                if (!is_null($this->resultado)) {

                    Redireccionador::redireccionar("ExitoRegistro");

                } else {
                    Redireccionador::redireccionar("ErrorCreacion");
                }

                break;

            case '3':
                if (!is_null($this->resultado)) {

                    Redireccionador::redireccionar("ExitoActualizacion");
                } else {
                    Redireccionador::redireccionar("ErrorCreacion");
                }

                break;

        }

    }

    /**
     * Funcionalidades Específicas
     */
    public function informacionBeneficiario()
    {
        foreach ($this->informacion_registrar as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('registroFamiliares', $value);
            $cadenaSql = str_replace(",)", ")", $cadenaSql);
            $this->resultado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        }

    }

    public function procesarInformacionBeneficiario()
    {

        foreach ($this->datos_beneficiario as $key => $value) {

            //
            $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionBeneficiario', $value['identificacion_beneficiario']);
            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            $this->informacion_registrar[] = array(
                'id_beneficiario' => $consulta['id_beneficiario'],
                'tipo_documento' => $value['tipo_identificacion_fm'],
                'identificacion_familiar' => $value['identificacion_fm'],
                'nombre_familiar' => $value['nombre_fm'],
                'primer_apellido_familiar' => $value['primer_apellido_fm'],
                'segundo_apellido_familiar' => $value['segundo_apellido_fm'],
                'parentesco' => $value['parentesco_fm'],
                'genero_familiar' => $value['genero_fm'],
                'edad_familiar' => $value['edad_fm'],
                'celular_familiar' => $value['celular_fm'],
                'nivel_estudio_familiar' => $value['nivel_estudio_fm'],
                'correo_familiar' => $value['correo_fm'],
                'pertenencia_etnica_familiar' => $value['pertencia_fm'],
                'institucion_educativa_familiar' => $value['institucion_edu_fm'],
                'ocupacion_familiar' => $value['ocupacion_fm'],
            );

        }

    }

    public function validarOtrosDatos()
    {

        foreach ($this->datos_beneficiario as $key => $value) {

            //Nombre ,Primer Apellido y Segundo Apellido

            if (isset($value['nombre_fm']) && isset($value['primer_apellido_fm']) && isset($value['segundo_apellido_fm'])) {

                if (is_null($value['nombre_fm'])) {

                    Redireccionador::redireccionar("ErrorCreacion");

                }

                if (is_null($value['primer_apellido_fm'])) {

                    Redireccionador::redireccionar("ErrorCreacion");

                }

                if (is_null($value['segundo_apellido_fm'])) {

                    Redireccionador::redireccionar("ErrorCreacion");

                }
            }

            // Correo

            if (isset($value['correo_fm'])) {

                if (is_null($value['correo_fm'])) {

                    Redireccionador::redireccionar("ErrorCreacion");

                }

            }

            //Parentesco Familiar
            if (isset($value['parentesco_fm']) && is_numeric($value['parentesco_fm'])) {

                if ($value['parentesco_fm'] != 0) {

                    if ($value['parentesco_fm'] < 1 || $value['parentesco_fm'] > 12) {
                        Redireccionador::redireccionar("ErrorCreacion");

                    }

                }

            } else {
                Redireccionador::redireccionar("ErrorCreacion");

            }

            //Genero Familiar
            if (isset($value['genero_fm']) && is_numeric($value['genero_fm'])) {

                if ($value['genero_fm'] != 0) {

                    if ($value['genero_fm'] < 1 || $value['genero_fm'] > 2) {

                        Redireccionador::redireccionar("ErrorCreacion");

                    }

                }

            } else {

                Redireccionador::redireccionar("ErrorCreacion");

            }

            //Edad Familiar
            if (isset($value['edad_fm']) && is_numeric($value['edad_fm'])) {

                if ($value['edad_fm'] != 0) {

                    if ($value['edad_fm'] < 1 || $value['edad_fm'] > 100) {

                        Redireccionador::redireccionar("ErrorCreacion");

                    }

                }

            } else {
                Redireccionador::redireccionar("ErrorCreacion");

            }

            //Nivel Estudio del Familiar
            if (isset($value['nivel_estudio_fm']) && is_numeric($value['nivel_estudio_fm'])) {

                if ($value['nivel_estudio_fm'] != 0) {

                    if ($value['nivel_estudio_fm'] < 1 || $value['nivel_estudio_fm'] > 9) {
                        Redireccionador::redireccionar("ErrorCreacion");

                    }

                }

            } else {

                Redireccionador::redireccionar("ErrorCreacion");

            }

            //Pertencia Etnica
            if (isset($value['pertencia_fm']) && is_numeric($value['pertencia_fm'])) {

                if ($value['pertencia_fm'] != 0) {

                    if ($value['pertencia_fm'] < 1 || $value['pertencia_fm'] > 5) {

                        Redireccionador::redireccionar("ErrorCreacion");

                    }

                }

            } else {

                Redireccionador::redireccionar("ErrorCreacion");

                $mensaje = null;
            }

            //Institucion del Familiar

            if (isset($value['institucion_edu_fm'])) {

                if (is_null($value['institucion_edu_fm'])) {
                    Redireccionador::redireccionar("ErrorCreacion");

                }

            }

            //Ocupación del Familiar
            if (isset($value['ocupacion_fm']) && is_numeric($value['ocupacion_fm'])) {

                if ($value['ocupacion_fm'] != 0) {

                    if ($value['ocupacion_fm'] < 1 || $value['ocupacion_fm'] > 30) {

                        Redireccionador::redireccionar("ErrorCreacion");

                    }

                }

            } else {

                Redireccionador::redireccionar("ErrorCreacion");

            }

        }
    }

    public function validarBeneficiariosExistentes()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaBeneficiario', $value['identificacion_beneficiario']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta)) {
                Redireccionador::redireccionar("ErrorCreacion");

            }
        }

    }

    public function validarDuplicidad()
    {

        $conteo_identificaciones = array_count_values($this->identificaciones_familiares);

        foreach ($conteo_identificaciones as $key => $value) {

            if ($value > 1) {
                Redireccionador::redireccionar("ErrorCreacion");
            }

        }

    }

    public function validarDuplicidadFamiliares()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            if (is_null($value['identificacion_fm'])) {
                Redireccionador::redireccionar("ErrorCreacion");
            } else {

                $cadenaSql = $this->miSql->getCadenaSql('consultarDuplicidadFamiliar', $value['identificacion_fm']);

                $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

                if ($consulta) {
                    Redireccionador::redireccionar("ErrorCreacion");
                }
            }
        }

    }

    public function validarExistenciaFamiliares()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            if (is_null($value['identificacion_fm'])) {
                Redireccionador::redireccionar("ErrorCreacion");
            } else {

                $arreglo = array(
                    'identificacion_familiar' => $value['identificacion_fm'],
                    'identificacion_beneficiario' => $value['identificacion_beneficiario'],
                );
            }

            $cadenaSql = $this->miSql->getCadenaSql('consultarExistenciaFamiliar', $arreglo);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (is_null($consulta)) {
                Redireccionador::redireccionar("ErrorCreacion");

            }
        }

    }

    public function validarBeneficiariosExistentesRegistro()
    {
        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaBeneficiario', $value['identificacion_beneficiario']);
            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (!is_null($consulta)) {
                Redireccionador::redireccionar("ErrorCreacion");

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

            $total_filas = $informacion_general[0]['totalRows'];

            if ($total_filas > 501) {
                Redireccionador::redireccionar("ErrorNoCargaInformacionHojaCalculo");
            }

            for ($i = 2; $i <= $total_filas; $i++) {

                $datos_beneficiario[$i]['identificacion_beneficiario'] = $informacion->setActiveSheetIndex()->getCell('A' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['tipo_identificacion_fm'] = $informacion->setActiveSheetIndex()->getCell('B' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['identificacion_fm'] = trim($informacion->setActiveSheetIndex()->getCell('C' . $i)->getCalculatedValue());

                $this->identificaciones_familiares[] = trim($informacion->setActiveSheetIndex()->getCell('C' . $i)->getCalculatedValue());

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
