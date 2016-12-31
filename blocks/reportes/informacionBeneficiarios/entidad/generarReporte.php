<?php
namespace reportes\informacionBeneficiarios\entidad;

include_once 'Redireccionador.php';

class GenerarReporteInstalaciones {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $proyectos;
    public $proyectos_general;
    public $directorio_archivos;

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $_REQUEST['tiempo'] = time();

        $conexion = "interoperacion";

        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        /**
         * 0. Estrucurar Información Reporte
         **/
        $this->estruturarProyectos();

        /**
         * 1. Creación Directorio
         **/

        $this->crearDirectorio();

    }

    public function estruturarProyectos() {

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionBeneficiario');
        $this->beneficiarios = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        if ($this->beneficiarios == false) {

            Redireccionador::redireccionar('SinResultado');
        }

        ini_set('xdebug.var_display_max_depth', 5);
        ini_set('xdebug.var_display_max_children', 256);
        ini_set('xdebug.var_display_max_data', 1024);

        if (isset($_REQUEST['estado_beneficiario'])) {

            switch ($_REQUEST['estado_beneficiario']) {

                case '2':
                    var_dump(count($this->beneficiarios));
                    foreach ($this->beneficiarios as $key => $value) {

                        $cadenaSql = $this->miSql->getCadenaSql('verificarDocumentos', $value['id_beneficiario']);

                        $documentos_Beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

                        if ($documentos_Beneficiario != '19') {

                            unset($this->beneficiarios[$key]);

                        }
                    }

                    $var = count($this->beneficiarios);

                    if ($var == 0) {

                        Redireccionador::redireccionar('SinResultado');

                    }

                    break;

            }

        }

        foreach ($this->beneficiarios as $key => $value) {

            // Cantidad de personas que pertenecen al género femenino
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidadMujeresHogar', $value['id_beneficiario']);
            $numero_mujeres = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['numero_mujeres'] = $numero_mujeres;

            // Cantidad de personas que pertenecen al género masculino
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidadMasculinoHogar', $value['id_beneficiario']);
            $numero_hombres = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['numero_hombres'] = $numero_hombres;

            // Cantidad de personas mayores < 18 años
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidadMenores18', $value['id_beneficiario']);
            $numero_pers_mn_18 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['personas_menores_18'] = $numero_pers_mn_18;

            // Cantidad de personas  18 a 25 años
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidad18y25', $value['id_beneficiario']);
            $numero_pers_18_25 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['personas_18_25'] = $numero_pers_18_25;

            // Cantidad de personas 26 a 30 años
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidad26y30', $value['id_beneficiario']);
            $numero_pers_26_30 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['personas_26_30'] = $numero_pers_26_30;

            // Cantidad de personas 31 a 40 años
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidad31y40', $value['id_beneficiario']);
            $numero_pers_31_40 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['personas_31_40'] = $numero_pers_31_40;

            // Cantidad de personas 41 a 65 años
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidad41y65', $value['id_beneficiario']);
            $numero_pers_41_65 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['personas_41_65'] = $numero_pers_41_65;

            // Cantidad de personas mayor 65 años
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidadMayor65', $value['id_beneficiario']);
            $numero_pers_my_65 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['personas_my_65'] = $numero_pers_my_65;

            // Cantidad de personas ocupacion trabajo Informal
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidadEmpleado', $value['id_beneficiario']);
            $numero_trabajo_empleado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['personas_trabajo_empleado'] = $numero_trabajo_empleado;

            // Cantidad de personas ocupacion trabajo Informal
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidadTrabajoInformal', $value['id_beneficiario']);
            $numero_trabajo_formal = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['personas_trabajo_informal'] = $numero_trabajo_formal;

            // Cantidad de personas ocupacion estudiante
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidadEstudiante', $value['id_beneficiario']);
            $numero_trabajo_estudiante = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['personas_estudiante'] = $numero_trabajo_estudiante;

            // Cantidad de personas ocupacion trabajo Independiente
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidadTrabajoIndependiente', $value['id_beneficiario']);
            $numero_trabajo_independiente = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['personas_trabajo_independiente'] = $numero_trabajo_independiente;

            // Cantidad de personas ocupacion trabajo hogar Domestico
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidadHogarDomestico', $value['id_beneficiario']);
            $numero_trabajo_hogar_domestico = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['personas_trabajo_hogar_domestico'] = $numero_trabajo_hogar_domestico;

            // Cantidad de personas ocupacion trabajo hogar Domestico en el hogar
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidadHogarDomesticoCasa', $value['id_beneficiario']);
            $numero_trabajo_hogar_domestico_casa = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['personas_trabajo_hogar_domestico_casa'] = $numero_trabajo_hogar_domestico_casa;

            // Cantidad de personas ocupacion no trabaja
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidadNoTrabaja', $value['id_beneficiario']);
            $numero_trabajo_no = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['personas_trabajo_no'] = $numero_trabajo_no;

            // Cantidad de personas ocupacion otro
            $cadenaSql = $this->miSql->getCadenaSql('consultaCantidadOtro', $value['id_beneficiario']);
            $numero_otro = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['count'];

            $this->beneficiarios[$key]['personas_trabajo_otro'] = $numero_otro;

        }

    }

    public function directorio_beneficiario($ruta) {
        $directorio_beneficiario = $ruta . "/" . $value['id_beneficiario'];

        mkdir($directorio_beneficiario, 0777, true);
        chmod($directorio_beneficiario, 0777);
    }

    public function analizarCrearDirectorio($directorio) {

        if (!is_dir($directorio)) {
            $partes = explode("/", $directorio);

            $conteo = count($partes);

            unset($partes[$conteo - 1]);

            $url = implode("/", $partes);

            if (!is_dir($url)) {

                mkdir($url, 0777, true);
                chmod($url, 0777);

                mkdir($directorio, 0777, true);
                chmod($directorio, 0777);

            }

        }

    }

    public function crearDirectorioArchivosBeneficiarios() {

        foreach ($this->beneficiarios as $key => $value) {
            $directorio_municipio = $this->ruta_dir_archivos . "/" . $value['municipio'];

            $nombre = trim(str_replace("URBANIZACION", "", $value['urbanizacion']));

            $directorio_urbanizacion = $directorio_municipio . "/" . $nombre;

            $this->analizarCrearDirectorio($directorio_urbanizacion);

            $directorio_beneficiario = $directorio_urbanizacion . "/" . $value['id_beneficiario'];

            mkdir($directorio_beneficiario, 0777);
            chmod($directorio_beneficiario, 0777);

            $cadenaSql = $this->miSql->getCadenaSql('consultaDocumentosBeneficiarios', $value['id_beneficiario']);
            $documentos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            if ($documentos) {
                foreach ($documentos as $llave => $valor) {
                    /*
                    if (!copy($this->directorio_archivos . $valor['nombre_documento'], $directorio_beneficiario . "/" . $valor['nombre_documento'])) {
                    echo "Error al copiar $fichero...\n";

                    }*/

                    @copy($this->directorio_archivos . $valor['nombre_documento'], $directorio_beneficiario . "/" . $valor['nombre_documento']);
                }

            }

            if (!is_null($value['nombre_documento_contrato'])) {
                @copy($this->directorio_archivos . "/contratos/" . $value['nombre_documento_contrato'], $directorio_beneficiario . "/" . $value['nombre_documento_contrato']);
            }

        }

    }

    public function crearDirectorio() {

        /**
         * 1. Crear Directorio
         **/

        $this->directorio_archivos = $this->rutaAbsoluta . "/archivos/";

        $this->rutaURLArchivo = $this->rutaURL . "/archivos/archivosDescargaAccesos";
        $this->ruta_dir = $this->rutaAbsoluta . "/archivos/archivosDescargaAccesos";

        $this->nombre_dir = "paqueteAccesos" . time();
        $this->ruta_dir = $this->ruta_dir . "/" . $this->nombre_dir;

        mkdir($this->ruta_dir, 0777, true);
        chmod($this->ruta_dir, 0777);

        $this->nombre_dir = "Accesos";
        $this->ruta_dir_archivos = $this->ruta_dir . "/" . $this->nombre_dir;

        mkdir($this->ruta_dir_archivos, 0777, true);
        chmod($this->ruta_dir_archivos, 0777);

        /**
         * 2. Crear Documento Hoja de Calculo(Reporte)
         **/

        $this->crearHojaCalculo();

        /**
         * 3. Crear Directorios Archivos Beneficiarios
         **/

        $this->crearDirectorioArchivosBeneficiarios();
        exit;
        /**
         * 3. Comprimir Directorio
         **/
        $this->nombre_archivo_zip = $this->comprimir($this->ruta_dir, $this->nombre_dir_actas, $this->nombre_dir_actas);

        /**
         * 4. Eliminar Archivos No Necesarios
         **/
        $this->eliminarDirectorioContenido($this->ruta_dir_actas);

        /**
         * 4. Redireccionar
         **/

        $arreglo = array(
            "nombre_archivo" => $this->nombre_archivo_zip,
            "rutaUrl" => $this->rutaURLArchivo . "/" . $this->nombre_archivo_zip,
        );

        if (file_exists($this->ruta_dir . "/" . $this->nombre_archivo_zip)) {

            Redireccionador::redireccionar('archivoGenerado', $arreglo);
        } else {

            Redireccionador::redireccionar('archivoNoGenerado');
        }

    }

    public function crearHojaCalculo() {
        include_once "crearDocumentoHojaCalculo.php";

    }

}

$miProcesador = new GenerarReporteInstalaciones($this->sql);

?>

