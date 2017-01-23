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
    public $ruta_directorio = '';

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $_REQUEST['tiempo'] = time();

        $conexion = "interoperacion";

        //$conexion = "produccion";

        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        switch ($_REQUEST['tipo_resultado']) {
            case '1':

                $this->estruturarProyectos();
                $this->crearHojaCalculo();
                break;

            case '2':
                $this->generarProceso();
                break;

            case '3':

                $this->consultarProceso();

                break;

        }

    }

    public function consultarProceso() {

        $cadenaSql = $this->miSql->getCadenaSql('consultarProcesoParticular');
        $this->proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

        if ($this->proceso) {

            ini_set('memory_limit', '2048M');
            ini_set('max_execution_time', 1000000);

            // Eliminar Tarea Crontab

            $this->eliminarTrabajoCrontab();

            // Cambiar Estado Proceso
            $cadenaSql = $this->miSql->getCadenaSql('actualizarProcesoParticularEstado', $this->proceso['id_proceso']);
            $estadoproceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

            $parametros = json_decode(base64_decode($this->proceso['parametros']), true);

            $_REQUEST = array_merge($_REQUEST, $parametros);

            $this->estruturarProyectos();

            $this->crearDirectorio();

            // Url Proceso
            $this->crearUrlProceso();

            // Crear Tarea Crontab

            $this->crearTrabajosCrontab();
            exit;

        } else {
            exit;
        }
    }

    /**
     * Metodos Correspondientes al Trabajos del Crontab
     **/
    public function crearTrabajosCrontab() {

        exec('echo -e "`crontab -l`\n*/5 * * * * ' . $this->Url_ejecucion . '" | crontab -', $variable);
        //exec('echo  "`crontab -l`\n* * * * * ' . $this->Url_ejecucion . '" | crontab -', $variable);

    }

    public function eliminarTrabajoCrontab() {

        exec('crontab -l', $crontab);

        shell_exec('echo "" | crontab -');

        if (!empty($crontab) && is_array($crontab)) {

            $cadena_buscada = '#Accesos';

            foreach ($crontab as $key => $value) {

                $posicion_coincidencia = strpos($value, $cadena_buscada);
                if ($posicion_coincidencia === false) {

                } else {

                    unset($crontab[$key]);
                }
            }

            foreach ($crontab as $key => $value) {

                $valor = ($value == '') ? '' : '`crontab -l`\n';

                exec('echo -e "' . $valor . $value . '" | crontab -');
                //exec('echo  "' . $valor . $value . '" | crontab -');
            }

        }

    }

    public function crearUrlProceso() {

        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        {

            $url = $this->miConfigurador->getVariableConfiguracion("host");
            $url .= $this->miConfigurador->getVariableConfiguracion("site");
            $url .= "/index.php?";

            $valorCodificado = "pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
            $valorCodificado .= "&action=" . $this->miConfigurador->getVariableConfiguracion('pagina');
            $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
            $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
            $valorCodificado .= "&opcion=generarReporte";
            $valorCodificado .= "&tipo_resultado=3";

        }

        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valorCodificado, $enlace);

        $this->UrlProceso = $url . $cadena;

        $this->Url_ejecucion = "curl  " . $this->UrlProceso . "  #Accesos";

    }

    public function generarProceso() {

        $arreglo = array(
            'departamento' => $_REQUEST['departamento'],
            'municipio' => $_REQUEST['municipio'],
            'urbanizacion' => $_REQUEST['urbanizacion'],
            'estado_beneficiario' => $_REQUEST['estado_beneficiario'],
            'beneficiario' => $_REQUEST['beneficiario'],
            'estado_documento' => $_REQUEST['estado_documento'],
        );

        $cadenaSql = $this->miSql->getCadenaSql('consultaGeneralInformacion');
        $this->Informacion = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        if ($this->Informacion == false) {

            Redireccionador::redireccionar('SinResultado');
        }

        $descripcion = '';
        foreach ($this->Informacion as $key => $value) {

            $descripcion .= ($key + 1) . ". " . $value['departamento'] . ", " . $value['municipio'] . ", " . trim(str_replace("URBANIZACION", "", $value['urbanizacion'])) . "<br>";

        }

        $arreglo = array(
            'parametros' => base64_encode(json_encode($arreglo)),
            'descripcion' => $descripcion,
        );

        $cadenaSql = $this->miSql->getCadenaSql('crearProceso', $arreglo);

        $proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0][0];

        if ($proceso) {

            Redireccionador::redireccionar('exitoProceso', $proceso);
        } else {

            Redireccionador::redireccionar('errorProceso');
        }

    }

    public function actualizarAvance($avance) {

        $arreglo = array(
            'avance' => $avance,
            'proceso' => $this->proceso['id_proceso'],
        );

        $cadenaSql = $this->miSql->getCadenaSql('actualizarProcesoParticularAvance', $arreglo);

        $avanceproceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

        return $avanceproceso;
    }

    public function eliminarInformacion() {

        switch ($_REQUEST['estado_documento']) {
            case '1':

                foreach ($this->beneficiarios as $key => $value) {

                    if (is_null($value['nombre_documento_contrato'])) {

                        $cadenaSql = $this->miSql->getCadenaSql('consultaDocumentosBeneficiarios', $value['id_beneficiario']);
                        $documentos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                        if (!$documentos) {
                            unset($this->beneficiarios[$key]);
                        }

                    }

                }

                break;

            case '0':

                foreach ($this->beneficiarios as $key => $value) {

                    if (!is_null($value['nombre_documento_contrato'])) {

                        $cadenaSql = $this->miSql->getCadenaSql('consultaDocumentosBeneficiarios', $value['id_beneficiario']);
                        $documentos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                        if ($documentos) {

                            unset($this->beneficiarios[$key]);
                        }

                    }

                }
                break;
        }

    }

    public function estruturarProyectos() {
        ini_set('xdebug.var_display_max_depth', 5);
        ini_set('xdebug.var_display_max_children', 256);
        ini_set('xdebug.var_display_max_data', 1024);
        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionBeneficiario');
        $this->beneficiarios = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        if ($this->beneficiarios == false) {

            Redireccionador::redireccionar('SinResultado');
        }

        //Eliminar Informacion

        $this->eliminarInformacion();

        //Actualizar Avance Progreso
        $this->actualizarAvance(10);

        if (isset($_REQUEST['estado_beneficiario'])) {

            switch ($_REQUEST['estado_beneficiario']) {

                case '2':

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

        //Actualizar Avance Progreso
        $this->actualizarAvance(25);

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

            } elseif (is_dir($url)) {

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

            if (!is_dir($directorio_beneficiario)) {
                mkdir($directorio_beneficiario, 0777);
                chmod($directorio_beneficiario, 0777);
            }

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

    public function comprimir($rutaObjetivoContenido, $nombreComprimido, $nombreDirectorioComprimir, $rutaSalidaComprimido = '') {

        $ruta_actual = getcwd();
        chdir($rutaObjetivoContenido);
        $nombre_archivo = time() . ".zip";
        $cadena = "zip -r " . $rutaSalidaComprimido . $nombre_archivo . " " . $nombreDirectorioComprimir . "/*";
        $queries = exec($cadena);
        chdir($ruta_actual);

        return $nombre_archivo;

    }

    public function eliminarDirectorioContenido($rutaAnalizar) {
        foreach (glob($rutaAnalizar . "/*") as $archivos_carpeta) {
            if (is_dir($archivos_carpeta)) {

                $valorContenido = @scandir($archivos_carpeta);

                if (count($valorContenido) == 2) {

                    rmdir($archivos_carpeta);
                } else {

                    $this->eliminarDirectorioContenido($archivos_carpeta);
                }
            } else {
                unlink($archivos_carpeta);
            }
        }
        rmdir($rutaAnalizar);
    }
    public function crearDirectorio() {

        /**
         * 1. Crear Directorio
         **/

        $this->directorio_archivos = $this->rutaAbsoluta . "/archivos/";

        $this->rutaURLArchivo = $this->rutaURL . "/archivos/archivosDescargaAccesos";
        $this->ruta_directorio_raiz = $this->rutaAbsoluta . "/archivos/archivosDescargaAccesos";

        $this->nombre_directorio = "paqueteAccesos" . time();
        $this->ruta_directorio = $this->ruta_directorio_raiz . "/" . $this->nombre_directorio;

        mkdir($this->ruta_directorio, 0777, true);
        chmod($this->ruta_directorio, 0777);

        $this->nombre_dir = "Accesos";
        $this->ruta_dir_archivos = $this->ruta_directorio . "/" . $this->nombre_dir;

        mkdir($this->ruta_dir_archivos, 0777, true);
        chmod($this->ruta_dir_archivos, 0777);

        /**
         * 2. Crear Documento Hoja de Calculo(Reporte)
         **/

        $this->crearHojaCalculo();

        //Actualizar Avance Progreso
        $this->actualizarAvance(50);

        /**
         * 3. Crear Directorios Archivos Beneficiarios
         **/

        $this->crearDirectorioArchivosBeneficiarios();

        //Actualizar Avance Progreso
        $this->actualizarAvance(60);

        /**
         * 3. Comprimir Directorio
         **/
        $this->nombre_archivo_zip = $this->comprimir($this->ruta_directorio_raiz, $this->nombre_directorio, $this->nombre_directorio);

        /**
         * 4. Eliminar Archivos No Necesarios
         **/
        $this->eliminarDirectorioContenido($this->ruta_directorio);

        //Actualizar Avance Progreso
        $this->actualizarAvance(98);

        /**
         * 4. Registrar Finalizacion Proceso
         **/

        exec('ls -lh ' . $this->ruta_directorio_raiz, $lista);

        foreach ($lista as $key => $value) {

            $posicion_coincidencia = strrpos($value, $this->nombre_archivo_zip);

            if ($posicion_coincidencia === false) {

            } else {

                $variable = explode(" ", $value);

                $tamanio_archivo = $variable[count($variable) - 5];
            }
        }

        $arreglo = array(
            "nombre_archivo" => $this->nombre_archivo_zip,
            "rutaUrl" => $this->rutaURLArchivo . "/" . $this->nombre_archivo_zip,
            "proceso" => $this->proceso['id_proceso'],
            "tamanio_archivo" => $tamanio_archivo,
        );

        $cadenaSql = $this->miSql->getCadenaSql('finalizarProceso', $arreglo);
        $finalizacionproceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

    }

    public function crearHojaCalculo() {
        include_once "crearDocumentoHojaCalculo.php";

    }

}

$miProcesador = new GenerarReporteInstalaciones($this->sql);

?>

