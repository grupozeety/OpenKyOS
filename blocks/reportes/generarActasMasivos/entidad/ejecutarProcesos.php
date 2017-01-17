<?php
namespace reportes\generarActasMasivos\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

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
    public function __construct($sql) {

        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 10000);
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/archivos/generacionMasiva/";
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento") . "/archivos/generacionMasiva/";

        $this->rutaAbsolutaRaiz = $this->miConfigurador->getVariableConfiguracion("raizDocumento") . "/archivos/generacionMasiva/";

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        /**
         *  0. Crear Url Procesos
         **/

        $this->crearUrlProcesos();

        /**
         *  1. Consultar Proceso
         **/

        $this->consultarProceso();

        /**
         *  2. Cambiar Estado Proceso
         **/

        $this->actualizarEstadoProceso();

        /**
         *  3. Creacion Directorio
         **/

        $this->crearDirectorio();

        /**
         *  4. Creación Documentos
         **/

        $this->creacionDocumentos();

        /**
         *  5. Generar Comprimido
         **/

        $this->generarComprimido();

        /**
         *  6. Limpiar Directorio
         **/

        $this->limpiarDirectorio();

        /**
         *  6. Regitrar Comprimido
         **/

        $this->registrarComprimido();

        /**
         *  7. Validar Existencia Beneficiarios
         **/

        $this->crearTrabajosCrontab();

    }

    public function registrarComprimido() {

        exec('ls -lh ' . $this->rutaAbsolutaRaiz, $lista);

        foreach ($lista as $key => $value) {

            $posicion_coincidencia = strrpos($value, $this->nombre_archivo_zip);

            if ($posicion_coincidencia === false) {

            } else {

                $variable = explode(" ", $value);

                $tamanio_archivo = $variable[count($variable) - 5];
            }
        }

        $arreglo = array(
            'id_proceso' => $this->proceso[0],
            'ruta_archivo' => $this->ruta_url_archivo,
            'nombre_archivo' => $this->nombre_archivo_zip,
            'tamanio_archivo' => $tamanio_archivo,
        );

        $cadenaSql = $this->miSql->getCadenaSql('finalizarProceso', $arreglo);

        $this->finalizacion_proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

    }

    public function limpiarDirectorio() {

        $this->eliminarDirectorioContenido($this->rutaAbsoluta_archivos);

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

    public function generarComprimido() {

        $this->nombre_archivo_zip = $this->comprimir($this->rutaAbsoluta, "Proceso_" . $this->proceso['id_proceso'], "Proceso_" . $this->proceso['id_proceso']);

        $this->ruta_url_archivo = $this->rutaURL . $this->nombre_archivo_zip;

    }

    public function comprimir($rutaObjetivoContenido, $nombreComprimido, $nombreDirectorioComprimir, $rutaSalidaComprimido = '') {

        $ruta_actual = getcwd();

        chdir($rutaObjetivoContenido);

        $nombre_archivo = $nombreComprimido . "_" . time() . ".zip";

        $cadena = "zip " . $rutaSalidaComprimido . $nombre_archivo . " " . $nombreDirectorioComprimir . "/*";

        $queries = exec($cadena);

        chdir($ruta_actual);

        return $nombre_archivo;

    }

    public function creacionDocumentos() {

        switch ($this->proceso['descripcion']) {

            case 'Actas':
                include_once "generacionActas.php";
                break;

        }

    }

    public function crearDirectorio() {

        $this->rutaURL_archivos = $this->rutaURL . "Proceso_" . $this->proceso['id_proceso'];
        $this->rutaAbsoluta_archivos = $this->rutaAbsoluta . "Proceso_" . $this->proceso['id_proceso'];

        if (!file_exists($this->rutaAbsoluta_archivos)) {

            mkdir($this->rutaAbsoluta_archivos, 0777, true);
            chmod($this->rutaAbsoluta_archivos, 0777);
        }
    }

    public function actualizarEstadoProceso() {

        $cadenaSql = $this->miSql->getCadenaSql('actualizarProceso', $this->proceso['id_proceso']);
        $actualizacion = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

    }

    public function consultarProceso() {

        $cadenaSql = $this->miSql->getCadenaSql('consultarProcesoParticular');
        $this->proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

        if (!is_null($this->proceso)) {

            $this->eliminarTrabajoCrontab();
        } else {
            exit;
        }

    }
    /**
     * Metodos Correspondientes al Trabajos del Crontab
     **/
    public function crearTrabajosCrontab() {

        exec('echo -e "`crontab -l`\n* * * * * ' . $this->Url_ejecucion . '" | crontab -', $variable);
        //exec('echo "`crontab -l`\n* * * * * ' . $this->Url_ejecucion . '" | crontab -', $variable);

    }

    public function eliminarTrabajoCrontab() {

        exec('crontab -l', $crontab);

        shell_exec('echo "" | crontab -');

        if (!empty($crontab) && is_array($crontab)) {

            $cadena_buscada = '#Actas';

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

    public function crearUrlProcesos() {

        $esteBloque = $this->miConfigurador->configuracion['esteBloque'];

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";

        // Variables para Con
        $cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
        $cadenaACodificar .= "&procesarAjax=true";
        $cadenaACodificar .= "&action=index.php";
        $cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
        $cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
        $cadenaACodificar .= "&funcion=ejecutarProcesos";

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

        // URL Consultar Proyectos
        $this->UrlProceso = $url . $cadena;

        $this->Url_ejecucion = "curl  " . $this->UrlProceso . "  #Actas ";

    }

}

$miProcesador = new FormProcessor($this->sql);
?>

