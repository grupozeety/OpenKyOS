<?php
namespace reportes\instalacionesGenerales\entidad;

class GenerarReporteInstalaciones {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $proyectos;

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->proyectos = json_decode(base64_decode($_REQUEST['info_proyectos']), true);

        $_REQUEST['tiempo'] = time();

        /**
         * 1. Filtrar Proyectos a Reportear
         **/
        $this->filtrarProyectos();

        /**
         * 2. Obtener Paquetes de Trabajo
         **/
        $this->obtenerPaquetesTrabajo();

        /**
         * 3. Obtener Actividades Paquetes de Trabajo
         **/
        $this->obtenerActividades();

        /**
         * 4. Filtrar Actividades Paquetes de Trabajo
         **/
        $this->filtrarActividades();

        /**
         * 5. Crear Documento Hoja de Calculo(Reporte)
         **/
        $this->crearHojaCalculo();

    }

    public function crearHojaCalculo() {
        include_once "crearDocumentoHojaCalculo.php";

    }
    public function filtrarActividades() {

        foreach ($this->proyectos as $key => $value) {

            foreach ($value['paquetesTrabajo'] as $llave => $valor) {

                if ($valor['type_id'] == 2) {

                    foreach ($valor['actividades'] as $llave2 => $actividad) {

                        if ($actividad['_type'] != 'Activity::Comment') {
                            unset($this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][$llave2]);

                        }
                        $fecha_actividad = substr($actividad['createdAt'], 0, 10);
                        $fecha_actividad = strtotime($fecha_actividad);
                        $fecha_inicio = strtotime($_REQUEST['fecha_inicio']);
                        $fecha_final = strtotime($_REQUEST['fecha_final']);

                        if ($fecha_actividad < $fecha_inicio) {
                            unset($this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][$llave2]);
                        }

                        if ($fecha_actividad > $fecha_final) {
                            unset($this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][$llave2]);
                        }

                    }

                    //var_dump($this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades']);exit();
                }

            }

        }

    }
    public function obtenerActividades() {

        foreach ($this->proyectos as $key => $value) {

            foreach ($value['paquetesTrabajo'] as $llave => $valor) {

                $urlActividades = $this->crearUrlActividades($valor['id']);

                $actividades = file_get_contents($urlActividades);

                $actividad = json_decode($actividades, true);

                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'] = $actividad;

            }

        }

        //var_dump($this->proyectos[0]['paquetesTrabajo'][2]['actividades']);exit;

    }

    public function crearUrlActividades($var = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=detalleActividadesPaquetesTrabajo";
        $variable .= "&id_paquete_trabajo=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }

    public function obtenerPaquetesTrabajo() {

        foreach ($this->proyectos as $key => $value) {

            $urlPaquetes = $this->crearUrlPaquetesTrabajo($value['id']);

            $paquetesTrabajo = file_get_contents($urlPaquetes);

            $paquetesTrabajo = json_decode($paquetesTrabajo, true);

            $this->proyectos[$key]['paquetesTrabajo'] = $paquetesTrabajo;

        }

    }

    public function crearUrlPaquetesTrabajo($var = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=paquetesTrabajo";
        $variable .= "&id_proyecto=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }
    public function filtrarProyectos() {

        $cantidadProyectos = count($this->proyectos);

        for ($i = 1; $i < $cantidadProyectos; $i++) {

            if (isset($_REQUEST['item' . $i])) {

                $ident_proyectos[] = $_REQUEST['item' . $i];

            }

        }

        if (isset($ident_proyectos)) {

            foreach ($this->proyectos as $key => $value) {

                foreach ($ident_proyectos as $valor) {

                    if ($value['id'] == $valor) {

                        $proyectos[] = $value;

                    }

                }

            }

            $this->proyectos = $proyectos;

        }

    }

}

$miProcesador = new GenerarReporteInstalaciones($this->sql);

?>

